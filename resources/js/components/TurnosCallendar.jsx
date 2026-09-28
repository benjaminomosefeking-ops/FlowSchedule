import React, {
    useState,
    useEffect,
    useRef,
    useMemo,
    useCallback,
} from "react";
import { createPortal } from "react-dom";
import {
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
    Pencil,
    Eraser,
    X,
    Trash2,
    CalendarDays,
    Check,
    Loader2,
} from "lucide-react";

// ---------- helpers ----------

const WEEKDAYS = ["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];

const COLORS = [
    { name: "Rojo", value: "#C4432B" },
    { name: "Naranja", value: "#D98B3F" },
    { name: "Amarillo", value: "#D9B342" },
    { name: "Verde", value: "#5B8C5A" },
    { name: "Azul", value: "#3B6EA5" },
    { name: "Morado", value: "#7A5C9E" },
    { name: "Gris", value: "#8A8680" },
];

const toKey = (d) =>
    `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}`;

const isSameDate = (a, b) =>
    a.getFullYear() === b.getFullYear() &&
    a.getMonth() === b.getMonth() &&
    a.getDate() === b.getDate();

const getWeekNumber = (date) => {
    const d = new Date(
        Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()),
    );
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    return Math.ceil(((d - yearStart) / 86400000 + 1) / 7);
};

const formatLongDate = (d) =>
    new Intl.DateTimeFormat("es", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    }).format(d);

// Cabeceras comunes para las peticiones (el CSRF solo se envía si existe)
const buildHeaders = (withBody) => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    return {
        Accept: "application/json",
        ...(withBody ? { "Content-Type": "application/json" } : {}),
        ...(csrf ? { "X-CSRF-TOKEN": csrf } : {}),
    };
};

// ---------- drawing canvas ----------

function DrawPad({ value, onChange }) {
    const canvasRef = useRef(null);
    const drawingRef = useRef(false);
    const [tool, setTool] = useState("pencil");
    const toolRef = useRef("pencil");
    toolRef.current = tool;

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext("2d");
        ctx.fillStyle = "#FFFFFF";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        if (value) {
            const img = new Image();
            img.onload = () =>
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            img.src = value;
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const getPos = (e) => {
        const canvas = canvasRef.current;
        const rect = canvas.getBoundingClientRect();
        const cx = e.touches ? e.touches[0].clientX : e.clientX;
        const cy = e.touches ? e.touches[0].clientY : e.clientY;
        let x = (cx - rect.left) * (canvas.width / rect.width);
        let y = (cy - rect.top) * (canvas.height / rect.height);
        x = Math.max(0, Math.min(canvas.width, x));
        y = Math.max(0, Math.min(canvas.height, y));
        return { x, y };
    };

    const start = (e) => {
        e.preventDefault();
        const canvas = canvasRef.current;
        if (canvas.setPointerCapture && e.pointerId !== undefined) {
            try {
                canvas.setPointerCapture(e.pointerId);
            } catch (_) {}
        }
        drawingRef.current = true;
        const ctx = canvas.getContext("2d");
        const { x, y } = getPos(e);
        ctx.beginPath();
        ctx.moveTo(x, y);
    };

    const move = (e) => {
        if (!drawingRef.current) return;
        e.preventDefault();
        const canvas = canvasRef.current;
        const ctx = canvas.getContext("2d");
        const { x, y } = getPos(e);
        ctx.lineCap = "round";
        ctx.lineJoin = "round";
        ctx.globalCompositeOperation = "source-over";
        if (toolRef.current === "eraser") {
            // Pinta en blanco (antes borraba a transparente y el PNG guardado quedaba con huecos)
            ctx.strokeStyle = "#FFFFFF";
            ctx.lineWidth = 24;
        } else {
            ctx.strokeStyle = "#1A1A1A";
            ctx.lineWidth = 3;
        }
        ctx.lineTo(x, y);
        ctx.stroke();
    };

    const end = () => {
        if (!drawingRef.current) return;
        drawingRef.current = false;
        const canvas = canvasRef.current;
        onChange(canvas.toDataURL("image/png"));
    };

    const clear = () => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext("2d");
        ctx.globalCompositeOperation = "source-over";
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = "#FFFFFF";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        onChange(null);
    };

    return (
        <div>
            <div style={{ display: "flex", gap: "8px", marginBottom: "8px" }}>
                <button
                    onClick={() => setTool("pencil")}
                    style={toolBtnStyle(tool === "pencil")}
                    type="button"
                    onMouseEnter={(e) => {
                        e.currentTarget.style.transform = "scale(1.02)";
                        e.currentTarget.style.background = "var(--ink)";
                        e.currentTarget.style.color = "var(--paper)";
                    }}
                    onMouseLeave={(e) => {
                        e.currentTarget.style.transform = "scale(1)";
                        e.currentTarget.style.background =
                            tool === "pencil" ? "var(--ink)" : "var(--paper)";
                        e.currentTarget.style.color =
                            tool === "pencil" ? "var(--paper)" : "var(--ink)";
                    }}
                    onMouseDown={(e) => {
                        e.currentTarget.style.transform = "scale(0.97)";
                    }}
                    onMouseUp={(e) => {
                        e.currentTarget.style.transform = "scale(1.02)";
                    }}
                >
                    <Pencil size={14} /> Lápiz
                </button>
                <button
                    onClick={() => setTool("eraser")}
                    style={toolBtnStyle(tool === "eraser")}
                    type="button"
                    onMouseEnter={(e) => {
                        e.currentTarget.style.transform = "scale(1.02)";
                        e.currentTarget.style.background = "var(--ink)";
                        e.currentTarget.style.color = "var(--paper)";
                    }}
                    onMouseLeave={(e) => {
                        e.currentTarget.style.transform = "scale(1)";
                        e.currentTarget.style.background =
                            tool === "eraser" ? "var(--ink)" : "var(--paper)";
                        e.currentTarget.style.color =
                            tool === "eraser" ? "var(--paper)" : "var(--ink)";
                    }}
                    onMouseDown={(e) => {
                        e.currentTarget.style.transform = "scale(0.97)";
                    }}
                    onMouseUp={(e) => {
                        e.currentTarget.style.transform = "scale(1.02)";
                    }}
                >
                    <Eraser size={14} /> Goma
                </button>
                <button
                    onClick={clear}
                    style={{
                        ...toolBtnStyle(false),
                        marginLeft: "auto",
                        color: "#B23A3A",
                    }}
                    onMouseEnter={(e) => {
                        e.currentTarget.style.transform = "scale(1.02)";
                        e.currentTarget.style.background = "#B23A3A";
                        e.currentTarget.style.color = "white";
                    }}
                    onMouseLeave={(e) => {
                        e.currentTarget.style.transform = "scale(1)";
                        e.currentTarget.style.background = "transparent";
                        e.currentTarget.style.color = "#B23A3A";
                    }}
                    onMouseDown={(e) => {
                        e.currentTarget.style.transform = "scale(0.97)";
                    }}
                    onMouseUp={(e) => {
                        e.currentTarget.style.transform = "scale(1.02)";
                    }}
                    type="button"
                >
                    <Trash2 size={14} /> Borrar
                </button>
            </div>
            <div
                style={{
                    border: "2px solid var(--ink)",
                    background: "#fff",
                    touchAction: "none",
                    width: "100%",
                    overflow: "hidden",
                    lineHeight: 0,
                    boxShadow: "0 2px 4px rgba(0,0,0,0.05)",
                    transition: "box-shadow 0.2s ease",
                }}
                onMouseEnter={(e) => {
                    e.currentTarget.style.boxShadow =
                        "0 4px 8px rgba(0,0,0,0.1)";
                }}
                onMouseLeave={(e) => {
                    e.currentTarget.style.boxShadow =
                        "0 2px 4px rgba(0,0,0,0.05)";
                }}
            >
                <canvas
                    ref={canvasRef}
                    width={520}
                    height={220}
                    style={{
                        width: "100%",
                        height: "180px",
                        display: "block",
                        touchAction: "none",
                        cursor: tool === "eraser" ? "cell" : "crosshair",
                    }}
                    onPointerDown={start}
                    onPointerMove={move}
                    onPointerUp={end}
                    onPointerCancel={end}
                    onPointerLeave={end}
                />
            </div>
            <p
                style={{
                    fontSize: "0.72rem",
                    color: "var(--pencil)",
                    marginTop: "6px",
                }}
            >
                Dibuja solo dentro de este recuadro. El trazo nunca sale de
                aquí.
            </p>
        </div>
    );
}

const toolBtnStyle = (active) => ({
    display: "flex",
    alignItems: "center",
    gap: "6px",
    padding: "6px 10px",
    fontSize: "0.72rem",
    fontWeight: 700,
    border: "2px solid var(--ink)",
    background: active ? "var(--ink)" : "var(--paper)",
    color: active ? "var(--paper)" : "var(--ink)",
    cursor: "pointer",
    transition: "all 0.16s ease",
});

// ---------- estilos compartidos ----------

const labelStyle = {
    display: "block",
    fontSize: "0.72rem",
    fontWeight: 700,
    marginBottom: "6px",
    color: "var(--ink)",
};

const inputStyle = {
    width: "100%",
    padding: "8px 10px",
    border: "2px solid var(--ink)",
    background: "var(--paper)",
    color: "var(--ink)",
    fontFamily: "var(--font-sans)",
    fontSize: "0.72rem",
    borderRadius: "0",
    boxSizing: "border-box",
};

const btnStyle = {
    padding: "8px 14px",
    background: "var(--paper)",
    color: "var(--ink)",
    border: "2px solid var(--ink)",
    fontFamily: "var(--font-display)",
    fontSize: "0.72rem",
    fontWeight: 700,
    textTransform: "uppercase",
    cursor: "pointer",
    boxShadow: "2px 2px 0 var(--ink)",
    display: "inline-flex",
    alignItems: "center",
};

const navBtnStyle = {
    padding: "8px 10px",
    background: "var(--paper)",
    color: "var(--ink)",
    border: "none",
    cursor: "pointer",
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
};

// ---------- day editor modal ----------

function DayModal({ date, entry, onChange, onClear, onClose }) {
    const [title, setTitle] = useState(entry?.title || "");
    const [note, setNote] = useState(entry?.note || "");
    const [color, setColor] = useState(entry?.color || null);
    const [drawing, setDrawing] = useState(entry?.drawing || null);
    const [visible, setVisible] = useState(false);

    // Refs para no depender de closures obsoletas
    const draftRef = useRef({ title, note, color, drawing });
    const dirtyRef = useRef(false);
    const timerRef = useRef(null);
    const onChangeRef = useRef(onChange);
    const onCloseRef = useRef(onClose);
    onChangeRef.current = onChange;
    onCloseRef.current = onClose;

    // Guarda lo pendiente (si hay cambios)
    const flush = useCallback(() => {
        clearTimeout(timerRef.current);
        if (!dirtyRef.current) return;
        dirtyRef.current = false;
        onChangeRef.current(draftRef.current);
    }, []);

    // Actualiza el borrador; guarda con debounce o al instante
    const update = (patch, immediate = false) => {
        draftRef.current = { ...draftRef.current, ...patch };
        dirtyRef.current = true;
        clearTimeout(timerRef.current);
        if (immediate) flush();
        else timerRef.current = setTimeout(flush, 600);
    };

    // Cierra SIEMPRE guardando lo pendiente
    const handleClose = useCallback(() => {
        flush();
        onCloseRef.current();
    }, [flush]);

    // Red de seguridad: si se desmonta con cambios pendientes, guardar
    useEffect(() => () => flush(), [flush]);

    // Bloquea el scroll del body + Escape + animación de entrada
    useEffect(() => {
        const prevOverflow = document.body.style.overflow;
        document.body.style.overflow = "hidden";

        const raf = requestAnimationFrame(() => setVisible(true));

        const onKey = (e) => {
            if (e.key === "Escape") handleClose();
        };
        window.addEventListener("keydown", onKey);

        return () => {
            cancelAnimationFrame(raf);
            document.body.style.overflow = prevOverflow;
            window.removeEventListener("keydown", onKey);
        };
    }, [handleClose]);

    const modal = (
        <div
            onClick={handleClose}
            style={{
                position: "fixed",
                inset: 0,
                background: "rgba(26,26,26,0.55)",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                zIndex: 9999,
                padding: "16px",
                opacity: visible ? 1 : 0,
                transition: "opacity 200ms cubic-bezier(0.23,1,0.32,1)",
            }}
        >
            <div
                onClick={(e) => e.stopPropagation()}
                style={{
                    background: "var(--paper)",
                    border: "3px solid var(--ink)",
                    boxShadow: "10px 10px 0 var(--ink)",
                    width: "100%",
                    maxWidth: "460px",
                    maxHeight: "90vh",
                    overflowY: "auto",
                    padding: "24px",
                    transform: visible ? "scale(1)" : "scale(0.96)",
                    transition:
                        "transform 200ms cubic-bezier(0.23,1,0.32,1), box-shadow 200ms ease",
                }}
                onMouseEnter={(e) => {
                    e.currentTarget.style.boxShadow = "12px 12px 0 var(--ink)";
                }}
                onMouseLeave={(e) => {
                    e.currentTarget.style.boxShadow = "10px 10px 0 var(--ink)";
                }}
            >
                <div
                    style={{
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "flex-start",
                        marginBottom: "18px",
                    }}
                >
                    <h2
                        style={{
                            fontFamily: "var(--font-display)",
                            fontSize: "1.3rem",
                            margin: 0,
                            textTransform: "capitalize",
                        }}
                    >
                        {formatLongDate(date)}
                    </h2>
                    <button
                        type="button"
                        onClick={handleClose}
                        style={{
                            background: "none",
                            border: "none",
                            cursor: "pointer",
                            color: "var(--ink)",
                        }}
                    >
                        <X size={22} />
                    </button>
                </div>

                <label style={labelStyle}>Nombre del calendario</label>
                <input
                    value={title}
                    maxLength={60}
                    onChange={(e) => {
                        setTitle(e.target.value);
                        update({ title: e.target.value });
                    }}
                    onBlur={(e) => {
                        flush();
                        e.target.style.borderColor = "var(--ink)";
                        e.target.style.boxShadow = "none";
                    }}
                    placeholder="Ej. Dia de descanso"
                    style={inputStyle}
                    onFocus={(e) => {
                        e.target.style.borderColor = "var(--blue)";
                        e.target.style.boxShadow =
                            "0 0 0 2px rgba(59,110,165,0.2)";
                    }}
                />

                <div
                    style={{
                        display: "flex",
                        justifyContent: "space-between",
                        marginTop: "14px",
                    }}
                >
                    <label style={labelStyle}>Nota</label>
                    <span
                        style={{ fontSize: "0.7rem", color: "var(--pencil)" }}
                    >
                        {note.length}/200
                    </span>
                </div>
                <textarea
                    value={note}
                    maxLength={200}
                    onChange={(e) => {
                        setNote(e.target.value);
                        update({ note: e.target.value });
                    }}
                    onBlur={(e) => {
                        flush();
                        e.target.style.borderColor = "var(--ink)";
                        e.target.style.boxShadow = "none";
                    }}
                    rows={3}
                    placeholder="Escribe hasta 200 caracteres…"
                    style={{
                        ...inputStyle,
                        resize: "vertical",
                        fontFamily: "var(--font-sans)",
                    }}
                    onFocus={(e) => {
                        e.target.style.borderColor = "var(--blue)";
                        e.target.style.boxShadow =
                            "0 0 0 2px rgba(59,110,165,0.2)";
                    }}
                />

                <label
                    style={{
                        ...labelStyle,
                        marginTop: "14px",
                        display: "block",
                    }}
                >
                    Color del día
                </label>
                <div
                    style={{
                        display: "flex",
                        flexWrap: "wrap",
                        gap: "8px",
                        alignItems: "center",
                        marginBottom: "18px",
                    }}
                >
                    <button
                        type="button"
                        onClick={() => {
                            setColor(null);
                            update({ color: null }, true);
                        }}
                        title="Sin color"
                        style={{
                            width: "28px",
                            height: "28px",
                            borderRadius: "50%",
                            border: `2px solid ${!color ? "var(--ink)" : "#ccc"}`,
                            background:
                                "repeating-linear-gradient(45deg,#fff,#fff 3px,#eee 3px,#eee 6px)",
                            cursor: "pointer",
                            boxShadow: !color
                                ? "0 0 0 2px var(--paper), 0 0 0 3px var(--ink)"
                                : "none",
                            transition: "all 0.16s ease",
                        }}
                        onMouseEnter={(e) => {
                            e.currentTarget.style.transform = "scale(1.05)";
                            e.currentTarget.style.boxShadow = !color
                                ? "0 0 0 3px var(--paper), 0 0 0 4px var(--ink)"
                                : "none";
                        }}
                        onMouseLeave={(e) => {
                            e.currentTarget.style.transform = "scale(1)";
                            e.currentTarget.style.boxShadow = !color
                                ? "0 0 0 2px var(--paper), 0 0 0 3px var(--ink)"
                                : "none";
                        }}
                    />
                    {COLORS.map((c) => (
                        <button
                            type="button"
                            key={c.value}
                            onClick={() => {
                                setColor(c.value);
                                update({ color: c.value }, true);
                            }}
                            title={c.name}
                            style={{
                                width: "28px",
                                height: "28px",
                                borderRadius: "50%",
                                background: c.value,
                                border: "2px solid var(--ink)",
                                cursor: "pointer",
                                boxShadow:
                                    color === c.value
                                        ? "0 0 0 2px var(--paper), 0 0 0 3px var(--ink)"
                                        : "none",
                                transition: "all 0.16s ease",
                            }}
                            onMouseEnter={(e) => {
                                e.currentTarget.style.transform = "scale(1.05)";
                                e.currentTarget.style.boxShadow =
                                    color === c.value
                                        ? "0 0 0 3px var(--paper), 0 0 0 4px var(--ink)"
                                        : "none";
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.transform = "scale(1)";
                                e.currentTarget.style.boxShadow =
                                    color === c.value
                                        ? "0 0 0 2px var(--paper), 0 0 0 3px var(--ink)"
                                        : "none";
                            }}
                        />
                    ))}
                    <label
                        title="Elegir cualquier color"
                        style={{
                            width: "28px",
                            height: "28px",
                            borderRadius: "50%",
                            border: "2px dashed var(--ink)",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            cursor: "pointer",
                            position: "relative",
                            overflow: "hidden",
                            fontSize: "14px",
                            color: "var(--ink)",
                        }}
                    >
                        +
                        <input
                            type="color"
                            value={color || "#3B6EA5"}
                            onChange={(e) => {
                                setColor(e.target.value);
                                // El selector dispara muchos eventos: usamos debounce
                                update({ color: e.target.value });
                            }}
                            style={{
                                position: "absolute",
                                inset: 0,
                                opacity: 0,
                                cursor: "pointer",
                            }}
                        />
                    </label>
                </div>

                <label
                    style={{
                        ...labelStyle,
                        display: "block",
                        marginBottom: "6px",
                    }}
                >
                    Dibujo
                </label>
                <DrawPad
                    value={drawing}
                    onChange={(d) => {
                        setDrawing(d);
                        update({ drawing: d }, true);
                    }}
                />

                <div
                    style={{ display: "flex", gap: "10px", marginTop: "20px" }}
                >
                    <button
                        type="button"
                        onClick={() => {
                            // Cancela cualquier guardado pendiente para que no recree la entrada
                            clearTimeout(timerRef.current);
                            dirtyRef.current = false;
                            onClear();
                            onCloseRef.current();
                        }}
                        style={{
                            ...btnStyle,
                            color: "#B23A3A",
                            display: "flex",
                            alignItems: "center",
                            gap: "6px",
                        }}
                        onMouseEnter={(e) => {
                            e.currentTarget.style.transform = "scale(1.03)";
                            e.currentTarget.style.background = "#e53e3e";
                            e.currentTarget.style.color = "white";
                        }}
                        onMouseLeave={(e) => {
                            e.currentTarget.style.transform = "scale(1)";
                            e.currentTarget.style.background = "var(--paper)";
                            e.currentTarget.style.color = "#B23A3A";
                        }}
                        onMouseDown={(e) => {
                            e.currentTarget.style.transform = "scale(0.95)";
                        }}
                        onMouseUp={(e) => {
                            e.currentTarget.style.transform = "scale(1.03)";
                        }}
                    >
                        <Trash2 size={14} /> Eliminar día
                    </button>
                    <button
                        type="button"
                        onClick={handleClose}
                        style={{ ...btnStyle, marginLeft: "auto" }}
                        onMouseEnter={(e) => {
                            e.currentTarget.style.transform = "scale(1.02)";
                            e.currentTarget.style.background = "var(--blue)";
                            e.currentTarget.style.color = "var(--paper)";
                        }}
                        onMouseLeave={(e) => {
                            e.currentTarget.style.transform = "scale(1)";
                            e.currentTarget.style.background = "var(--paper)";
                            e.currentTarget.style.color = "var(--ink)";
                        }}
                        onMouseDown={(e) => {
                            e.currentTarget.style.transform = "scale(0.96)";
                        }}
                        onMouseUp={(e) => {
                            e.currentTarget.style.transform = "scale(1.02)";
                        }}
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    );

    return createPortal(modal, document.body);
}

// ---------- main calendar ----------

export default function FullCalendar() {
    const today = useMemo(() => new Date(), []);
    const [viewDate, setViewDate] = useState(new Date());
    const [data, setData] = useState({});
    const [activeKey, setActiveKey] = useState(null);
    const [saveStatus, setSaveStatus] = useState("idle");

    // dataRef siempre tiene la versión más reciente (se actualiza de forma síncrona)
    const dataRef = useRef(data);

    // Carga inicial: fusiona sin pisar lo que se haya editado mientras tanto
    useEffect(() => {
        let mounted = true;
        (async () => {
            try {
                const res = await fetch("/calendar/events", {
                    headers: { Accept: "application/json" },
                });
                if (!res.ok)
                    throw new Error("No se pudieron cargar los eventos");
                const events = await res.json();
                if (mounted) {
                    // Laravel devuelve [] cuando está vacío
                    const safe = Array.isArray(events) ? {} : events;
                    const merged = { ...safe, ...dataRef.current };
                    dataRef.current = merged;
                    setData(merged);
                }
            } catch (_) {
                if (mounted) setSaveStatus("error");
            }
        })();
        return () => {
            mounted = false;
        };
    }, []);

    const persistEntry = useCallback(async (key, entry) => {
        setSaveStatus("saving");
        try {
            const res = await fetch(`/calendar/events/${key}`, {
                method: "PUT",
                headers: buildHeaders(true),
                body: JSON.stringify(entry),
            });
            if (!res.ok) throw new Error("No se pudo guardar el evento");
            setSaveStatus("saved");
        } catch (_) {
            setSaveStatus("error");
        }
    }, []);

    const year = viewDate.getFullYear();
    const month = viewDate.getMonth();
    const monthLabel = new Intl.DateTimeFormat("es", {
        month: "long",
        year: "numeric",
    }).format(viewDate);

    const weeks = useMemo(() => {
        const firstOfMonth = new Date(year, month, 1);
        const startOffset = (firstOfMonth.getDay() + 6) % 7; // Monday = 0
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const cells = [];
        for (let i = startOffset; i > 0; i--) {
            cells.push({ date: new Date(year, month, 1 - i), inMonth: false });
        }
        for (let d = 1; d <= daysInMonth; d++) {
            cells.push({ date: new Date(year, month, d), inMonth: true });
        }
        while (cells.length % 7 !== 0) {
            const prev = cells[cells.length - 1].date;
            cells.push({
                date: new Date(
                    prev.getFullYear(),
                    prev.getMonth(),
                    prev.getDate() + 1,
                ),
                inMonth: false,
            });
        }
        const out = [];
        for (let i = 0; i < cells.length; i += 7)
            out.push(cells.slice(i, i + 7));
        return out;
    }, [year, month]);

    const clearEntry = useCallback((key) => {
        const next = { ...dataRef.current };
        delete next[key];
        dataRef.current = next;
        setData(next);

        setSaveStatus("saving");
        fetch(`/calendar/events/${key}`, {
            method: "DELETE",
            headers: buildHeaders(false),
        })
            .then((res) => {
                if (!res.ok) throw new Error("No se pudo eliminar el evento");
                setSaveStatus("saved");
            })
            .catch(() => setSaveStatus("error"));
    }, []);

    const updateEntry = useCallback(
        (key, patch) => {
            const nextEntry = { ...dataRef.current[key], ...patch };
            const hasContent = [
                nextEntry.title,
                nextEntry.note,
                nextEntry.color,
                nextEntry.drawing,
            ].some((value) => Boolean(value));

            if (!hasContent) {
                clearEntry(key);
                return;
            }

            dataRef.current = { ...dataRef.current, [key]: nextEntry };
            setData(dataRef.current);
            persistEntry(key, nextEntry);
        },
        [clearEntry, persistEntry],
    );

    const activeDate = activeKey
        ? new Date(
              Number(activeKey.split("-")[0]),
              Number(activeKey.split("-")[1]) - 1,
              Number(activeKey.split("-")[2]),
          )
        : null;

    return (
        <div
            style={{
                "--ink": "#1A1D24",
                "--paper": "#F2EBDC",
                "--paper-edge": "#E5DCC4",
                "--pencil": "#6B6660",
                "--blue": "#1F3FA8",
                "--weekend": "#E5DCC4",
                "--font-display": "'IBM Plex Mono', ui-monospace, monospace",
                "--font-sans": "'Inter', system-ui, -apple-system, sans-serif",
                background: "var(--paper)",
                backgroundImage:
                    "linear-gradient(to right, rgba(26,29,36,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(26,29,36,0.07) 1px, transparent 1px)",
                backgroundSize: "28px 28px",
                color: "var(--ink)",
                minHeight: "100vh",
                width: "100%",
                boxSizing: "border-box",
                padding: "clamp(16px, 3vw, 40px)",
                fontFamily: "var(--font-sans)",
                position: "relative",
                overflowX: "hidden",
            }}
        >
            <style>{`
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap');
        * { box-sizing: border-box; }
        .cal-cell:hover { transform: translate(-1px,-1px); box-shadow: 5px 5px 0 var(--ink) !important; }

        @keyframes spin {
          from { transform: rotate(0deg); }
          to { transform: rotate(360deg); }
        }
        .spin { animation: spin 0.8s linear infinite; }

        /* iOS hace zoom automático al enfocar campos con menos de 16px */
        @media (max-width: 760px) {
          input, textarea { font-size: 16px !important; }
        }
      `}</style>

            {/* header */}
            <div
                className="no-print"
                style={{
                    display: "flex",
                    flexWrap: "wrap",
                    gap: "16px",
                    justifyContent: "space-between",
                    alignItems: "flex-end",
                    paddingBottom: "20px",
                    borderBottom: "2px solid var(--ink)",
                    marginBottom: "24px",
                    position: "relative",
                    zIndex: 10,
                }}
            >
                <div>
                    <div
                        style={{
                            display: "flex",
                            alignItems: "center",
                            gap: "8px",
                            color: "var(--pencil)",
                            fontSize: "0.75rem",
                            fontWeight: 700,
                            textTransform: "uppercase",
                            letterSpacing: "0.05em",
                            marginBottom: "4px",
                        }}
                    >
                        <CalendarDays size={14} /> Calendario
                    </div>
                    <h1
                        style={{
                            fontFamily: "var(--font-display)",
                            fontSize: "clamp(1.8rem, 4vw, 2.6rem)",
                            margin: 0,
                            textTransform: "capitalize",
                        }}
                    >
                        {monthLabel}
                    </h1>
                </div>

                <div
                    style={{
                        display: "flex",
                        flexWrap: "wrap",
                        gap: "10px",
                        alignItems: "center",
                    }}
                >
                    <span
                        style={{
                            fontSize: "0.72rem",
                            color: "var(--pencil)",
                            display: "flex",
                            alignItems: "center",
                            gap: "4px",
                            minWidth: "84px",
                        }}
                    >
                        {saveStatus === "saving" && (
                            <>
                                <Loader2 size={12} className="spin" />{" "}
                                Guardando…
                            </>
                        )}
                        {saveStatus === "saved" && (
                            <>
                                <Check size={12} /> Guardado
                            </>
                        )}
                        {saveStatus === "error" && "Error al guardar"}
                    </span>

                    <div
                        style={{
                            display: "flex",
                            border: "2px solid var(--ink)",
                            background: "var(--paper)",
                        }}
                    >
                        <button
                            onClick={() =>
                                setViewDate(new Date(year - 1, month, 1))
                            }
                            style={navBtnStyle}
                            title="Año anterior"
                            onMouseEnter={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(-2px) scale(1.05)";
                                e.currentTarget.style.background =
                                    "var(--blue)";
                                e.currentTarget.style.color = "var(--paper)";
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(0) scale(1)";
                                e.currentTarget.style.background =
                                    "var(--paper)";
                                e.currentTarget.style.color = "var(--ink)";
                            }}
                            onMouseDown={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(-2px) scale(0.98)";
                            }}
                            onMouseUp={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(-2px) scale(1.05)";
                            }}
                        >
                            <ChevronsLeft size={16} />
                        </button>
                        <button
                            onClick={() =>
                                setViewDate(new Date(year, month - 1, 1))
                            }
                            style={navBtnStyle}
                            title="Mes anterior"
                            onMouseEnter={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(-2px) scale(1.05)";
                                e.currentTarget.style.background =
                                    "var(--blue)";
                                e.currentTarget.style.color = "var(--paper)";
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(0) scale(1)";
                                e.currentTarget.style.background =
                                    "var(--paper)";
                                e.currentTarget.style.color = "var(--ink)";
                            }}
                            onMouseDown={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(-2px) scale(0.98)";
                            }}
                            onMouseUp={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(-2px) scale(1.05)";
                            }}
                        >
                            <ChevronLeft size={16} />
                        </button>
                        <button
                            onClick={() => setViewDate(new Date())}
                            style={{
                                ...navBtnStyle,
                                fontFamily: "var(--font-display)",
                                fontWeight: 700,
                                fontSize: "0.75rem",
                                padding: "8px 14px",
                            }}
                        >
                            Hoy
                        </button>
                        <button
                            onClick={() =>
                                setViewDate(new Date(year, month + 1, 1))
                            }
                            style={navBtnStyle}
                            title="Mes siguiente"
                            onMouseEnter={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(2px) scale(1.05)";
                                e.currentTarget.style.background =
                                    "var(--blue)";
                                e.currentTarget.style.color = "var(--paper)";
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(0) scale(1)";
                                e.currentTarget.style.background =
                                    "var(--paper)";
                                e.currentTarget.style.color = "var(--ink)";
                            }}
                            onMouseDown={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(2px) scale(0.98)";
                            }}
                            onMouseUp={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(2px) scale(1.05)";
                            }}
                        >
                            <ChevronRight size={16} />
                        </button>
                        <button
                            onClick={() =>
                                setViewDate(new Date(year + 1, month, 1))
                            }
                            style={navBtnStyle}
                            title="Año siguiente"
                            onMouseEnter={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(2px) scale(1.05)";
                                e.currentTarget.style.background =
                                    "var(--blue)";
                                e.currentTarget.style.color = "var(--paper)";
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(0) scale(1)";
                                e.currentTarget.style.background =
                                    "var(--paper)";
                                e.currentTarget.style.color = "var(--ink)";
                            }}
                            onMouseDown={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(2px) scale(0.98)";
                            }}
                            onMouseUp={(e) => {
                                e.currentTarget.style.transform =
                                    "translateX(2px) scale(1.05)";
                            }}
                        >
                            <ChevronsRight size={16} />
                        </button>
                    </div>
                </div>
            </div>

            {/* grid */}
            <div
                style={{
                    display: "grid",
                    gridTemplateColumns: "44px repeat(7, 1fr)",
                    gap: "8px",
                    width: "100%",
                }}
            >
                <div />
                {WEEKDAYS.map((d) => (
                    <div
                        key={d}
                        style={{
                            textAlign: "center",
                            fontFamily: "var(--font-display)",
                            fontSize: "0.78rem",
                            fontWeight: 700,
                            textTransform: "uppercase",
                            color: "var(--pencil)",
                            paddingBottom: "8px",
                        }}
                    >
                        {d}
                    </div>
                ))}

                {weeks.map((week, wi) => (
                    <React.Fragment key={wi}>
                        <div
                            style={{
                                display: "flex",
                                alignItems: "flex-start",
                                justifyContent: "center",
                                fontSize: "0.65rem",
                                color: "var(--pencil)",
                                writingMode: "vertical-rl",
                                paddingTop: "10px",
                                fontWeight: 700,
                            }}
                        >
                            S{getWeekNumber(week[0].date)}
                        </div>
                        {week.map(({ date, inMonth }, di) => {
                            const key = toKey(date);
                            const entry = data[key];
                            const isToday = isSameDate(date, today);
                            return (
                                <div
                                    key={di}
                                    className="cal-cell no-print-hover"
                                    onClick={() => setActiveKey(key)}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.transform =
                                            "translateY(-2px)";
                                        e.currentTarget.style.boxShadow =
                                            "0 8px 16px rgba(0,0,0,0.12)";
                                        e.currentTarget.style.zIndex = "10";
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.transform =
                                            "translateY(0)";
                                        e.currentTarget.style.boxShadow =
                                            "3px 3px 0 var(--ink)";
                                        e.currentTarget.style.zIndex = "auto";
                                    }}
                                    onMouseDown={(e) => {
                                        e.currentTarget.style.transform =
                                            "translateY(0)";
                                        e.currentTarget.style.boxShadow =
                                            "3px 3px 0 var(--ink)";
                                    }}
                                    onMouseUp={(e) => {
                                        e.currentTarget.style.transform =
                                            "translateY(0)";
                                        e.currentTarget.style.boxShadow =
                                            "3px 3px 0 var(--ink)";
                                    }}
                                    style={{
                                        height: "clamp(80px, 12vw, 130px)",
                                        minWidth: 0,
                                        background: entry?.color
                                            ? `${entry.color}26`
                                            : "var(--paper)",
                                        border: `2px solid ${isToday ? "var(--blue)" : "var(--ink)"}`,
                                        padding: "8px",
                                        cursor: "pointer",
                                        display: "flex",
                                        flexDirection: "column",
                                        gap: "6px",
                                        opacity: inMonth ? 1 : 0.4,
                                        boxShadow: "3px 3px 0 var(--ink)",
                                        transition:
                                            "transform 0.1s ease, box-shadow 0.1s ease",
                                        position: "relative",
                                    }}
                                >
                                    <span
                                        style={{
                                            fontSize: "0.95rem",
                                            fontWeight: 700,
                                            width: "24px",
                                            height: "24px",
                                            display: "flex",
                                            alignItems: "center",
                                            justifyContent: "center",
                                            borderRadius: "4px",
                                            background: isToday
                                                ? "var(--blue)"
                                                : "transparent",
                                            color: isToday
                                                ? "var(--paper)"
                                                : "var(--ink)",
                                        }}
                                    >
                                        {date.getDate()}
                                    </span>
                                    {entry?.title && (
                                        <div
                                            style={{
                                                fontSize: "0.68rem",
                                                fontWeight: 700,
                                                padding: "3px 6px",
                                                background:
                                                    entry.color || "var(--ink)",
                                                color: "#fff",
                                                overflow: "hidden",
                                                textOverflow: "ellipsis",
                                                whiteSpace: "nowrap",
                                                minWidth: 0,
                                            }}
                                        >
                                            {entry.title}
                                        </div>
                                    )}
                                    {entry?.note && (
                                        <div
                                            style={{
                                                fontSize: "0.65rem",
                                                color: "var(--pencil)",
                                                overflow: "hidden",
                                                textOverflow: "ellipsis",
                                                display: "-webkit-box",
                                                WebkitLineClamp: 2,
                                                WebkitBoxOrient: "vertical",
                                                wordBreak: "break-word",
                                                wordWrap: "break-word",
                                                minWidth: 0,
                                            }}
                                        >
                                            {entry.note}
                                        </div>
                                    )}
                                    {entry?.drawing && (
                                        <Pencil
                                            size={11}
                                            style={{
                                                marginTop: "auto",
                                                alignSelf: "flex-end",
                                                color: "var(--pencil)",
                                            }}
                                        />
                                    )}
                                </div>
                            );
                        })}
                    </React.Fragment>
                ))}
            </div>

            <p
                className="no-print"
                style={{
                    marginTop: "20px",
                    fontSize: "0.72rem",
                    color: "var(--pencil)",
                }}
            >
                Haz clic en un día para añadir un título, una nota (hasta 200
                caracteres), un color o un dibujo. Los cambios se guardan
                automáticamente.
            </p>

            {activeKey && (
                <DayModal
                    date={activeDate}
                    entry={data[activeKey]}
                    onChange={(patch) => updateEntry(activeKey, patch)}
                    onClear={() => clearEntry(activeKey)}
                    onClose={() => setActiveKey(null)}
                />
            )}
        </div>
    );
}