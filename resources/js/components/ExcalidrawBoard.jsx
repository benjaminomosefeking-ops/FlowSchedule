import React, {
    useState,
    useMemo,
    useCallback,
    useEffect,
    useRef,
} from "react";
import { flushSync } from "react-dom";
import {
    Pencil,
    Pen,
    Paintbrush,
    Eraser,
    Highlighter,
    Move,
    RotateCcw,
    RotateCw,
    Minus,
    Plus,
    Trash2,
    CalendarDays,
    ChevronLeft,
    ChevronRight,
    X,
    StickyNote,
    Table,
    Ban,
    Type,
    Users,
    AlertCircle,
    Download,
    Check,
} from "lucide-react";

import html2canvas from "html2canvas";

const DEFAULT_VIEWPORT = { scale: 1, offsetX: 0, offsetY: 0 };
const MIN_ZOOM = 0.1;
const MAX_ZOOM = 8;
const HISTORY_LIMIT = 60;
const SAVE_DEBOUNCE_MS = 700;
const MIN_CALENDAR_WIDTH = 180;
const MAX_CALENDAR_WIDTH = 600;
const DEFAULT_CALENDAR_WIDTH = 260;
const DEFAULT_STICKY_SIZE = 150;
const DEFAULT_TABLE_ROWS = 4;
const DEFAULT_TABLE_COLS = 4;
const MIN_TABLE_WIDTH = 180;
const MIN_TABLE_HEIGHT = 120;
const DEFAULT_TEXT_SIZE = 20;
const TEXT_LINE_HEIGHT = 1.25;
const BASE_GRID_SIZE = 28;
const MIN_GRID_SIZE = 14;
const TABLE_FILL_PALETTE = [
    "#FEE440",
    "#FFB7B2",
    "#B2F2BB",
    "#B2CEFE",
    "#D1B2FE",
    "#F8C9A9",
    "#FFFFFF",
];
const STICKY_PALETTE = ["#FEE440", "#FFB7B2", "#B2F2BB", "#B2CEFE", "#D1B2FE"];
const CALENDAR_EVENT_PALETTE = ["#FEE440", "#FFB7B2", "#B2F2BB", "#B2CEFE", "#D1B2FE"];

// Estilo común de las asas de redimensionado (círculo grande, fácil de tocar).
const HANDLE_SHADOW = "0 0 0 3px rgba(255,255,255,0.7)";

const TEXT_FONT_OPTIONS = [
    { id: "hand", label: "Manuscrita", family: "var(--font-hand, 'Caveat', cursive)" },
    { id: "display", label: "Título", family: "var(--font-display, 'Georgia', serif)" },
    { id: "body", label: "Cuerpo", family: "var(--font-body, sans-serif)" },
    { id: "mono", label: "Monoespacio", family: "'JetBrains Mono', 'Courier New', monospace" },
    { id: "serif", label: "Serif", family: "Georgia, 'Times New Roman', serif" },
];

const clampZoom = (value) => Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, value));
const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

// Herramientas de dibujo disponibles
const DRAWING_TOOLS = {
    pencil: { name: "Lápiz", icon: Pencil, opacity: 1, defaultSize: 2.5, key: "P" },
    pen: { name: "Bolígrafo", icon: Pen, opacity: 1, defaultSize: 2.0, key: "B" },
    marker: {
        name: "Marcador",
        icon: Paintbrush,
        opacity: 0.8,
        defaultSize: 5.0,
        key: "M",
    },
    eraser: { name: "Goma", icon: Eraser, opacity: 1, defaultSize: 25.0, key: "G" },
    highlighter: {
        name: "Resaltador",
        icon: Highlighter,
        opacity: 0.4,
        defaultSize: 15.0,
        key: "R",
    },
};

const COLOR_PALETTE = [
    { color: "#1A1D24", name: "Tinta" },
    { color: "#1F3FA8", name: "Azul" },
    { color: "#E63946", name: "Rojo" },
    { color: "#2A9D8F", name: "Verde" },
    { color: "#F1C761", name: "Amarillo" },
    { color: "#8338EC", name: "Morado" },
    { color: "#FB5607", name: "Naranja" },
    { color: "#6B6660", name: "Grafito" },
];

const MONTH_NAMES = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
];
const DAY_NAMES = ["L", "M", "X", "J", "V", "S", "D"];

function getMonthMatrix(year, month) {
    const first = new Date(year, month, 1);
    // Lunes = 0 ... Domingo = 6
    const firstWeekday = (first.getDay() + 6) % 7;
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const cells = [];
    for (let i = 0; i < firstWeekday; i += 1) cells.push(null);
    for (let d = 1; d <= daysInMonth; d += 1) cells.push(d);
    while (cells.length % 7 !== 0) cells.push(null);

    const weeks = [];
    for (let i = 0; i < cells.length; i += 7) weeks.push(cells.slice(i, i + 7));
    return weeks;
}

/* =====================================================================
   Cuadrante de horario ("Mi horario")
   Diseño: hoja de papel con cinta rayada, sombra dura, filas por categoría
   (Trabajo / Estudio / Descanso / Personal…), bloques de color con título y
   franja horaria, resumen de horas por fila, leyenda y estado de sobrecarga.
   ===================================================================== */

const DEFAULT_ROSTER_WIDTH = 640;
const MAX_SHIFT_TITLE_LENGTH = 22;
const MAX_SHIFT_NOTE_LENGTH = 18;

const INK = "var(--ink, #1A1D24)";
const PAPER = "var(--paper, #F2EBDC)";
const PENCIL = "var(--pencil, #6B6660)";
const FONT_MONO = "var(--font-mono, 'IBM Plex Mono', 'JetBrains Mono', 'Courier New', monospace)";
const FONT_HAND = "var(--font-hand, 'Caveat', cursive)";
const OK_GREEN = "#1E6B4E";
const BAD_RED = "#B3261E";

const ROSTER_DAY_NAMES = ["LUN", "MAR", "MIÉ", "JUE", "VIE", "SÁB", "DOM"];
const MONTH_SHORT = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];

// Cada "tono" es un estilo de bloque. El amarillo va con borde discontinuo,
// igual que los bloques de descanso de la imagen de referencia.
const ROSTER_TONES = [
    { id: "blue", label: "Azul", bg: "#1F3FA8", fg: "#FFFFFF", dashed: false },
    { id: "ink", label: "Tinta", bg: "#1A1D24", fg: "#F2EBDC", dashed: false },
    { id: "yellow", label: "Amarillo", bg: "#F4C95D", fg: "#1A1D24", dashed: true },
    { id: "red", label: "Rojo", bg: "#CC3018", fg: "#FFFFFF", dashed: false },
    { id: "green", label: "Verde", bg: "#2A9D8F", fg: "#FFFFFF", dashed: false },
    { id: "purple", label: "Morado", bg: "#6D3FC4", fg: "#FFFFFF", dashed: false },
];

const getTone = (id) => ROSTER_TONES.find((t) => t.id === id) || ROSTER_TONES[0];

/* ------------------------------ Semanas ------------------------------ */

function getISOWeek(date) {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = (d.getUTCDay() + 6) % 7;
    d.setUTCDate(d.getUTCDate() - dayNum + 3);
    const firstThursday = new Date(Date.UTC(d.getUTCFullYear(), 0, 4));
    return 1 + Math.round((d - firstThursday) / (7 * 24 * 60 * 60 * 1000));
}

function getISOWeekYear(date) {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = (d.getUTCDay() + 6) % 7;
    d.setUTCDate(d.getUTCDate() - dayNum + 3);
    return d.getUTCFullYear();
}

// El 28 de diciembre siempre cae en la última semana ISO del año.
const weeksInYear = (year) => getISOWeek(new Date(year, 11, 28));

function getISOWeekStart(year, week) {
    const jan4 = new Date(year, 0, 4);
    const dayNum = (jan4.getDay() + 6) % 7;
    return new Date(year, 0, 4 - dayNum + (week - 1) * 7);
}

function formatWeekRange(start, dayCount) {
    const end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + dayCount - 1);
    if (start.getMonth() === end.getMonth()) {
        return `${start.getDate()}–${end.getDate()} ${MONTH_SHORT[end.getMonth()]}`;
    }
    return `${start.getDate()} ${MONTH_SHORT[start.getMonth()]}–${end.getDate()} ${MONTH_SHORT[end.getMonth()]}`;
}

/* ------------------------------ Horas ------------------------------ */

const toMinutes = (t) => {
    if (!t || !/^\d{1,2}:\d{2}$/.test(t)) return null;
    const [h, m] = t.split(":").map(Number);
    return h * 60 + m;
};

// "09:00" -> "09", "20:15" -> "20:15"
const fmtTime = (t) => {
    const total = toMinutes(t);
    if (total === null) return "";
    const h = String(Math.floor(total / 60)).padStart(2, "0");
    const m = total % 60;
    return m ? `${h}:${String(m).padStart(2, "0")}` : h;
};

const fmtRange = (start, end) => {
    const a = fmtTime(start);
    const b = fmtTime(end);
    if (a && b) return `${a}–${b}`;
    return a || b || "";
};

const shiftDuration = (shift) => {
    const a = toMinutes(shift.start);
    const b = toMinutes(shift.end);
    if (a === null || b === null) return 0;
    let diff = b - a;
    if (diff < 0) diff += 24 * 60; // turno que cruza la medianoche
    return diff / 60;
};

const fmtHours = (h) => {
    const rounded = Math.round(h * 10) / 10;
    return `${Number.isInteger(rounded) ? rounded : String(rounded).replace(".", ",")}h`;
};

// Lee objetivos del tipo "8H/DÍA" o "2h/día" del subtítulo de la fila.
const parseDailyTarget = (hint) => {
    const match = String(hint || "").match(/(\d+(?:[.,]\d+)?)\s*h\s*\/\s*d/i);
    return match ? parseFloat(match[1].replace(",", ".")) : null;
};

/* ------------------------------ Datos ------------------------------ */

const weekPrefix = (roster) => `${roster.year}-${roster.weekNumber}:`;
const shiftKey = (roster, memberId, dayIndex) =>
    `${weekPrefix(roster)}${memberId}-${dayIndex}`;

// Acepta cuadrantes guardados con el diseño anterior y los convierte al nuevo
// formato (turnos por semana, título + horas, tono de color por fila).
function normalizeRoster(raw) {
    const now = new Date();
    const year = raw.year ?? getISOWeekYear(now);
    const weekNumber = raw.weekNumber ?? getISOWeek(now);
    const prefix = `${year}-${weekNumber}:`;

    const members = (Array.isArray(raw.members) ? raw.members : []).map((m) => ({
        ...m,
        hint: m.hint ?? "",
        tone:
            m.tone ||
            ROSTER_TONES.find((t) => t.bg.toLowerCase() === String(m.color || "").toLowerCase())?.id ||
            "blue",
    }));

    const shifts = {};
    Object.entries(raw.shifts || {}).forEach(([oldKey, s]) => {
        if (!s) return;
        const key = oldKey.includes(":") ? oldKey : `${prefix}${oldKey}`;
        if ("title" in s || "start" in s) {
            shifts[key] = s;
            return;
        }
        const title = s.libre ? "Libre" : String(s.text || "");
        if (!title) return;
        shifts[key] = {
            title: title.slice(0, MAX_SHIFT_TITLE_LENGTH),
            start: "",
            end: "",
            note: "",
            tone: s.libre
                ? "yellow"
                : ROSTER_TONES.find((t) => t.bg.toLowerCase() === String(s.color || "").toLowerCase())?.id || null,
        };
    });

    return {
        ...raw,
        title: !raw.title || raw.title === "Nombre de la tabla" ? "MI HORARIO" : raw.title,
        year,
        weekNumber,
        weekend: Boolean(raw.weekend),
        members,
        shifts,
    };
}

function createRoster({ x, y, z }) {
    const now = new Date();
    const stamp = Date.now().toString(36);
    const rows = [
        ["Trabajo", "8H/DÍA", "blue"],
        ["Estudio", "2H/DÍA", "ink"],
        ["Descanso", "ESENCIAL", "yellow"],
        ["Personal", "FLEXIBLE", "red"],
    ];
    return {
        id: `roster-${stamp}-${Math.random().toString(36).slice(2, 7)}`,
        x: x - DEFAULT_ROSTER_WIDTH / 2,
        y: y - 210,
        width: DEFAULT_ROSTER_WIDTH,
        title: "MI HORARIO",
        year: getISOWeekYear(now),
        weekNumber: getISOWeek(now),
        weekend: false,
        members: rows.map(([name, hint, tone], i) => ({
            id: `m${i}-${stamp}`,
            name,
            hint,
            tone,
        })),
        shifts: {},
        z,
    };
}

// --- Transformaciones puras: se usan con onMutate((r) => ...) ---

function addRosterMember(r) {
    const tone = ROSTER_TONES[r.members.length % ROSTER_TONES.length].id;
    return {
        ...r,
        members: [
            ...r.members,
            {
                id: `m-${Date.now().toString(36)}-${Math.random().toString(36).slice(2, 5)}`,
                name: "Nueva fila",
                hint: "",
                tone,
            },
        ],
    };
}

function removeRosterMember(r, memberId) {
    const shifts = { ...r.shifts };
    Object.keys(shifts).forEach((key) => {
        if (key.includes(`:${memberId}-`)) delete shifts[key];
    });
    return { ...r, members: r.members.filter((m) => m.id !== memberId), shifts };
}

function updateRosterMember(r, memberId, updates) {
    return {
        ...r,
        members: r.members.map((m) => (m.id === memberId ? { ...m, ...updates } : m)),
    };
}

function shiftRosterWeek(r, delta) {
    let week = r.weekNumber + delta;
    let year = r.year;
    if (week > weeksInYear(year)) {
        week = 1;
        year += 1;
    } else if (week < 1) {
        year -= 1;
        week = weeksInYear(year);
    }
    return { ...r, weekNumber: week, year };
}

function toggleRosterWeekend(r) {
    const next = !r.weekend;
    // Se ensancha o estrecha la tarjeta para que las columnas mantengan su tamaño.
    const width = Math.max(480, r.width + (next ? 208 : -208));
    return { ...r, weekend: next, width };
}

// updates === null vacía la celda.
function applyShiftUpdate(r, memberId, dayIndex, updates) {
    const key = shiftKey(r, memberId, dayIndex);
    const shifts = { ...r.shifts };
    if (updates === null) {
        delete shifts[key];
        return { ...r, shifts };
    }
    const next = {
        title: "",
        start: "",
        end: "",
        note: "",
        tone: null,
        ...(shifts[key] || {}),
        ...updates,
    };
    next.title = next.title.slice(0, MAX_SHIFT_TITLE_LENGTH);
    next.note = next.note.slice(0, MAX_SHIFT_NOTE_LENGTH);
    if (!next.title && !next.start && !next.end && !next.note && !next.tone) {
        delete shifts[key];
    } else {
        shifts[key] = next;
    }
    return { ...r, shifts };
}

/* ------------------------------ Análisis ------------------------------
   Total de horas por fila + detección de problemas:
   - dos bloques que se solapan el mismo día
   - un bloque que supera el objetivo diario de su fila ("8H/DÍA")        */

function analyzeRoster(roster, dayCount) {
    const totals = {};
    const conflicts = new Set();
    const messages = [];

    roster.members.forEach((member) => {
        totals[member.id] = 0;
        const target = parseDailyTarget(member.hint);
        for (let d = 0; d < dayCount; d += 1) {
            const shift = roster.shifts[shiftKey(roster, member.id, d)];
            if (!shift) continue;
            const hours = shiftDuration(shift);
            totals[member.id] += hours;
            if (target !== null && hours > target) {
                conflicts.add(shiftKey(roster, member.id, d));
                messages.push(
                    `${ROSTER_DAY_NAMES[d]}: ${member.name} pasa de ${fmtHours(target)}/día`,
                );
            }
        }
    });

    for (let d = 0; d < dayCount; d += 1) {
        const items = [];
        roster.members.forEach((member) => {
            const key = shiftKey(roster, member.id, d);
            const shift = roster.shifts[key];
            if (!shift) return;
            const start = toMinutes(shift.start);
            let end = toMinutes(shift.end);
            if (start === null || end === null) return;
            if (end < start) end += 24 * 60;
            items.push({ key, start, end, name: shift.title || member.name });
        });
        items.sort((a, b) => a.start - b.start);
        for (let i = 0; i < items.length; i += 1) {
            for (let j = i + 1; j < items.length; j += 1) {
                if (items[j].start < items[i].end) {
                    conflicts.add(items[i].key);
                    conflicts.add(items[j].key);
                    messages.push(`${ROSTER_DAY_NAMES[d]}: se solapan ${items[i].name} y ${items[j].name}`);
                }
            }
        }
    }

    return { totals, conflicts, messages };
}

/* ------------------------------ Tarjeta ------------------------------ */

const roundBtnStyle = {
    width: 30,
    height: 30,
    borderRadius: "50%",
    border: `1.5px solid ${INK}`,
    background: "transparent",
    color: INK,
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    cursor: "pointer",
    padding: 0,
    flexShrink: 0,
};

const toolButtonStyle = {
    display: "flex",
    alignItems: "center",
    gap: 5,
    border: "1px dashed rgba(26,29,36,0.35)",
    background: "transparent",
    color: PENCIL,
    fontFamily: FONT_MONO,
    fontSize: 11,
    padding: "6px 10px",
    cursor: "pointer",
};

function RosterCard({
    roster,
    onMutate,
    onRemove,
    onOpenShift,
    dragHandlers = {},
    resizeHandlers = {},
}) {
    const dayCount = roster.weekend ? 7 : 5;
    const days = ROSTER_DAY_NAMES.slice(0, dayCount);
    const gridCols = `112px repeat(${dayCount}, minmax(0, 1fr))`;

    const { totals, conflicts, messages } = useMemo(
        () => analyzeRoster(roster, dayCount),
        [roster, dayCount],
    );
    const hasIssues = messages.length > 0;

    const rangeLabel = formatWeekRange(getISOWeekStart(roster.year, roster.weekNumber), dayCount);
    const now = new Date();
    const todayIndex =
        getISOWeek(now) === roster.weekNumber && getISOWeekYear(now) === roster.year
            ? (now.getDay() + 6) % 7
            : -1;

    const stop = (e) => e.stopPropagation();

    return (
        <div
            style={{
                position: "absolute",
                left: roster.x,
                top: roster.y,
                width: roster.width,
                zIndex: roster.z || 0,
                pointerEvents: "auto",
                userSelect: "none",
                background: PAPER,
                border: `1.5px solid ${INK}`,
                boxShadow: `8px 8px 0 ${INK}`,
                padding: "26px 28px 22px",
                boxSizing: "border-box",
                transform: "rotate(0.5deg)",
                fontFamily: FONT_MONO,
                color: INK,
            }}
        >
            <style>{`
                .ebx-roster-row .ebx-roster-rowx { opacity: 0; transition: opacity 0.12s ease; }
                .ebx-roster-row:hover .ebx-roster-rowx { opacity: 0.6; }
                .ebx-roster-row .ebx-roster-rowx:hover { opacity: 1; }
                .ebx-rcell .ebx-rcell-plus { opacity: 0; transition: opacity 0.12s ease; }
                .ebx-rcell:hover .ebx-rcell-plus { opacity: 0.45; }
                .ebx-rcell .ebx-rblock { transition: transform 0.12s ease, box-shadow 0.12s ease; }
                .ebx-rcell:hover .ebx-rblock { transform: translateY(-1px); box-shadow: 0 3px 0 rgba(26,29,36,0.18); }
                .ebx-rcell:focus-visible { outline: 2px solid ${INK}; outline-offset: -2px; }
                .ebx-rinput::placeholder { opacity: 0.35; }
                .ebx-rtool { transition: background 0.12s ease; }
                .ebx-rtool:hover { background: rgba(26,29,36,0.07); }
                .ebx-rround:hover { background: rgba(26,29,36,0.08); }
            `}</style>

            {/* Cinta rayada superior */}
            <div
                style={{
                    position: "absolute",
                    left: -1.5,
                    right: -1.5,
                    top: -1.5,
                    height: 11,
                    background: `repeating-linear-gradient(135deg, ${INK} 0 6px, transparent 6px 12px)`,
                    opacity: 0.9,
                    pointerEvents: "none",
                }}
            />

            {/* Eliminar tarjeta (no sale en la exportación) */}
            <button
                type="button"
                data-export-ignore="true"
                className="ebx-rtool"
                onClick={onRemove}
                onPointerDown={stop}
                title="Eliminar horario"
                style={{
                    position: "absolute",
                    top: 14,
                    right: 8,
                    border: "none",
                    background: "transparent",
                    color: PENCIL,
                    cursor: "pointer",
                    padding: 2,
                    display: "flex",
                    opacity: 0.6,
                }}
            >
                <X size={13} />
            </button>

            {/* Cabecera: ‹ TÍTULO ›  ·····  sem. N · rango  (zona de arrastre) */}
            <div
                {...dragHandlers}
                style={{
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "space-between",
                    gap: 12,
                    padding: "14px 0 20px",
                    minHeight: 64,
                    borderBottom: `1px solid ${INK}`,
                    cursor: "grab",
                    touchAction: "none",
                }}
            >
                <div style={{ display: "flex", alignItems: "center", gap: 12, minWidth: 0 }}>
                    <button
                        type="button"
                        className="ebx-rround"
                        onClick={() => onMutate((r) => shiftRosterWeek(r, -1))}
                        onPointerDown={stop}
                        title="Semana anterior"
                        style={roundBtnStyle}
                    >
                        <ChevronLeft size={16} />
                    </button>
                    <input
                        value={roster.title}
                        onChange={(e) => onMutate((r) => ({ ...r, title: e.target.value.toUpperCase() }))}
                        onPointerDown={stop}
                        size={Math.max(6, roster.title.length + 1)}
                        style={{
                            fontFamily: FONT_MONO,
                            fontSize: 21,
                            fontWeight: 700,
                            letterSpacing: "0.04em",
                            color: INK,
                            background: "transparent",
                            border: "none",
                            outline: "none",
                            padding: 0,
                            minWidth: 0,
                            maxWidth: 260,
                        }}
                    />
                    <button
                        type="button"
                        className="ebx-rround"
                        onClick={() => onMutate((r) => shiftRosterWeek(r, 1))}
                        onPointerDown={stop}
                        title="Semana siguiente"
                        style={roundBtnStyle}
                    >
                        <ChevronRight size={16} />
                    </button>
                </div>
                <span
                    style={{
                        fontFamily: FONT_HAND,
                        fontStyle: "italic",
                        fontSize: 25,
                        color: "rgba(26,29,36,0.7)",
                        whiteSpace: "nowrap",
                        paddingRight: 14,
                    }}
                >
                    sem. {roster.weekNumber} · {rangeLabel}
                </span>
            </div>

            {/* Días */}
            <div style={{ display: "grid", gridTemplateColumns: gridCols, paddingTop: 14 }}>
                <div />
                {days.map((d, i) => (
                    <div
                        key={d}
                        style={{
                            textAlign: "center",
                            fontSize: 13,
                            fontWeight: 700,
                            letterSpacing: "0.08em",
                            padding: "0 0 12px",
                            background: i === todayIndex ? "rgba(241,199,97,0.28)" : "transparent",
                        }}
                    >
                        {d}
                    </div>
                ))}
            </div>

            {/* Filas por categoría */}
            <div style={{ borderTop: "1px solid rgba(26,29,36,0.2)" }}>
                {roster.members.map((member, rowIndex) => (
                    <div
                        key={member.id}
                        className="ebx-roster-row"
                        style={{
                            position: "relative",
                            display: "grid",
                            gridTemplateColumns: gridCols,
                            borderTop: rowIndex === 0 ? "none" : "1px solid rgba(26,29,36,0.16)",
                        }}
                    >
                        {/* Etiqueta de la fila */}
                        <div
                            style={{
                                position: "relative",
                                borderRight: `1.5px solid ${INK}`,
                                padding: "12px 12px 8px 4px",
                                display: "flex",
                                flexDirection: "column",
                                alignItems: "flex-end",
                                gap: 3,
                                minWidth: 0,
                            }}
                        >
                            <input
                                value={member.name}
                                onChange={(e) =>
                                    onMutate((r) => updateRosterMember(r, member.id, { name: e.target.value }))
                                }
                                className="ebx-rinput"
                                style={{
                                    width: "100%",
                                    textAlign: "right",
                                    fontFamily: FONT_HAND,
                                    fontSize: 26,
                                    lineHeight: 1,
                                    color: INK,
                                    background: "transparent",
                                    border: "none",
                                    outline: "none",
                                    padding: 0,
                                }}
                            />
                            <input
                                value={member.hint}
                                onChange={(e) =>
                                    onMutate((r) =>
                                        updateRosterMember(r, member.id, { hint: e.target.value.toUpperCase() }),
                                    )
                                }
                                placeholder="OBJETIVO"
                                className="ebx-rinput"
                                style={{
                                    width: "100%",
                                    textAlign: "right",
                                    fontFamily: FONT_MONO,
                                    fontSize: 11.5,
                                    letterSpacing: "0.08em",
                                    color: PENCIL,
                                    background: "transparent",
                                    border: "none",
                                    outline: "none",
                                    padding: 0,
                                }}
                            />
                            <button
                                type="button"
                                data-export-ignore="true"
                                className="ebx-roster-rowx"
                                onClick={() => onMutate((r) => removeRosterMember(r, member.id))}
                                title="Quitar fila"
                                style={{
                                    position: "absolute",
                                    left: 0,
                                    top: 4,
                                    border: "none",
                                    background: "transparent",
                                    color: PENCIL,
                                    cursor: "pointer",
                                    padding: 2,
                                    display: "flex",
                                }}
                            >
                                <X size={11} />
                            </button>
                        </div>

                        {/* Celdas por día */}
                        {days.map((dayName, dayIndex) => {
                            const key = shiftKey(roster, member.id, dayIndex);
                            const shift = roster.shifts[key];
                            const hasContent = shift && (shift.title || shift.start || shift.end);
                            const tone = getTone(shift?.tone || member.tone);
                            const isConflict = conflicts.has(key);
                            const range = shift ? fmtRange(shift.start, shift.end) : "";

                            return (
                                <div
                                    key={dayIndex}
                                    role="button"
                                    tabIndex={0}
                                    className="ebx-rcell"
                                    onClick={() => onOpenShift(member.id, dayIndex)}
                                    onKeyDown={(e) => {
                                        if (e.key === "Enter" || e.key === " ") {
                                            e.preventDefault();
                                            onOpenShift(member.id, dayIndex);
                                        }
                                    }}
                                    title={`${member.name} · ${dayName}`}
                                    style={{
                                        position: "relative",
                                        minHeight: 68,
                                        padding: "7px 5px 9px",
                                        boxSizing: "border-box",
                                        display: "flex",
                                        alignItems: "flex-start",
                                        justifyContent: "center",
                                        cursor: "pointer",
                                        borderLeft: dayIndex === 0 ? "none" : "1px solid rgba(26,29,36,0.14)",
                                        background:
                                            dayIndex === todayIndex ? "rgba(241,199,97,0.28)" : "transparent",
                                    }}
                                >
                                    {hasContent ? (
                                        <div
                                            className="ebx-rblock"
                                            style={{
                                                width: "100%",
                                                boxSizing: "border-box",
                                                background: tone.bg,
                                                color: tone.fg,
                                                border: tone.dashed
                                                    ? `1.5px dashed ${INK}`
                                                    : "1.5px solid transparent",
                                                padding: "6px 8px 7px",
                                                lineHeight: 1.15,
                                                textAlign: "left",
                                                outline: isConflict ? `2px solid ${BAD_RED}` : "none",
                                                outlineOffset: 1,
                                            }}
                                        >
                                            {shift.title && (
                                                <div
                                                    style={{
                                                        fontSize: 13,
                                                        fontWeight: 700,
                                                        wordBreak: "break-word",
                                                    }}
                                                >
                                                    {shift.title}
                                                </div>
                                            )}
                                            {range && (
                                                <div
                                                    style={{
                                                        fontSize: 11.5,
                                                        fontWeight: 500,
                                                        marginTop: shift.title ? 4 : 0,
                                                        opacity: 0.88,
                                                        letterSpacing: "0.02em",
                                                    }}
                                                >
                                                    {range}
                                                </div>
                                            )}
                                        </div>
                                    ) : (
                                        <span
                                            data-export-ignore="true"
                                            className="ebx-rcell-plus"
                                            style={{ marginTop: 14, display: "flex", color: PENCIL }}
                                        >
                                            <Plus size={13} />
                                        </span>
                                    )}

                                    {/* Anotación a mano, tipo "¡deadline!" */}
                                    {hasContent && shift.note && (
                                        <span
                                            style={{
                                                position: "absolute",
                                                right: 8,
                                                bottom: 1,
                                                fontFamily: FONT_HAND,
                                                fontSize: 16,
                                                color: "#CC3018",
                                                transform: "rotate(-4deg)",
                                                whiteSpace: "nowrap",
                                                pointerEvents: "none",
                                                zIndex: 2,
                                            }}
                                        >
                                            {shift.note}
                                        </span>
                                    )}
                                </div>
                            );
                        })}
                    </div>
                ))}
                <div style={{ borderTop: "1px solid rgba(26,29,36,0.2)" }} />
            </div>

            {/* Resumen de horas por fila */}
            {roster.members.length > 0 && (
                <div
                    style={{
                        display: "flex",
                        border: `1px solid ${INK}`,
                        marginTop: 20,
                    }}
                >
                    {roster.members.map((member, i) => (
                        <div
                            key={member.id}
                            style={{
                                flex: 1,
                                minWidth: 0,
                                textAlign: "center",
                                padding: "11px 6px 10px",
                                borderLeft: i === 0 ? "none" : "1px solid rgba(26,29,36,0.3)",
                            }}
                        >
                            <div style={{ fontSize: 21, fontWeight: 700, lineHeight: 1.1 }}>
                                {fmtHours(totals[member.id] || 0)}
                            </div>
                            <div
                                style={{
                                    fontSize: 10.5,
                                    letterSpacing: "0.1em",
                                    color: PENCIL,
                                    marginTop: 4,
                                    textTransform: "uppercase",
                                    overflow: "hidden",
                                    textOverflow: "ellipsis",
                                    whiteSpace: "nowrap",
                                }}
                            >
                                {member.name}
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {/* Leyenda + estado */}
            <div
                style={{
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "space-between",
                    gap: 12,
                    flexWrap: "wrap",
                    borderTop: `1px solid ${INK}`,
                    marginTop: 18,
                    paddingTop: 14,
                }}
            >
                <div style={{ display: "flex", alignItems: "center", gap: 14, flexWrap: "wrap" }}>
                    {roster.members.map((member) => (
                        <span
                            key={member.id}
                            style={{
                                display: "flex",
                                alignItems: "center",
                                gap: 6,
                                fontSize: 11.5,
                                letterSpacing: "0.08em",
                                color: PENCIL,
                                textTransform: "uppercase",
                            }}
                        >
                            <span
                                style={{
                                    width: 10,
                                    height: 10,
                                    borderRadius: "50%",
                                    background: getTone(member.tone).bg,
                                    flexShrink: 0,
                                }}
                            />
                            {member.name}
                        </span>
                    ))}
                </div>
                <span
                    title={hasIssues ? messages.join("\n") : "Sin solapes ni excesos de horas"}
                    style={{
                        display: "flex",
                        alignItems: "center",
                        gap: 6,
                        fontSize: 13,
                        fontWeight: 700,
                        letterSpacing: "0.06em",
                        color: hasIssues ? BAD_RED : OK_GREEN,
                        whiteSpace: "nowrap",
                    }}
                >
                    {hasIssues ? <AlertCircle size={15} /> : <Check size={15} strokeWidth={2.6} />}
                    {hasIssues ? "SOBRECARGA" : "SIN SOBRECARGA"}
                </span>
            </div>

            {hasIssues && (
                <div style={{ marginTop: 8, fontSize: 11, color: BAD_RED, lineHeight: 1.5 }}>
                    {messages.slice(0, 3).map((m) => (
                        <div key={m}>{m}</div>
                    ))}
                    {messages.length > 3 && <div>+{messages.length - 3} más</div>}
                </div>
            )}

            {/* Controles de edición (no salen en la exportación) */}
            <div
                data-export-ignore="true"
                style={{ display: "flex", gap: 8, marginTop: 16, flexWrap: "wrap" }}
            >
                <button
                    type="button"
                    className="ebx-rtool"
                    onClick={() => onMutate(addRosterMember)}
                    style={toolButtonStyle}
                >
                    <Plus size={12} /> Añadir fila
                </button>
                <button
                    type="button"
                    className="ebx-rtool"
                    onClick={() => onMutate(toggleRosterWeekend)}
                    style={toolButtonStyle}
                >
                    {roster.weekend ? "Ocultar fin de semana" : "Mostrar fin de semana"}
                </button>
            </div>

            {/* Asa de ancho (más grande para poder usarla bien) */}
            <div
                data-export-ignore="true"
                {...resizeHandlers}
                title="Arrastra para cambiar el ancho"
                style={{
                    position: "absolute",
                    right: -11,
                    bottom: -11,
                    width: 24,
                    height: 24,
                    borderRadius: "50%",
                    background: INK,
                    opacity: 0.85,
                    cursor: "ew-resize",
                    touchAction: "none",
                    boxShadow: HANDLE_SHADOW,
                }}
            />
        </div>
    );
}

/* ------------------------------ Editor de turno ------------------------------ */

function ShiftEditor({ roster, memberId, dayIndex, onMutate, onClose }) {
    const member = roster.members.find((m) => m.id === memberId);
    if (!member) return null;

    const shift = roster.shifts[shiftKey(roster, memberId, dayIndex)] || {};
    const activeTone = shift.tone || member.tone;
    const set = (updates) => onMutate((r) => applyShiftUpdate(r, memberId, dayIndex, updates));

    const labelStyle = {
        fontSize: 11,
        letterSpacing: "0.08em",
        textTransform: "uppercase",
        color: PENCIL,
        marginBottom: 5,
        display: "block",
    };
    const fieldStyle = {
        width: "100%",
        boxSizing: "border-box",
        padding: "8px 9px",
        border: `1px solid ${INK}`,
        background: "rgba(255,255,255,0.45)",
        fontFamily: FONT_MONO,
        fontSize: 13,
        color: INK,
        outline: "none",
        borderRadius: 0,
    };

    return (
        <div
            data-export-ignore="true"
            style={{
                position: "fixed",
                top: "50%",
                left: "50%",
                transform: "translate(-50%, -50%)",
                zIndex: 100,
                width: 300,
                maxWidth: "calc(100vw - 24px)",
                background: PAPER,
                border: `1.5px solid ${INK}`,
                boxShadow: `6px 6px 0 ${INK}`,
                padding: 16,
                display: "flex",
                flexDirection: "column",
                gap: 14,
                fontFamily: FONT_MONO,
                color: INK,
            }}
        >
            <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                <span style={{ fontWeight: 700, fontSize: 14 }}>
                    <span style={{ fontFamily: FONT_HAND, fontSize: 22, fontWeight: 400 }}>{member.name}</span>
                    {" · "}
                    {ROSTER_DAY_NAMES[dayIndex]}
                </span>
                <button
                    type="button"
                    className="ebx-btn"
                    onClick={onClose}
                    title="Cerrar"
                    style={{ border: "none", background: "transparent", cursor: "pointer", padding: 4, display: "flex" }}
                >
                    <X size={16} />
                </button>
            </div>

            <label>
                <span style={labelStyle}>Actividad</span>
                <input
                    autoFocus
                    value={shift.title || ""}
                    onChange={(e) => set({ title: e.target.value })}
                    placeholder="Ej. Reuniones"
                    maxLength={MAX_SHIFT_TITLE_LENGTH}
                    style={fieldStyle}
                />
            </label>

            <div style={{ display: "flex", gap: 10 }}>
                <label style={{ flex: 1 }}>
                    <span style={labelStyle}>Desde</span>
                    <input
                        type="time"
                        value={shift.start || ""}
                        onChange={(e) => set({ start: e.target.value })}
                        style={fieldStyle}
                    />
                </label>
                <label style={{ flex: 1 }}>
                    <span style={labelStyle}>Hasta</span>
                    <input
                        type="time"
                        value={shift.end || ""}
                        onChange={(e) => set({ end: e.target.value })}
                        style={fieldStyle}
                    />
                </label>
            </div>

            <div>
                <span style={labelStyle}>Color</span>
                <div style={{ display: "flex", alignItems: "center", gap: 8, flexWrap: "wrap" }}>
                    {ROSTER_TONES.map((tone) => (
                        <button
                            key={tone.id}
                            type="button"
                            className="ebx-swatch"
                            title={tone.label}
                            onClick={() => set({ tone: tone.id })}
                            style={{
                                width: 22,
                                height: 22,
                                background: tone.bg,
                                cursor: "pointer",
                                padding: 0,
                                border: tone.dashed ? `1.5px dashed ${INK}` : "1.5px solid transparent",
                                boxShadow: activeTone === tone.id ? `0 0 0 2px ${PAPER}, 0 0 0 3.5px ${INK}` : "none",
                            }}
                        />
                    ))}
                    {shift.tone && shift.tone !== member.tone && (
                        <button
                            type="button"
                            onClick={() => set({ tone: null })}
                            style={{
                                border: "none",
                                background: "transparent",
                                color: PENCIL,
                                fontFamily: FONT_MONO,
                                fontSize: 11,
                                textDecoration: "underline",
                                cursor: "pointer",
                                padding: 0,
                            }}
                        >
                            Igual que la fila
                        </button>
                    )}
                </div>
            </div>

            <label>
                <span style={labelStyle}>Nota a mano (opcional)</span>
                <input
                    value={shift.note || ""}
                    onChange={(e) => set({ note: e.target.value })}
                    placeholder="Ej. ¡deadline!"
                    maxLength={MAX_SHIFT_NOTE_LENGTH}
                    style={{ ...fieldStyle, fontFamily: FONT_HAND, fontSize: 17 }}
                />
            </label>

            <div style={{ display: "flex", gap: 8 }}>
                <button
                    type="button"
                    onClick={() => {
                        set(null);
                        onClose();
                    }}
                    style={{
                        flex: 1,
                        background: "transparent",
                        border: "1px dashed rgba(26,29,36,0.5)",
                        color: BAD_RED,
                        fontFamily: FONT_MONO,
                        fontSize: 12,
                        padding: "8px 8px",
                        cursor: "pointer",
                    }}
                >
                    Vaciar celda
                </button>
                <button
                    type="button"
                    className="ebx-popup-btn"
                    onClick={onClose}
                    style={{
                        flex: 1,
                        background: INK,
                        color: PAPER,
                        border: "none",
                        fontFamily: FONT_MONO,
                        fontWeight: 700,
                        fontSize: 12,
                        padding: "8px 8px",
                        cursor: "pointer",
                    }}
                >
                    Listo
                </button>
            </div>
        </div>
    );
}

/* =====================================================================
   Normalización de datos y utilidades generales del tablero
   ===================================================================== */

function normalizeInitialData(rawData) {
    let parsed = rawData;

    for (let i = 0; i < 2; i += 1) {
        if (typeof parsed === "string") {
            try {
                parsed = JSON.parse(parsed);
            } catch {
                parsed = {};
            }
        }
    }

    if (!parsed || typeof parsed !== "object") {
        return {
            type: "board",
            version: 4,
            strokes: [],
            calendars: [],
            stickies: [],
            tables: [],
            texts: [],
            rosters: [],
            viewport: DEFAULT_VIEWPORT,
        };
    }

    return {
        type: parsed.type || "board",
        version: parsed.version || 4,
        strokes: Array.isArray(parsed.strokes) ? parsed.strokes : [],
        calendars: Array.isArray(parsed.calendars)
            ? (parsed.calendars || []).map((cal) => ({
                  ...cal,
                  events: cal.events || {},
              }))
            : [],
        stickies: Array.isArray(parsed.stickies) ? parsed.stickies : [],
        tables: Array.isArray(parsed.tables)
            ? (parsed.tables || []).map((t) => ({ texts: {}, ...t }))
            : [],
        texts: Array.isArray(parsed.texts) ? parsed.texts : [],
        rosters: Array.isArray(parsed.rosters)
            ? parsed.rosters.map(normalizeRoster)
            : [],
        viewport:
            parsed.viewport && typeof parsed.viewport === "object"
                ? {
                      scale: Number(parsed.viewport.scale) || 1,
                      offsetX: Number(parsed.viewport.offsetX) || 0,
                      offsetY: Number(parsed.viewport.offsetY) || 0,
                  }
                : DEFAULT_VIEWPORT,
    };
}

const getDistance = (a, b) => Math.hypot(a.x - b.x, a.y - b.y);
const getMidpoint = (a, b) => ({ x: (a.x + b.x) / 2, y: (a.y + b.y) / 2 });

export default function ExcalidrawBoard({
    initialData = null,
    csrfToken = "",
    saveUrl = null,
}) {
    const containerRef = useRef(null);
    const canvasRef = useRef(null);
    const lastPointerRef = useRef(null);
    const pointersRef = useRef(new Map());
    const pinchRef = useRef(null);
    const strokesRef = useRef([]);
    const zCounterRef = useRef(1);
    const dragRef = useRef(null);
    const resizeRef = useRef(null);
    const viewportRef = useRef(DEFAULT_VIEWPORT);
    // Texto: el clic se guarda al pulsar y la caja se crea al soltar, para que
    // el foco que da el navegador al hacer clic no le quite el cursor al texto.
    const textDownRef = useRef(null);
    // Toque/arrastre sobre un texto ya colocado (distingue "editar" de "mover").
    const textTapRef = useRef(null);
    const lastTextTapRef = useRef({ id: null, time: 0 });

    const [strokes, setStrokes] = useState([]);
    const [currentStroke, setCurrentStroke] = useState(null);
    const [viewport, setViewport] = useState(DEFAULT_VIEWPORT);
    const [mode, setMode] = useState("draw");
    const [isSaving, setIsSaving] = useState(false);
    const [justSaved, setJustSaved] = useState(false);
    const [dragging, setDragging] = useState(false);
    const [canvasSize, setCanvasSize] = useState({ width: 0, height: 0 });

    const [drawingTool, setDrawingTool] = useState("pencil");
    const [brushColor, setBrushColor] = useState("#1A1D24");
    const [brushSize, setBrushSize] = useState(2.5);

    const [textFont, setTextFont] = useState("hand");
    const [textColor, setTextColor] = useState("#1A1D24");
    const [activeTextId, setActiveTextId] = useState(null);
    // Barra de ajustes del texto: solo aparece al hacer doble clic sobre uno.
    const [textToolsId, setTextToolsId] = useState(null);

    const [past, setPast] = useState([]);
    const [future, setFuture] = useState([]);

    // --- Elementos del lienzo ---
    const [calendars, setCalendars] = useState([]);
    const [stickies, setStickies] = useState([]);
    const [tables, setTables] = useState([]);
    const [texts, setTexts] = useState([]);
    const [rosters, setRosters] = useState([]);
    const [showCalendarPopup, setShowCalendarPopup] = useState(false);
    const [showTablePopup, setShowTablePopup] = useState(false);
    const [activeEvent, setActiveEvent] = useState(null); // { calId, day, month, year }
    const [activeShift, setActiveShift] = useState(null); // { rosterId, memberId, dayIndex }
    const [calendarWidthInput, setCalendarWidthInput] = useState(
        DEFAULT_CALENDAR_WIDTH,
    );
    const [tableRowsInput, setTableRowsInput] = useState(DEFAULT_TABLE_ROWS);
    const [tableColsInput, setTableColsInput] = useState(DEFAULT_TABLE_COLS);
    const [isExporting, setIsExporting] = useState(false);
    const [justExported, setJustExported] = useState(false);

    useEffect(() => {
        strokesRef.current = strokes;
    }, [strokes]);

    useEffect(() => {
        viewportRef.current = viewport;
    }, [viewport]);

    // Al pulsar en cualquier sitio fuera del texto que se está editando, se
    // quita el foco: el texto queda fijado en la pizarra (y si está vacío, se borra).
    useEffect(() => {
        const onPointerDownCapture = (e) => {
            const active = document.activeElement;
            if (!active || active.tagName !== "TEXTAREA") return;
            const wrap = active.closest?.("[data-text-wrapper]");
            if (!wrap || wrap.contains(e.target)) return;
            active.blur();
        };
        window.addEventListener("pointerdown", onPointerDownCapture, true);
        return () =>
            window.removeEventListener("pointerdown", onPointerDownCapture, true);
    }, []);

    useEffect(() => {
        const tool = DRAWING_TOOLS[drawingTool];
        if (tool && tool.defaultSize) {
            setBrushSize(tool.defaultSize);
        }
    }, [drawingTool]);

    const safeInitialData = useMemo(
        () => normalizeInitialData(initialData),
        [initialData],
    );

    useEffect(() => {
        const nextCalendars = safeInitialData.calendars || [];
        const nextStickies = safeInitialData.stickies || [];
        const nextTables = safeInitialData.tables || [];
        const nextTexts = safeInitialData.texts || [];
        const nextRosters = safeInitialData.rosters || [];

        setStrokes(safeInitialData.strokes || []);
        setCalendars(nextCalendars);
        setStickies(nextStickies);
        setTables(nextTables);
        setTexts(nextTexts);
        setRosters(nextRosters);
        setViewport(safeInitialData.viewport || DEFAULT_VIEWPORT);
        setPast([]);
        setFuture([]);

        // Continúa la numeración de capas donde se quedó el tablero guardado,
        // para que un elemento recién movido siempre quede por encima.
        const maxZ = [
            ...nextCalendars,
            ...nextStickies,
            ...nextTables,
            ...nextTexts,
            ...nextRosters,
        ].reduce((max, item) => Math.max(max, item.z || 0), 0);
        zCounterRef.current = maxZ + 1;
    }, [safeInitialData]);

    // Mide el contenedor y se adapta cuando cambia de tamaño.
    // Si por lo que sea el contenedor mide 0 (p.ej. el padre no le da
    // altura todavía), se usa un tamaño mínimo de seguridad para que el
    // lienzo nunca quede invisible.
    useEffect(() => {
        const el = containerRef.current;
        if (!el) return undefined;
        const updateSize = () => {
            const rect = el.getBoundingClientRect();
            const width = rect.width || el.clientWidth || 320;
            const height = rect.height || el.clientHeight || 480;
            setCanvasSize({ width, height });
        };
        updateSize();
        const observer = new ResizeObserver(updateSize);
        observer.observe(el);
        window.addEventListener("resize", updateSize);
        return () => {
            observer.disconnect();
            window.removeEventListener("resize", updateSize);
        };
    }, []);

    const commitStrokes = useCallback((nextStrokes) => {
        setPast((prev) => {
            const next = [...prev, strokesRef.current];
            return next.length > HISTORY_LIMIT
                ? next.slice(next.length - HISTORY_LIMIT)
                : next;
        });
        setFuture([]);
        setStrokes(nextStrokes);
    }, []);

    // Deshacer / rehacer: sin anidar setState dentro de otro setState, para
    // que el historial no se duplique en modo estricto de React.
    const undo = useCallback(() => {
        if (past.length === 0) return;
        const current = strokesRef.current;
        const previous = past[past.length - 1];
        setPast(past.slice(0, -1));
        setFuture((f) => [current, ...f]);
        setStrokes(previous);
    }, [past]);

    const redo = useCallback(() => {
        if (future.length === 0) return;
        const current = strokesRef.current;
        const [next, ...rest] = future;
        setPast((p) => [...p, current]);
        setFuture(rest);
        setStrokes(next);
    }, [future]);

    const clearDrawing = useCallback(() => {
        if (strokesRef.current.length === 0) return;
        commitStrokes([]);
    }, [commitStrokes]);

    // Atajos de teclado: Ctrl/Cmd+Z deshacer, Ctrl/Cmd+Shift+Z o Ctrl+Y rehacer.
    // Se ignoran mientras se escribe en un campo, para que Ctrl+Z deshaga el
    // texto y no el dibujo.
    useEffect(() => {
        const handler = (event) => {
            const meta = event.metaKey || event.ctrlKey;
            if (!meta) return;

            const t = event.target;
            if (
                t?.tagName === "INPUT" ||
                t?.tagName === "TEXTAREA" ||
                t?.tagName === "SELECT" ||
                t?.isContentEditable
            ) {
                return;
            }

            const key = event.key.toLowerCase();
            if (key === "z") {
                event.preventDefault();
                if (event.shiftKey) redo();
                else undo();
            } else if (key === "y") {
                event.preventDefault();
                redo();
            }
        };
        window.addEventListener("keydown", handler);
        return () => window.removeEventListener("keydown", handler);
    }, [undo, redo]);

    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        const { width, height } = canvasSize;
        if (!width || !height) return;

        const ctx = canvas.getContext("2d");
        const ratio = window.devicePixelRatio || 1;
        canvas.width = width * ratio;
        canvas.height = height * ratio;

        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        ctx.clearRect(0, 0, width, height);

        ctx.save();
        ctx.translate(viewport.offsetX, viewport.offsetY);
        ctx.scale(viewport.scale, viewport.scale);

        const drawStroke = (stroke) => {
            if (!stroke || !stroke.points || stroke.points.length === 0) return;
            ctx.globalAlpha = stroke.opacity ?? 1;
            ctx.lineWidth = stroke.size || 2.5;
            ctx.lineCap = "round";
            ctx.lineJoin = "round";

            if (stroke.tool === "eraser") {
                ctx.globalCompositeOperation = "destination-out";
                ctx.strokeStyle = "rgba(0,0,0,1)";
            } else {
                ctx.globalCompositeOperation = "source-over";
                ctx.strokeStyle = stroke.color || "#1A1D24";
            }

            const pts = stroke.points;
            ctx.beginPath();
            ctx.moveTo(pts[0].x, pts[0].y);
            for (let i = 1; i < pts.length - 1; i += 1) {
                const p1 = pts[i];
                const p2 = pts[i + 1];
                ctx.quadraticCurveTo(
                    p1.x,
                    p1.y,
                    (p1.x + p2.x) / 2,
                    (p1.y + p2.y) / 2,
                );
            }
            if (pts.length > 1)
                ctx.lineTo(pts[pts.length - 1].x, pts[pts.length - 1].y);
            ctx.stroke();

            // Se restauran los valores por defecto para no contaminar el resto del dibujo.
            ctx.globalAlpha = 1;
            ctx.globalCompositeOperation = "source-over";
        };

        strokes.forEach(drawStroke);
        if (currentStroke) drawStroke(currentStroke);

        ctx.restore();
    }, [strokes, currentStroke, viewport, canvasSize]);

    const getWorldPoint = useCallback(
        (clientX, clientY) => {
            const canvas = canvasRef.current;
            if (!canvas) return { x: 0, y: 0 };
            const rect = canvas.getBoundingClientRect();
            return {
                x: (clientX - rect.left - viewport.offsetX) / viewport.scale,
                y: (clientY - rect.top - viewport.offsetY) / viewport.scale,
            };
        },
        [viewport],
    );

    const saveBoard = useCallback(async () => {
        if (!saveUrl || isSaving) return;
        setIsSaving(true);
        try {
            const payload = {
                type: "board",
                version: 4,
                strokes: strokesRef.current,
                calendars,
                stickies,
                tables,
                texts,
                rosters,
                viewport,
            };
            const response = await fetch(saveUrl, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ content: JSON.stringify(payload) }),
            });
            if (!response.ok) {
                console.error("Board save failed", await response.text());
            } else {
                setJustSaved(true);
                setTimeout(() => setJustSaved(false), 1500);
            }
        } catch (error) {
            console.error("Board save error", error);
        } finally {
            setIsSaving(false);
        }
    }, [csrfToken, isSaving, saveUrl, viewport, calendars, stickies, tables, texts, rosters]);

    useEffect(() => {
        if (!saveUrl) return undefined;
        const timer = setTimeout(() => {
            void saveBoard();
        }, SAVE_DEBOUNCE_MS);
        return () => clearTimeout(timer);
    }, [strokes, calendars, viewport, stickies, tables, texts, rosters, saveUrl]);

    // Exporta el tablero COMPLETO como un archivo PNG: el dibujo del lienzo
    // más todo lo que hay "encima" (calendarios, notas, tablas, textos y
    // cuadrantes de turnos), que son elementos HTML normales y no forman
    // parte del <canvas> de los trazos. Para capturar ambas capas juntas se
    // usa html2canvas sobre el contenedor entero; los elementos puramente de
    // interfaz (barra de herramientas, popups, modales) se excluyen mediante
    // el atributo data-export-ignore="true" para que la imagen exportada
    // muestre solo el contenido de la pizarra, no los controles.
    const exportDrawing = useCallback(async () => {
        if (!containerRef.current || isExporting) return;
        setIsExporting(true);

        // Guarda qué overlays estaban abiertos y los cierra: no deben salir
        // en la imagen exportada.
        const prevState = {
            calendarPopup: showCalendarPopup,
            tablePopup: showTablePopup,
            event: activeEvent,
            shift: activeShift,
            textId: activeTextId,
            textTools: textToolsId,
        };
        setShowCalendarPopup(false);
        setShowTablePopup(false);
        setActiveEvent(null);
        setActiveShift(null);
        setActiveTextId(null);
        setTextToolsId(null);

        // Espera dos frames para que React aplique esos cambios al DOM real
        // antes de que html2canvas tome la "foto".
        await new Promise((resolve) =>
            requestAnimationFrame(() => requestAnimationFrame(resolve)),
        );

        try {
            let paperColor = "#F2EBDC";
            const computed = getComputedStyle(containerRef.current)
                .getPropertyValue("--paper")
                .trim();
            if (computed) paperColor = computed;

            const rendered = await html2canvas(containerRef.current, {
                backgroundColor: paperColor,
                useCORS: true,
                scale: Math.min(2, window.devicePixelRatio || 1.5),
                ignoreElements: (el) =>
                    Boolean(el.getAttribute && el.getAttribute("data-export-ignore") === "true"),
            });

            await new Promise((resolve) => {
                rendered.toBlob((blob) => {
                    if (blob) {
                        const url = URL.createObjectURL(blob);
                        const link = document.createElement("a");
                        const stamp = new Date().toISOString().slice(0, 10);
                        link.href = url;
                        link.download = `pizarra-${stamp}.png`;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);
                        setJustExported(true);
                        setTimeout(() => setJustExported(false), 1600);
                    }
                    resolve();
                }, "image/png");
            });
        } catch (error) {
            console.error("Board export error", error);
        } finally {
            setIsExporting(false);
            setShowCalendarPopup(prevState.calendarPopup);
            setShowTablePopup(prevState.tablePopup);
            setActiveEvent(prevState.event);
            setActiveShift(prevState.shift);
            setActiveTextId(prevState.textId);
            setTextToolsId(prevState.textTools);
        }
    }, [
        isExporting,
        showCalendarPopup,
        showTablePopup,
        activeEvent,
        activeShift,
        activeTextId,
        textToolsId,
    ]);

    const zoomBy = useCallback((factor) => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        const rect = canvas.getBoundingClientRect();
        const cx = rect.width / 2;
        const cy = rect.height / 2;
        setViewport((prev) => {
            const nextScale = clampZoom(prev.scale * factor);
            return {
                scale: nextScale,
                offsetX: cx - (cx - prev.offsetX) * (nextScale / prev.scale),
                offsetY: cy - (cy - prev.offsetY) * (nextScale / prev.scale),
            };
        });
    }, []);

    const resetView = useCallback(() => setViewport(DEFAULT_VIEWPORT), []);

    // Zoom con la rueda del ratón. Se registra a mano con { passive: false }
    // porque React registra onWheel como "passive" y entonces preventDefault()
    // no funciona (la página haría scroll a la vez que el zoom).
    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return undefined;
        const onWheel = (event) => {
            event.preventDefault();
            const factor = event.deltaY < 0 ? 1.12 : 0.88;
            const rect = canvas.getBoundingClientRect();
            const targetX = event.clientX - rect.left;
            const targetY = event.clientY - rect.top;
            setViewport((prev) => {
                const nextScale = clampZoom(prev.scale * factor);
                const ratio = nextScale / prev.scale;
                return {
                    scale: nextScale,
                    offsetX: targetX - (targetX - prev.offsetX) * ratio,
                    offsetY: targetY - (targetY - prev.offsetY) * ratio,
                };
            });
        };
        canvas.addEventListener("wheel", onWheel, { passive: false });
        return () => canvas.removeEventListener("wheel", onWheel);
    }, []);

    // Atajos de teclado "de herramienta", al estilo de las apps de dibujo
    // profesionales: una tecla cambia de herramienta al instante, sin pasar
    // por el ratón. Se ignoran mientras se escribe en cualquier campo (inputs,
    // textareas, selects) para no interferir con lo que el usuario está
    // tecleando, y también mientras hay un popup o un editor modal abierto.
    useEffect(() => {
        const TOOL_SHORTCUTS = {
            h: () => setMode("pan"),
            p: () => {
                setMode("draw");
                setDrawingTool("pencil");
            },
            b: () => {
                setMode("draw");
                setDrawingTool("pen");
            },
            m: () => {
                setMode("draw");
                setDrawingTool("marker");
            },
            g: () => {
                setMode("draw");
                setDrawingTool("eraser");
            },
            r: () => {
                setMode("draw");
                setDrawingTool("highlighter");
            },
            t: () => setMode("text"),
        };

        const handler = (event) => {
            if (event.metaKey || event.ctrlKey || event.altKey) return;

            const target = event.target;
            const tag = target?.tagName;
            const isEditableTarget =
                tag === "INPUT" ||
                tag === "TEXTAREA" ||
                tag === "SELECT" ||
                target?.isContentEditable;
            if (isEditableTarget) return;

            if (event.key === "Escape") {
                setShowCalendarPopup(false);
                setShowTablePopup(false);
                setActiveEvent(null);
                setActiveShift(null);
                setActiveTextId(null);
                setTextToolsId(null);
                return;
            }

            if (event.key === "+" || event.key === "=") {
                event.preventDefault();
                zoomBy(1.15);
                return;
            }
            if (event.key === "-" || event.key === "_") {
                event.preventDefault();
                zoomBy(0.85);
                return;
            }
            if (event.key === "0") {
                event.preventDefault();
                resetView();
                return;
            }

            const key = event.key.toLowerCase();
            const action = TOOL_SHORTCUTS[key];
            if (action) {
                event.preventDefault();
                action();
            }
        };

        window.addEventListener("keydown", handler);
        return () => window.removeEventListener("keydown", handler);
    }, [zoomBy, resetView]);

    const handlePointerDown = (event) => {
        canvasRef.current?.setPointerCapture?.(event.pointerId);
        pointersRef.current.set(event.pointerId, {
            x: event.clientX,
            y: event.clientY,
        });

        if (pointersRef.current.size === 2) {
            // Segundo dedo: empieza el pellizco y cancela cualquier trazo a medias.
            setCurrentStroke(null);
            textDownRef.current = null;
            const pts = Array.from(pointersRef.current.values());
            pinchRef.current = {
                startDistance: getDistance(pts[0], pts[1]) || 1,
                startScale: viewport.scale,
                startOffset: { x: viewport.offsetX, y: viewport.offsetY },
            };
            return;
        }
        if (pointersRef.current.size > 2) return;

        if (mode === "pan") {
            setDragging(true);
            lastPointerRef.current = { x: event.clientX, y: event.clientY };
            return;
        }

        if (mode === "text") {
            // La caja se crea al soltar (ver handlePointerUp).
            textDownRef.current = {
                pointerId: event.pointerId,
                x: event.clientX,
                y: event.clientY,
            };
            event.preventDefault();
            return;
        }

        const point = getWorldPoint(event.clientX, event.clientY);
        const tool = DRAWING_TOOLS[drawingTool];
        setCurrentStroke({
            id: Date.now() + Math.random(),
            points: [point],
            tool: drawingTool,
            color: brushColor,
            size: brushSize,
            opacity: tool?.opacity ?? 1,
        });
        event.preventDefault();
    };

    const handlePointerMove = (event) => {
        if (pointersRef.current.has(event.pointerId)) {
            pointersRef.current.set(event.pointerId, {
                x: event.clientX,
                y: event.clientY,
            });
        }

        if (pointersRef.current.size === 2 && pinchRef.current) {
            const pts = Array.from(pointersRef.current.values());
            const canvas = canvasRef.current;
            if (!canvas) return;
            const rect = canvas.getBoundingClientRect();
            const mid = getMidpoint(pts[0], pts[1]);
            const localMid = { x: mid.x - rect.left, y: mid.y - rect.top };
            const ratio =
                getDistance(pts[0], pts[1]) / pinchRef.current.startDistance;
            const nextScale = clampZoom(pinchRef.current.startScale * ratio);
            setViewport({
                scale: nextScale,
                offsetX:
                    localMid.x -
                    (localMid.x - pinchRef.current.startOffset.x) *
                        (nextScale / pinchRef.current.startScale),
                offsetY:
                    localMid.y -
                    (localMid.y - pinchRef.current.startOffset.y) *
                        (nextScale / pinchRef.current.startScale),
            });
            return;
        }

        if (mode === "pan" && dragging) {
            const last = lastPointerRef.current;
            if (!last) return;
            const dx = event.clientX - last.x;
            const dy = event.clientY - last.y;
            lastPointerRef.current = { x: event.clientX, y: event.clientY };
            setViewport((prev) => ({
                ...prev,
                offsetX: prev.offsetX + dx,
                offsetY: prev.offsetY + dy,
            }));
            return;
        }

        if (!currentStroke) return;
        const point = getWorldPoint(event.clientX, event.clientY);
        setCurrentStroke((prev) =>
            prev ? { ...prev, points: [...prev.points, point] } : prev,
        );
    };

    const handlePointerUp = (event) => {
        pointersRef.current.delete(event.pointerId);
        if (pointersRef.current.size < 2) pinchRef.current = null;

        if (mode === "text") {
            const down = textDownRef.current;
            textDownRef.current = null;
            // Solo un clic "limpio" (sin arrastrar) crea la caja de texto.
            if (
                event.type === "pointerup" &&
                down &&
                down.pointerId === event.pointerId &&
                Math.hypot(event.clientX - down.x, event.clientY - down.y) < 8
            ) {
                addTextBoxAt(getWorldPoint(event.clientX, event.clientY));
            }
            setDragging(false);
            lastPointerRef.current = null;
            return;
        }

        if (mode === "pan") {
            setDragging(false);
            lastPointerRef.current = null;
            return;
        }

        if (currentStroke && currentStroke.points.length > 0) {
            commitStrokes([...strokesRef.current, currentStroke]);
        }
        setCurrentStroke(null);
    };

    // --- Arrastre y redimensionado genéricos ---
    // Un único mecanismo, parametrizado por el "setter" de estado de cada
    // tipo de elemento (calendarios, notas, tablas, textos y cuadrantes),
    // hace el trabajo para todos: así es más fácil de mantener y ningún tipo
    // se desincroniza de otro.

    const bringToFront = useCallback((id, setter) => {
        zCounterRef.current += 1;
        const z = zCounterRef.current;
        setter((prev) => prev.map((it) => (it.id === id ? { ...it, z } : it)));
    }, []);

    const beginDrag = useCallback(
        (event, item, setter) => {
            event.stopPropagation();
            event.currentTarget.setPointerCapture?.(event.pointerId);
            dragRef.current = {
                id: item.id,
                pointerId: event.pointerId,
                startClientX: event.clientX,
                startClientY: event.clientY,
                startX: item.x,
                startY: item.y,
                setter,
            };
            bringToFront(item.id, setter);
        },
        [bringToFront],
    );

    const onDragMove = useCallback((event) => {
        const drag = dragRef.current;
        if (!drag || drag.pointerId !== event.pointerId) return;
        const scale = viewportRef.current.scale;
        const dx = (event.clientX - drag.startClientX) / scale;
        const dy = (event.clientY - drag.startClientY) / scale;
        drag.setter((prev) =>
            prev.map((it) =>
                it.id === drag.id
                    ? { ...it, x: drag.startX + dx, y: drag.startY + dy }
                    : it,
            ),
        );
    }, []);

    const onDragEnd = useCallback((event) => {
        if (dragRef.current?.pointerId === event.pointerId) {
            dragRef.current = null;
        }
    }, []);

    // Fin de un toque/arrastre sobre un texto colocado: si casi no se movió es
    // un toque (edita; dos toques seguidos abren los ajustes); si se movió, fue
    // un arrastre y el texto ya está en su nueva posición.
    const endTextDrag = useCallback(
        (event, itemId) => {
            const tap = textTapRef.current;
            textTapRef.current = null;
            onDragEnd(event);

            if (!tap || tap.pointerId !== event.pointerId || tap.id !== itemId) return;
            if (event.type !== "pointerup") return;
            if (Math.hypot(event.clientX - tap.x, event.clientY - tap.y) > 6) return;

            const now = Date.now();
            const last = lastTextTapRef.current;
            const isDouble = last.id === itemId && now - last.time < 350;
            lastTextTapRef.current = { id: itemId, time: now };

            // Foco síncrono dentro del gesto: abre el teclado en móvil
            event.currentTarget.querySelector("textarea")?.focus();
            if (isDouble) setTextToolsId(itemId);
        },
        [onDragEnd],
    );

    const beginResize = useCallback((event, item, setter, options) => {
        event.stopPropagation();
        event.currentTarget.setPointerCapture?.(event.pointerId);
        resizeRef.current = {
            id: item.id,
            pointerId: event.pointerId,
            startClientX: event.clientX,
            startClientY: event.clientY,
            startWidth: item.width,
            startHeight: item.height,
            setter,
            options: options || {},
        };
    }, []);

    const onResizeMove = useCallback((event) => {
        const resize = resizeRef.current;
        if (!resize || resize.pointerId !== event.pointerId) return;
        const scale = viewportRef.current.scale;
        const dx = (event.clientX - resize.startClientX) / scale;
        const dy = (event.clientY - resize.startClientY) / scale;
        const {
            minWidth = 50,
            maxWidth = Infinity,
            minHeight = 50,
            maxHeight = Infinity,
            widthOnly = false,
        } = resize.options;

        resize.setter((prev) =>
            prev.map((it) => {
                if (it.id !== resize.id) return it;
                const nextWidth = clamp(resize.startWidth + dx, minWidth, maxWidth);
                if (widthOnly) return { ...it, width: nextWidth };
                const nextHeight = clamp(
                    resize.startHeight + dy,
                    minHeight,
                    maxHeight,
                );
                return { ...it, width: nextWidth, height: nextHeight };
            }),
        );
    }, []);

    const onResizeEnd = useCallback((event) => {
        if (resizeRef.current?.pointerId === event.pointerId) {
            resizeRef.current = null;
        }
    }, []);

    // --- Lógica de las cajas de calendario, notas, tablas, texto y cuadrantes ---

    const addCalendarBox = useCallback(() => {
        const canvas = canvasRef.current;
        const rect = canvas
            ? canvas.getBoundingClientRect()
            : { width: 600, height: 400 };
        const centerScreen = { x: rect.width / 2, y: rect.height / 2 };
        const worldX = (centerScreen.x - viewport.offsetX) / viewport.scale;
        const worldY = (centerScreen.y - viewport.offsetY) / viewport.scale;

        const width = clamp(
            Number(calendarWidthInput) || DEFAULT_CALENDAR_WIDTH,
            MIN_CALENDAR_WIDTH,
            MAX_CALENDAR_WIDTH,
        );
        const today = new Date();

        zCounterRef.current += 1;
        setCalendars((prev) => [
            ...prev,
            {
                id: `cal-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`,
                x: worldX - width / 2,
                y: worldY - width * 0.55,
                width,
                month: today.getMonth(),
                year: today.getFullYear(),
                events: {},
                z: zCounterRef.current,
            },
        ]);
        setShowCalendarPopup(false);
    }, [calendarWidthInput, viewport]);

    const addSticky = useCallback(() => {
        const canvas = canvasRef.current;
        const rect = canvas
            ? canvas.getBoundingClientRect()
            : { width: 400, height: 500 };
        const centerScreen = { x: rect.width / 2, y: rect.height / 2 };
        const worldX = (centerScreen.x - viewport.offsetX) / viewport.scale;
        const worldY = (centerScreen.y - viewport.offsetY) / viewport.scale;

        zCounterRef.current += 1;
        setStickies((prev) => [
            ...prev,
            {
                id: `sticky-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`,
                x: worldX - DEFAULT_STICKY_SIZE / 2,
                y: worldY - DEFAULT_STICKY_SIZE / 2,
                width: DEFAULT_STICKY_SIZE,
                height: DEFAULT_STICKY_SIZE,
                color: "#FEE440",
                text: "",
                rotation: Number((Math.random() * 5 - 2.5).toFixed(2)),
                z: zCounterRef.current,
            },
        ]);
    }, [viewport]);

    const updateSticky = useCallback((id, updates) => {
        setStickies((prev) =>
            prev.map((s) => (s.id === id ? { ...s, ...updates } : s)),
        );
    }, []);

    const removeSticky = useCallback((id) => {
        setStickies((prev) => prev.filter((s) => s.id !== id));
    }, []);

    const removeCalendar = useCallback((id) => {
        setCalendars((prev) => prev.filter((c) => c.id !== id));
    }, []);

    // Crea texto "de verdad" directamente sobre la pizarra: sin caja ni barra
    // de controles, solo el cursor parpadeante justo donde se ha hecho clic,
    // con la fuente/color de la barra de herramientas. Enter o tocar fuera lo
    // fija; si se deja vacío, se borra solo. flushSync renderiza la caja al
    // instante para poder darle el foco dentro del mismo gesto del usuario
    // (así el teclado del móvil sí se abre).
    const addTextBoxAt = useCallback(
        (point) => {
            zCounterRef.current += 1;
            const id = `text-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`;
            const z = zCounterRef.current;

            flushSync(() => {
                setTexts((prev) => [
                    ...prev,
                    {
                        id,
                        // El cursor queda en el punto exacto del clic.
                        x: point.x,
                        y: point.y - (DEFAULT_TEXT_SIZE * TEXT_LINE_HEIGHT) / 2,
                        text: "",
                        font: textFont,
                        color: textColor,
                        size: DEFAULT_TEXT_SIZE,
                        z,
                    },
                ]);
                setActiveTextId(id);
                setTextToolsId(null);
                // Vuelve a la herramienta de dibujo tras colocar el texto para no
                // crear cajas nuevas accidentalmente al seguir tocando el lienzo.
                setMode("draw");
            });

            const el = containerRef.current?.querySelector(
                `[data-text-id="${id}"] textarea`,
            );
            el?.focus();
            // Por si el navegador ignora el primer foco
            requestAnimationFrame(() => {
                if (el && document.activeElement !== el) el.focus();
            });
        },
        [textFont, textColor],
    );

    const updateText = useCallback((id, updates) => {
        setTexts((prev) => prev.map((t) => (t.id === id ? { ...t, ...updates } : t)));
    }, []);

    const removeText = useCallback((id) => {
        setTexts((prev) => prev.filter((t) => t.id !== id));
        setActiveTextId((prev) => (prev === id ? null : prev));
        setTextToolsId((prev) => (prev === id ? null : prev));
    }, []);

    const addTable = useCallback(() => {
        const canvas = canvasRef.current;
        const rect = canvas
            ? canvas.getBoundingClientRect()
            : { width: 400, height: 500 };
        const centerScreen = { x: rect.width / 2, y: rect.height / 2 };
        const worldX = (centerScreen.x - viewport.offsetX) / viewport.scale;
        const worldY = (centerScreen.y - viewport.offsetY) / viewport.scale;
        const rows = clamp(Number(tableRowsInput) || DEFAULT_TABLE_ROWS, 1, 12);
        const cols = clamp(Number(tableColsInput) || DEFAULT_TABLE_COLS, 1, 12);
        const width = Math.max(MIN_TABLE_WIDTH, cols * 68);
        const height = Math.max(MIN_TABLE_HEIGHT, rows * 34 + 74);

        zCounterRef.current += 1;
        setTables((prev) => [
            ...prev,
            {
                id: `table-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`,
                x: worldX - width / 2,
                y: worldY - height / 2,
                width,
                height,
                rows,
                cols,
                fills: {},
                texts: {},
                selectedCell: null,
                z: zCounterRef.current,
            },
        ]);
        setShowTablePopup(false);
    }, [tableColsInput, tableRowsInput, viewport]);

    const updateTable = useCallback((id, updates) => {
        setTables((prev) =>
            prev.map((table) => (table.id === id ? { ...table, ...updates } : table)),
        );
    }, []);

    const removeTable = useCallback((id) => {
        setTables((prev) => prev.filter((table) => table.id !== id));
    }, []);

    const selectTableCell = useCallback(
        (table, row, col) => {
            bringToFront(table.id, setTables);
            updateTable(table.id, { selectedCell: { row, col } });
        },
        [bringToFront, updateTable],
    );

    const paintTableCell = useCallback((tableId, row, col, color) => {
        setTables((prev) =>
            prev.map((table) => {
                if (table.id !== tableId) return table;
                const fillKey = `${row}-${col}`;
                const nextFills = { ...table.fills };
                if (color) nextFills[fillKey] = color;
                else delete nextFills[fillKey];
                return {
                    ...table,
                    fills: nextFills,
                    selectedCell: { row, col },
                };
            }),
        );
    }, []);

    // Actualiza el texto escrito dentro de una celda de tabla.
    const writeTableCellText = useCallback((tableId, row, col, value) => {
        setTables((prev) =>
            prev.map((table) => {
                if (table.id !== tableId) return table;
                const key = `${row}-${col}`;
                const nextTexts = { ...(table.texts || {}) };
                if (value) nextTexts[key] = value;
                else delete nextTexts[key];
                return { ...table, texts: nextTexts };
            }),
        );
    }, []);

    const updateCalendarEvent = useCallback((calId, day, month, year, updates) => {
        const eventKey = `${year}-${month}-${day}`;
        setCalendars((prev) =>
            prev.map((cal) => {
                if (cal.id !== calId) return cal;
                const currentEvent = cal.events[eventKey] || { text: "", color: "#FEE440" };
                return {
                    ...cal,
                    events: {
                        ...cal.events,
                        [eventKey]: { ...currentEvent, ...updates },
                    },
                };
            }),
        );
    }, []);

    const shiftCalendarMonth = useCallback((id, delta) => {
        setCalendars((prev) =>
            prev.map((c) => {
                if (c.id !== id) return c;
                let month = c.month + delta;
                let year = c.year;
                if (month < 0) {
                    month = 11;
                    year -= 1;
                } else if (month > 11) {
                    month = 0;
                    year += 1;
                }
                return { ...c, month, year };
            }),
        );
    }, []);

    // --- Cuadrante de horario (roster) ---
    // Toda la lógica de filas, semanas y turnos vive en funciones puras
    // (addRosterMember, shiftRosterWeek, applyShiftUpdate…) que se aplican
    // con mutateRoster, así que aquí solo hacen falta estas funciones.

    const addRoster = useCallback(() => {
        const canvas = canvasRef.current;
        const rect = canvas
            ? canvas.getBoundingClientRect()
            : { width: 600, height: 500 };
        const centerScreen = { x: rect.width / 2, y: rect.height / 2 };
        const worldX = (centerScreen.x - viewport.offsetX) / viewport.scale;
        const worldY = (centerScreen.y - viewport.offsetY) / viewport.scale;

        zCounterRef.current += 1;
        const z = zCounterRef.current;
        setRosters((prev) => [...prev, createRoster({ x: worldX, y: worldY, z })]);
    }, [viewport]);

    const mutateRoster = useCallback((id, fn) => {
        setRosters((prev) => prev.map((r) => (r.id === id ? fn(r) : r)));
    }, []);

    const removeRoster = useCallback((id) => {
        setRosters((prev) => prev.filter((r) => r.id !== id));
        setActiveShift((prev) => (prev && prev.rosterId === id ? null : prev));
    }, []);

    // Botones de la barra de herramientas: fondo/color solo se fijan en línea
    // cuando la herramienta está activa. Cuando no lo está, se deja que la
    // clase "ebx-btn" (hover incluido) controle el aspecto, así el hover
    // definido en CSS no compite con estilos en línea.
    const iconButtonStyle = (active) => ({
        border: "none",
        background: active ? "var(--ink, #1A1D24)" : undefined,
        color: active ? "var(--paper, #F2EBDC)" : "var(--ink, #1A1D24)",
        padding: "9px",
        cursor: "pointer",
        borderRadius: 12,
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        flexShrink: 0,
        boxShadow: active ? "0 3px 8px rgba(26,29,36,0.28)" : "none",
        transition: "background 0.16s ease, color 0.16s ease, transform 0.08s ease, box-shadow 0.16s ease",
    });

    const iconButtonClass = (active) =>
        `ebx-btn${active ? " ebx-btn-active" : ""}`;

    const divider = (
        <div
            style={{
                width: 1,
                height: 24,
                background: "var(--rule, #e5ded0)",
                margin: "0 6px",
                flexShrink: 0,
            }}
        />
    );

    // Estilo base compartido por calendarios, notas y tablas para
    // que se sientan parte de la misma familia visual: bordes finos, esquinas
    // suaves y una sombra de dos capas (contacto + elevación).
    const cardShadow = "0 1px 2px rgba(26,29,36,0.08), 0 10px 26px rgba(26,29,36,0.14)";
    const cardShadowLift = "0 2px 4px rgba(26,29,36,0.10), 0 16px 34px rgba(26,29,36,0.18)";
    const cardBorder = "1px solid rgba(26,29,36,0.14)";
    const cardRadius = 16;

    // Cuadrícula de fondo: acompaña al zoom y al desplazamiento del lienzo.
    // Si se aleja mucho se duplica el paso para que no se vuelva ilegible.
    let gridSize = BASE_GRID_SIZE * viewport.scale;
    while (gridSize < MIN_GRID_SIZE) gridSize *= 2;

    return (
        <div
            ref={containerRef}
            style={{
                height: "100%",
                width: "100%",
                minHeight: "860px",
                position: "relative",
                fontFamily: "var(--font-body, sans-serif)",
                backgroundColor: "var(--paper, #F2EBDC)",
                backgroundImage: `
          linear-gradient(to right, var(--rule-faint, rgba(26,29,36,0.09)) 1px, transparent 1px),
          linear-gradient(to bottom, var(--rule-faint, rgba(26,29,36,0.09)) 1px, transparent 1px)
        `,
                backgroundSize: `${gridSize}px ${gridSize}px`,
                backgroundPosition: `${viewport.offsetX}px ${viewport.offsetY}px`,
                color: "var(--ink, #1A1D24)",
                overflow: "hidden",
            }}
        >
            {/* Estilos utilitarios de la pizarra: hover/focus consistentes en
                toda la toolbar y en los controles de las tarjetas, sin tocar
                ningún color ni fondo existente. */}
            <style>{`
                .ebx-btn { background: transparent; }
                .ebx-btn:hover { background: rgba(26,29,36,0.08); }
                .ebx-btn:active { transform: scale(0.93); }
                .ebx-btn:focus-visible { outline: 2px solid var(--ink, #1A1D24); outline-offset: 1px; }
                .ebx-btn-active:hover { background: var(--ink, #1A1D24); filter: brightness(1.08); }
                .ebx-btn:disabled { cursor: not-allowed; }
                .ebx-btn:disabled:hover { background: transparent; }
                .ebx-swatch { transition: transform 0.12s ease, box-shadow 0.12s ease; }
                .ebx-swatch:hover { transform: scale(1.18); }
                .ebx-swatch:active { transform: scale(0.94); }
                .ebx-popup-btn:hover { filter: brightness(1.12); }
                .ebx-card { transition: box-shadow 0.18s ease, transform 0.18s ease; }
                .ebx-card:hover { box-shadow: 0 2px 4px rgba(26,29,36,0.10), 0 16px 34px rgba(26,29,36,0.18); }

                /* Toolbar: una sola fila con scroll horizontal en pantallas pequeñas */
                .ebx-toolbar { scrollbar-width: none; }
                .ebx-toolbar::-webkit-scrollbar { display: none; }
                .ebx-toolbar > * { flex-shrink: 0; }

                /* Texto sobre la pizarra: se mueve pulsando y arrastrando */
                .ebx-text-wrap { -webkit-user-select: none; user-select: none; -webkit-touch-callout: none; }
                .ebx-text-wrap:not(.is-editing):hover { outline: 1px dashed rgba(26,29,36,0.3); outline-offset: 2px; }
                .ebx-text-wrap.is-editing { outline: 1px dashed rgba(26,29,36,0.5); outline-offset: 2px; }

                /* Texto directo sobre la pizarra: sin caja ni borde. El <textarea>
                   se superpone a una copia invisible del texto, así que crece solo
                   (ancho y alto) mientras se escribe, como texto normal. */
                .ebx-text-item { display: inline-grid; }
                .ebx-text-item::after {
                    content: attr(data-value) " ";
                    visibility: hidden;
                    white-space: pre-wrap;
                    grid-area: 1 / 1 / 2 / 2;
                    padding: 0 2px;
                    min-width: 2px;
                    pointer-events: none;
                }
                .ebx-text-item > textarea {
                    grid-area: 1 / 1 / 2 / 2;
                    width: 100%;
                    min-width: 0;
                    margin: 0;
                    padding: 0 2px;
                    border: none;
                    outline: none;
                    background: transparent;
                    resize: none;
                    overflow: hidden;
                    white-space: pre-wrap;
                    font: inherit;
                    letter-spacing: inherit;
                    line-height: inherit;
                    color: inherit;
                    caret-color: currentColor;
                    cursor: text;
                    user-select: text;
                    -webkit-user-select: text;
                }
            `}</style>

            {/* Lienzo de trazos: transparente, por encima de la cuadrícula.
                La goma solo borra trazos; la cuadrícula (fondo del contenedor)
                queda intacta. */}
            <canvas
                ref={canvasRef}
                onPointerDown={handlePointerDown}
                onPointerMove={handlePointerMove}
                onPointerUp={handlePointerUp}
                onPointerLeave={handlePointerUp}
                onPointerCancel={handlePointerUp}
                style={{
                    position: "absolute",
                    inset: 0,
                    width: "100%",
                    height: "100%",
                    display: "block",
                    cursor:
                        mode === "pan"
                            ? dragging
                                ? "grabbing"
                                : "grab"
                            : mode === "text"
                                ? "text"
                                : "crosshair",
                    touchAction: "none",
                }}
            />

            {/* Capa de elementos HTML (calendarios, notas, tablas, textos y
                cuadrantes). Sigue el mismo zoom y desplazamiento que el lienzo. */}
            <div
                style={{
                    position: "absolute",
                    inset: 0,
                    pointerEvents: "none",
                    transform: `translate(${viewport.offsetX}px, ${viewport.offsetY}px) scale(${viewport.scale})`,
                    transformOrigin: "0 0",
                }}
            >
                {texts.map((item) => {
                    const fontOption =
                        TEXT_FONT_OPTIONS.find((f) => f.id === item.font) || TEXT_FONT_OPTIONS[0];
                    const isActive = activeTextId === item.id;
                    const showTools = textToolsId === item.id;
                    return (
                        <div
                            key={item.id}
                            data-text-id={item.id}
                            data-text-wrapper="true"
                            className={`ebx-text-wrap${isActive ? " is-editing" : ""}`}
                            style={{
                                position: "absolute",
                                // El padding da zona de agarre; el offset compensa para
                                // que el texto quede justo donde se hizo clic.
                                left: item.x - 6,
                                top: item.y - 4,
                                padding: "4px 6px",
                                zIndex: isActive ? 9999 : item.z || 0,
                                pointerEvents: "auto",
                                cursor: isActive ? "text" : "grab",
                                touchAction: isActive ? "auto" : "none",
                            }}
                            onPointerDown={(e) => {
                                // Ajustes y cursor dentro del texto: comportamiento normal
                                if (e.target.closest?.("[data-text-tools]")) return;
                                if (e.target.tagName === "TEXTAREA") return;
                                // Pulsar y arrastrar el texto (ratón o dedo) lo mueve
                                e.preventDefault();
                                textTapRef.current = {
                                    id: item.id,
                                    pointerId: e.pointerId,
                                    x: e.clientX,
                                    y: e.clientY,
                                };
                                beginDrag(e, item, setTexts);
                            }}
                            onPointerMove={onDragMove}
                            onPointerUp={(e) => endTextDrag(e, item.id)}
                            onPointerCancel={onDragEnd}
                            onFocus={() => setActiveTextId(item.id)}
                            onBlur={(e) => {
                                if (e.currentTarget.contains(e.relatedTarget)) return;
                                setActiveTextId((prev) => (prev === item.id ? null : prev));
                                setTextToolsId((prev) => (prev === item.id ? null : prev));
                                // Un texto vacío no deja nada en la pizarra.
                                if (!item.text.trim()) removeText(item.id);
                            }}
                            onDoubleClick={() => setTextToolsId(item.id)}
                        >
                            {/* Ajustes (mover / fuente / color / tamaño / eliminar):
                                solo con doble clic o doble toque sobre un texto. */}
                            {showTools && (
                                <div
                                    data-export-ignore="true"
                                    data-text-tools="true"
                                    style={{
                                        display: "flex",
                                        alignItems: "center",
                                        gap: 4,
                                        marginBottom: 4,
                                        background: "var(--paper, #F2EBDC)",
                                        border: "1px solid var(--rule, #e5ded0)",
                                        borderRadius: 10,
                                        padding: "4px 5px",
                                        boxShadow: cardShadow,
                                        width: "fit-content",
                                        fontFamily: "var(--font-body, sans-serif)",
                                        cursor: "default",
                                    }}
                                >
                                    <div
                                        onMouseDown={(e) => e.preventDefault()}
                                        onPointerDown={(e) => {
                                            e.preventDefault();
                                            beginDrag(e, item, setTexts);
                                        }}
                                        onPointerMove={onDragMove}
                                        onPointerUp={onDragEnd}
                                        onPointerCancel={onDragEnd}
                                        title="Mover"
                                        style={{
                                            cursor: "grab",
                                            touchAction: "none",
                                            background: "var(--ink, #1A1D24)",
                                            color: "var(--paper, #F2EBDC)",
                                            borderRadius: 8,
                                            padding: "6px 11px",
                                            minWidth: 34,
                                            minHeight: 26,
                                            boxSizing: "border-box",
                                            display: "flex",
                                            alignItems: "center",
                                            justifyContent: "center",
                                        }}
                                    >
                                        <Move size={14} />
                                    </div>
                                    <select
                                        value={item.font}
                                        onChange={(e) => updateText(item.id, { font: e.target.value })}
                                        style={{
                                            fontSize: 11,
                                            borderRadius: 6,
                                            border: "1px solid var(--rule, #e5ded0)",
                                            background: "var(--paper, #F2EBDC)",
                                            color: "var(--ink, #1A1D24)",
                                            padding: "3px 4px",
                                            cursor: "pointer",
                                        }}
                                    >
                                        {TEXT_FONT_OPTIONS.map((f) => (
                                            <option key={f.id} value={f.id}>
                                                {f.label}
                                            </option>
                                        ))}
                                    </select>
                                    <input
                                        type="color"
                                        value={item.color}
                                        onChange={(e) => updateText(item.id, { color: e.target.value })}
                                        style={{ width: 22, height: 22, padding: 0, border: "none", cursor: "pointer" }}
                                    />
                                    <input
                                        type="number"
                                        min={10}
                                        max={72}
                                        value={item.size}
                                        onChange={(e) =>
                                            updateText(item.id, { size: clamp(Number(e.target.value) || 20, 10, 72) })
                                        }
                                        style={{
                                            width: 42,
                                            fontSize: 11,
                                            borderRadius: 6,
                                            border: "1px solid var(--rule, #e5ded0)",
                                            padding: "3px 4px",
                                        }}
                                    />
                                    <button
                                        type="button"
                                        onClick={() => removeText(item.id)}
                                        title="Eliminar texto"
                                        style={{
                                            border: "none",
                                            background: "transparent",
                                            cursor: "pointer",
                                            color: "var(--pencil, #6B6660)",
                                            padding: 4,
                                            display: "flex",
                                            alignItems: "center",
                                        }}
                                    >
                                        <X size={14} />
                                    </button>
                                </div>
                            )}
                            <div
                                className="ebx-text-item"
                                data-value={item.text}
                                style={{
                                    fontFamily: fontOption.family,
                                    fontSize: item.size,
                                    lineHeight: TEXT_LINE_HEIGHT,
                                    color: item.color,
                                }}
                            >
                                <textarea
                                    value={item.text}
                                    onChange={(e) => updateText(item.id, { text: e.target.value })}
                                    onFocus={() => bringToFront(item.id, setTexts)}
                                    onKeyDown={(e) => {
                                        if (e.nativeEvent.isComposing) return;
                                        // Enter o Escape: fija el texto. Shift+Enter: salto de línea.
                                        if (e.key === "Escape" || (e.key === "Enter" && !e.shiftKey)) {
                                            e.preventDefault();
                                            e.currentTarget.blur();
                                        }
                                    }}
                                    rows={1}
                                    spellCheck={false}
                                    autoComplete="off"
                                    enterKeyHint="done"
                                    style={{ pointerEvents: isActive ? "auto" : "none" }}
                                />
                            </div>
                        </div>
                    );
                })}

                {tables.map((table) => (
                    <div
                        key={table.id}
                        className="ebx-card"
                        style={{
                            position: "absolute",
                            left: table.x,
                            top: table.y,
                            width: table.width,
                            height: table.height,
                            zIndex: table.z || 0,
                            pointerEvents: "auto",
                            background: "var(--paper, #F2EBDC)",
                            border: cardBorder,
                            borderRadius: cardRadius,
                            boxShadow: cardShadow,
                            fontFamily: "var(--font-hand, sans-serif)",
                            userSelect: "none",
                        }}
                    >
                        {/* Cabecera (zona de arrastre, más grande) */}
                        <div
                            onPointerDown={(e) => beginDrag(e, table, setTables)}
                            onPointerMove={onDragMove}
                            onPointerUp={onDragEnd}
                            onPointerCancel={onDragEnd}
                            style={{
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "space-between",
                                padding: "14px 12px 12px",
                                minHeight: 46,
                                boxSizing: "border-box",
                                borderBottom: cardBorder,
                                borderRadius: `${cardRadius - 1}px ${cardRadius - 1}px 0 0`,
                                cursor: "grab",
                                touchAction: "none",
                                background: "rgba(26,29,36,0.03)",
                            }}
                        >
                            <span style={{ fontSize: 12, fontWeight: 700, letterSpacing: "0.04em", textTransform: "uppercase" }}>
                                Tabla
                            </span>
                            <button
                                type="button"
                                className="ebx-btn"
                                onPointerDown={(e) => e.stopPropagation()}
                                onClick={() => removeTable(table.id)}
                                title="Eliminar tabla"
                                style={{
                                    border: "none",
                                    background: "transparent",
                                    cursor: "pointer",
                                    padding: 4,
                                    borderRadius: 8,
                                    color: "var(--pencil, #6B6660)",
                                    display: "flex",
                                    alignItems: "center",
                                }}
                            >
                                <X size={12} />
                            </button>
                        </div>

                        <div style={{ display: "flex", alignItems: "center", gap: 6, padding: "8px 8px 0", flexWrap: "wrap" }}>
                            <span style={{ fontSize: 10, color: "var(--pencil, #6B6660)", marginRight: 2 }}>
                                {table.selectedCell ? "Celda:" : "Elige celda"}
                            </span>
                            {TABLE_FILL_PALETTE.map((color) => (
                                <button
                                    key={color}
                                    type="button"
                                    className="ebx-swatch"
                                    onClick={() => {
                                        if (table.selectedCell) {
                                            paintTableCell(table.id, table.selectedCell.row, table.selectedCell.col, color);
                                        }
                                    }}
                                    title="Pintar celda seleccionada"
                                    style={{
                                        width: 14,
                                        height: 14,
                                        borderRadius: "50%",
                                        background: color,
                                        border: "1px solid rgba(0,0,0,0.2)",
                                        cursor: "pointer",
                                        padding: 0,
                                        opacity: table.selectedCell ? 1 : 0.45,
                                    }}
                                />
                            ))}
                            <button
                                type="button"
                                className="ebx-swatch"
                                onClick={() => {
                                    if (table.selectedCell) {
                                        paintTableCell(table.id, table.selectedCell.row, table.selectedCell.col, null);
                                    }
                                }}
                                title="Quitar color de la celda"
                                style={{
                                    width: 16,
                                    height: 16,
                                    borderRadius: "50%",
                                    background: "transparent",
                                    border: "1px solid rgba(0,0,0,0.3)",
                                    cursor: "pointer",
                                    padding: 0,
                                    display: "flex",
                                    alignItems: "center",
                                    justifyContent: "center",
                                    color: "var(--pencil, #6B6660)",
                                    opacity: table.selectedCell ? 1 : 0.45,
                                }}
                            >
                                <Ban size={10} />
                            </button>
                        </div>

                        <div
                            style={{
                                position: "relative",
                                display: "grid",
                                gridTemplateColumns: `repeat(${table.cols}, minmax(0, 1fr))`,
                                gridTemplateRows: `repeat(${table.rows}, minmax(0, 1fr))`,
                                gap: 1,
                                padding: 8,
                                height: `calc(100% - 96px)`,
                                boxSizing: "border-box",
                            }}
                        >
                            {Array.from({ length: table.rows * table.cols }, (_, index) => {
                                const row = Math.floor(index / table.cols);
                                const col = index % table.cols;
                                const key = `${row}-${col}`;
                                const isSelected = table.selectedCell && table.selectedCell.row === row && table.selectedCell.col === col;
                                const fill = table.fills?.[key] || "transparent";
                                const cellText = table.texts?.[key] || "";

                                return (
                                    <div
                                        key={key}
                                        onClick={() => selectTableCell(table, row, col)}
                                        style={{
                                            border: isSelected ? "2px solid var(--ink, #1A1D24)" : "1px solid rgba(26,29,36,0.15)",
                                            background: fill,
                                            borderRadius: 6,
                                            cursor: "text",
                                            minHeight: 0,
                                            display: "flex",
                                        }}
                                    >
                                        <input
                                            value={cellText}
                                            onChange={(e) => writeTableCellText(table.id, row, col, e.target.value)}
                                            onFocus={() => selectTableCell(table, row, col)}
                                            onPointerDown={(e) => e.stopPropagation()}
                                            style={{
                                                width: "100%",
                                                background: "transparent",
                                                border: "none",
                                                outline: "none",
                                                fontSize: 11,
                                                textAlign: "center",
                                                fontFamily: "inherit",
                                                color: "var(--ink, #1A1D24)",
                                                padding: "0 2px",
                                            }}
                                        />
                                    </div>
                                );
                            })}
                        </div>

                        {/* Asa de redimensionado (grande) */}
                        <div
                            data-export-ignore="true"
                            onPointerDown={(e) =>
                                beginResize(e, table, setTables, {
                                    minWidth: MIN_TABLE_WIDTH,
                                    minHeight: MIN_TABLE_HEIGHT,
                                })
                            }
                            onPointerMove={onResizeMove}
                            onPointerUp={onResizeEnd}
                            onPointerCancel={onResizeEnd}
                            title="Arrastra para cambiar el tamaño"
                            style={{
                                position: "absolute",
                                right: -10,
                                bottom: -10,
                                width: 24,
                                height: 24,
                                borderRadius: "50%",
                                background: "var(--ink, #1A1D24)",
                                opacity: 0.85,
                                cursor: "nwse-resize",
                                touchAction: "none",
                                boxShadow: HANDLE_SHADOW,
                            }}
                        />
                    </div>
                ))}

                {stickies.map((sticky) => (
                    <div
                        key={sticky.id}
                        className="ebx-card"
                        style={{
                            position: "absolute",
                            left: sticky.x,
                            top: sticky.y,
                            width: sticky.width,
                            height: sticky.height,
                            zIndex: sticky.z || 0,
                            pointerEvents: "auto",
                            background: sticky.color,
                            border: "1px solid rgba(0,0,0,0.08)",
                            borderRadius: 10,
                            boxShadow: "0 1px 2px rgba(0,0,0,0.08), 0 10px 22px rgba(0,0,0,0.16)",
                            padding: "8px",
                            display: "flex",
                            flexDirection: "column",
                            fontFamily: "var(--font-hand, sans-serif)",
                            userSelect: "none",
                            transform: `rotate(${sticky.rotation || 0}deg)`,
                        }}
                    >
                        {/* Cabecera: arrastre + color + eliminar (zona de arrastre más grande) */}
                        <div
                            onPointerDown={(e) => beginDrag(e, sticky, setStickies)}
                            onPointerMove={onDragMove}
                            onPointerUp={onDragEnd}
                            onPointerCancel={onDragEnd}
                            style={{
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "space-between",
                                cursor: "grab",
                                touchAction: "none",
                                marginBottom: 6,
                                padding: "6px 4px",
                                minHeight: 30,
                                boxSizing: "border-box",
                            }}
                        >
                            <div style={{ display: "flex", gap: 6 }}>
                                {STICKY_PALETTE.map((c) => (
                                    <div
                                        key={c}
                                        className="ebx-swatch"
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            updateSticky(sticky.id, { color: c });
                                        }}
                                        onPointerDown={(e) => e.stopPropagation()}
                                        style={{
                                            width: 14,
                                            height: 14,
                                            borderRadius: "50%",
                                            background: c,
                                            cursor: "pointer",
                                            border: sticky.color === c ? "1px solid black" : "1px solid rgba(0,0,0,0.2)",
                                        }}
                                    />
                                ))}
                            </div>
                            <button
                                type="button"
                                className="ebx-btn"
                                onClick={() => removeSticky(sticky.id)}
                                onPointerDown={(e) => e.stopPropagation()}
                                title="Eliminar nota"
                                style={{
                                    border: "none",
                                    background: "transparent",
                                    cursor: "pointer",
                                    color: "rgba(0,0,0,0.5)",
                                    padding: 5,
                                    borderRadius: 7,
                                    display: "flex",
                                    alignItems: "center",
                                }}
                            >
                                <X size={14} />
                            </button>
                        </div>

                        {/* Contenido: texto editable */}
                        <textarea
                            value={sticky.text}
                            onChange={(e) => updateSticky(sticky.id, { text: e.target.value })}
                            onPointerDown={(e) => e.stopPropagation()}
                            placeholder="Escribe aquí…"
                            style={{
                                flex: 1,
                                background: "transparent",
                                border: "none",
                                resize: "none",
                                outline: "none",
                                fontSize: 14,
                                fontFamily: "inherit",
                                color: "var(--ink, #1A1D24)",
                                width: "100%",
                                height: "100%",
                            }}
                        />

                        {/* Asa de redimensionado (más grande) */}
                        <div
                            data-export-ignore="true"
                            onPointerDown={(e) =>
                                beginResize(e, sticky, setStickies, { minWidth: 90, minHeight: 90 })
                            }
                            onPointerMove={onResizeMove}
                            onPointerUp={onResizeEnd}
                            onPointerCancel={onResizeEnd}
                            title="Arrastra para cambiar el tamaño"
                            style={{
                                position: "absolute",
                                right: 0,
                                bottom: 0,
                                width: 24,
                                height: 24,
                                cursor: "se-resize",
                                touchAction: "none",
                                background: "rgba(0,0,0,0.12)",
                                borderRadius: "10px 0 10px 0",
                            }}
                        />
                    </div>
                ))}

                {calendars.map((cal) => {
                    const weeks = getMonthMatrix(cal.year, cal.month);
                    const today = new Date();
                    const isCurrentMonth =
                        today.getMonth() === cal.month &&
                        today.getFullYear() === cal.year;

                    return (
                        <div
                            key={cal.id}
                            className="ebx-card"
                            style={{
                                position: "absolute",
                                left: cal.x,
                                top: cal.y,
                                width: cal.width,
                                zIndex: cal.z || 0,
                                pointerEvents: "auto",
                                background: "var(--paper, #FFFDF8)",
                                border: cardBorder,
                                borderRadius: cardRadius,
                                boxShadow: cardShadow,
                                fontFamily: "var(--font-body, sans-serif)",
                                userSelect: "none",
                            }}
                        >
                            <div
                                style={{
                                    height: 5,
                                    background: "var(--ink, #1A1D24)",
                                    opacity: 0.9,
                                    borderRadius: `${cardRadius - 1}px ${cardRadius - 1}px 0 0`,
                                }}
                            />

                            {/* Cabecera: arrastre + navegación de mes + borrar (zona más grande) */}
                            <div
                                onPointerDown={(e) => beginDrag(e, cal, setCalendars)}
                                onPointerMove={onDragMove}
                                onPointerUp={onDragEnd}
                                onPointerCancel={onDragEnd}
                                style={{
                                    display: "flex",
                                    alignItems: "center",
                                    justifyContent: "space-between",
                                    padding: "13px 12px 11px",
                                    minHeight: 46,
                                    boxSizing: "border-box",
                                    borderBottom: cardBorder,
                                    cursor: "grab",
                                    touchAction: "none",
                                    background: "rgba(255,255,255,0.08)",
                                }}
                            >
                                <button
                                    type="button"
                                    className="ebx-btn"
                                    onPointerDown={(e) => e.stopPropagation()}
                                    onClick={() => shiftCalendarMonth(cal.id, -1)}
                                    style={{
                                        border: "none",
                                        background: "transparent",
                                        cursor: "pointer",
                                        color: "var(--ink, #1A1D24)",
                                        padding: 5,
                                        borderRadius: 8,
                                        display: "flex",
                                        alignItems: "center",
                                    }}
                                >
                                    <ChevronLeft size={16} />
                                </button>
                                <span
                                    style={{
                                        fontSize: 13,
                                        fontWeight: 700,
                                        fontFamily: "var(--font-hand, serif)",
                                        letterSpacing: "0.03em",
                                        textTransform: "uppercase",
                                        color: "var(--ink, #1A1D24)",
                                    }}
                                >
                                    {MONTH_NAMES[cal.month]} {cal.year}
                                </span>
                                <div style={{ display: "flex", alignItems: "center", gap: 2 }}>
                                    <button
                                        type="button"
                                        className="ebx-btn"
                                        onPointerDown={(e) => e.stopPropagation()}
                                        onClick={() => shiftCalendarMonth(cal.id, 1)}
                                        style={{
                                            border: "none",
                                            background: "transparent",
                                            cursor: "pointer",
                                            color: "var(--ink, #1A1D24)",
                                            padding: 5,
                                            borderRadius: 8,
                                            display: "flex",
                                            alignItems: "center",
                                        }}
                                    >
                                        <ChevronRight size={16} />
                                    </button>
                                    <button
                                        type="button"
                                        className="ebx-btn"
                                        onPointerDown={(e) => e.stopPropagation()}
                                        onClick={() => removeCalendar(cal.id)}
                                        title="Quitar calendario"
                                        style={{
                                            border: "none",
                                            background: "transparent",
                                            cursor: "pointer",
                                            color: "var(--pencil, #6B6660)",
                                            padding: 5,
                                            borderRadius: 8,
                                            display: "flex",
                                            alignItems: "center",
                                        }}
                                    >
                                        <X size={16} />
                                    </button>
                                </div>
                            </div>

                            {/* Cuadrícula del mes */}
                            <div style={{ padding: 8 }}>
                                <div
                                    style={{
                                        display: "grid",
                                        gridTemplateColumns: "repeat(7, minmax(0, 1fr))",
                                        gap: 2,
                                        marginBottom: 4,
                                    }}
                                >
                                    {DAY_NAMES.map((d) => (
                                        <div
                                            key={d}
                                            style={{
                                                textAlign: "center",
                                                fontSize: 10,
                                                color: "var(--pencil, #6B6660)",
                                                fontWeight: 700,
                                                letterSpacing: "0.06em",
                                                textTransform: "uppercase",
                                            }}
                                        >
                                            {d}
                                        </div>
                                    ))}
                                </div>
                                {weeks.map((week, wi) => (
                                    <div
                                        key={wi}
                                        style={{
                                            display: "grid",
                                            gridTemplateColumns: "repeat(7, minmax(0, 1fr))",
                                            gap: 2,
                                            marginBottom: 2,
                                        }}
                                    >
                                        {week.map((day, di) => {
                                            const isToday = isCurrentMonth && day === today.getDate();
                                            const eventKey = day ? `${cal.year}-${cal.month}-${day}` : null;
                                            const event = eventKey ? cal.events?.[eventKey] : null;
                                            return (
                                                <div
                                                    key={di}
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        if (day) {
                                                            bringToFront(cal.id, setCalendars);
                                                            setActiveEvent({ calId: cal.id, day, month: cal.month, year: cal.year });
                                                        }
                                                    }}
                                                    style={{
                                                        textAlign: "center",
                                                        fontSize: 11,
                                                        padding: "3px 0",
                                                        borderRadius: 7,
                                                        background: event
                                                            ? event.color
                                                            : isToday
                                                                ? "var(--ink, #1A1D24)"
                                                                : day
                                                                    ? "rgba(26,29,36,0.04)"
                                                                    : "transparent",
                                                        color: event
                                                            ? "var(--ink, #1A1D24)"
                                                            : isToday
                                                                ? "var(--paper, #F2EBDC)"
                                                                : day
                                                                    ? "var(--ink, #1A1D24)"
                                                                    : "transparent",
                                                        fontWeight: isToday || event ? 700 : 500,
                                                        minHeight: 22,
                                                        display: "flex",
                                                        alignItems: "center",
                                                        justifyContent: "center",
                                                        cursor: day ? "pointer" : "default",
                                                        transition: "background 0.12s ease, transform 0.1s ease",
                                                    }}
                                                >
                                                    {day || "·"}
                                                </div>
                                            );
                                        })}
                                    </div>
                                ))}
                            </div>

                            {/* Asa de redimensionado (solo ancho, grande) */}
                            <div
                                data-export-ignore="true"
                                onPointerDown={(e) =>
                                    beginResize(e, cal, setCalendars, {
                                        minWidth: MIN_CALENDAR_WIDTH,
                                        maxWidth: MAX_CALENDAR_WIDTH,
                                        widthOnly: true,
                                    })
                                }
                                onPointerMove={onResizeMove}
                                onPointerUp={onResizeEnd}
                                onPointerCancel={onResizeEnd}
                                title="Arrastra para cambiar el ancho"
                                style={{
                                    position: "absolute",
                                    right: -10,
                                    bottom: -10,
                                    width: 24,
                                    height: 24,
                                    borderRadius: "50%",
                                    background: "var(--ink, #1A1D24)",
                                    opacity: 0.85,
                                    cursor: "ew-resize",
                                    touchAction: "none",
                                    boxShadow: HANDLE_SHADOW,
                                }}
                            />
                        </div>
                    );
                })}

                {/* Cuadrantes de horario (nuevo diseño) */}
                {rosters.map((roster) => (
                    <RosterCard
                        key={roster.id}
                        roster={roster}
                        onMutate={(fn) => mutateRoster(roster.id, fn)}
                        onRemove={() => removeRoster(roster.id)}
                        onOpenShift={(memberId, dayIndex) =>
                            setActiveShift({ rosterId: roster.id, memberId, dayIndex })
                        }
                        dragHandlers={{
                            onPointerDown: (e) => beginDrag(e, roster, setRosters),
                            onPointerMove: onDragMove,
                            onPointerUp: onDragEnd,
                            onPointerCancel: onDragEnd,
                        }}
                        resizeHandlers={{
                            onPointerDown: (e) =>
                                beginResize(e, roster, setRosters, {
                                    minWidth: 480,
                                    maxWidth: 1000,
                                    widthOnly: true,
                                }),
                            onPointerMove: onResizeMove,
                            onPointerUp: onResizeEnd,
                            onPointerCancel: onResizeEnd,
                        }}
                    />
                ))}
            </div>

            {/* Toolbar */}
            <div
                className="ebx-toolbar"
                data-export-ignore="true"
                style={{
                    position: "fixed",
                    left: "50%",
                    bottom: "calc(16px + env(safe-area-inset-bottom, 0px))",
                    transform: "translateX(-50%)",
                    zIndex: 20,
                    display: "flex",
                    alignItems: "center",
                    gap: 2,
                    background: "color-mix(in srgb, var(--paper, #F2EBDC) 92%, transparent)",
                    backdropFilter: "blur(10px)",
                    WebkitBackdropFilter: "blur(10px)",
                    border: "1px solid rgba(26,29,36,0.12)",
                    borderRadius: 20,
                    padding: 7,
                    boxShadow: "0 1px 2px rgba(26,29,36,0.10), 0 14px 32px rgba(26,29,36,0.18)",
                    maxWidth: "calc(100vw - 24px)",
                    overflowX: "auto",
                    overflowY: "hidden",
                    flexWrap: "nowrap",
                    overscrollBehavior: "contain",
                    WebkitOverflowScrolling: "touch",
                }}
            >
                <button
                    type="button"
                    className={iconButtonClass(mode === "pan")}
                    onClick={() => setMode("pan")}
                    style={iconButtonStyle(mode === "pan")}
                    title="Mover el lienzo (H)"
                >
                    <Move size={19} strokeWidth={2} />
                </button>

                {divider}

                {Object.entries(DRAWING_TOOLS).map(([key, tool]) => (
                    <button
                        key={key}
                        type="button"
                        className={iconButtonClass(mode === "draw" && drawingTool === key)}
                        onClick={() => {
                            setMode("draw");
                            setDrawingTool(key);
                        }}
                        style={iconButtonStyle(mode === "draw" && drawingTool === key)}
                        title={`${tool.name} (${tool.key})`}
                    >
                        {React.createElement(tool.icon, { size: 19, strokeWidth: 2 })}
                    </button>
                ))}

                <button
                    type="button"
                    className={iconButtonClass(mode === "text")}
                    onClick={() => setMode("text")}
                    style={iconButtonStyle(mode === "text")}
                    title="Escribir texto en la pizarra (T)"
                >
                    <Type size={19} strokeWidth={2} />
                </button>

                {mode === "draw" && drawingTool !== "eraser" && (
                    <>
                        {divider}
                        <div style={{ display: "flex", alignItems: "center", gap: 6, padding: "0 4px" }}>
                            {COLOR_PALETTE.map((item) => (
                                <button
                                    key={item.color}
                                    type="button"
                                    className="ebx-swatch"
                                    onClick={() => setBrushColor(item.color)}
                                    title={item.name}
                                    style={{
                                        width: 17,
                                        height: 17,
                                        borderRadius: "50%",
                                        background: item.color,
                                        cursor: "pointer",
                                        padding: 0,
                                        border:
                                            brushColor === item.color
                                                ? "2px solid var(--paper, #F2EBDC)"
                                                : "1px solid rgba(0,0,0,0.15)",
                                        boxShadow:
                                            brushColor === item.color
                                                ? "0 0 0 2px var(--ink, #1A1D24)"
                                                : "none",
                                    }}
                                />
                            ))}
                            <label
                                title="Color personalizado"
                                style={{
                                    position: "relative",
                                    width: 18,
                                    height: 18,
                                    borderRadius: "50%",
                                    border: "1px solid var(--ink, #1A1D24)",
                                    overflow: "hidden",
                                    cursor: "pointer",
                                    display: "block",
                                }}
                            >
                                <input
                                    type="color"
                                    value={brushColor}
                                    onChange={(e) => setBrushColor(e.target.value)}
                                    style={{
                                        position: "absolute",
                                        top: -2,
                                        left: -2,
                                        width: 24,
                                        height: 24,
                                        cursor: "pointer",
                                        border: "none",
                                        padding: 0,
                                    }}
                                />
                            </label>
                        </div>

                        {divider}

                        <div style={{ display: "flex", alignItems: "center", gap: 6, padding: "0 4px" }}>
                            <input
                                type="range"
                                min="0.5"
                                max="30"
                                step="0.5"
                                value={brushSize}
                                onChange={(e) => setBrushSize(parseFloat(e.target.value))}
                                style={{ width: 64, cursor: "pointer", accentColor: "var(--ink, #1A1D24)" }}
                            />
                            <span
                                style={{
                                    fontSize: 11,
                                    fontWeight: 600,
                                    minWidth: 26,
                                    color: "var(--pencil, #6B6660)",
                                    fontFamily: "var(--font-display, sans-serif)",
                                }}
                            >
                                {brushSize.toFixed(1)}
                            </span>
                        </div>
                    </>
                )}

                {mode === "text" && (
                    <>
                        {divider}
                        <div style={{ display: "flex", alignItems: "center", gap: 6, padding: "0 4px" }}>
                            <select
                                value={textFont}
                                onChange={(e) => setTextFont(e.target.value)}
                                style={{
                                    fontSize: 12,
                                    borderRadius: 8,
                                    border: "1px solid var(--rule, #e5ded0)",
                                    background: "var(--paper, #F2EBDC)",
                                    color: "var(--ink, #1A1D24)",
                                    padding: "4px 6px",
                                    cursor: "pointer",
                                }}
                            >
                                {TEXT_FONT_OPTIONS.map((f) => (
                                    <option key={f.id} value={f.id}>
                                        {f.label}
                                    </option>
                                ))}
                            </select>
                            <label
                                title="Color del texto"
                                style={{
                                    position: "relative",
                                    width: 18,
                                    height: 18,
                                    borderRadius: "50%",
                                    border: "1px solid var(--ink, #1A1D24)",
                                    overflow: "hidden",
                                    cursor: "pointer",
                                    display: "block",
                                }}
                            >
                                <input
                                    type="color"
                                    value={textColor}
                                    onChange={(e) => setTextColor(e.target.value)}
                                    style={{
                                        position: "absolute",
                                        top: -2,
                                        left: -2,
                                        width: 24,
                                        height: 24,
                                        cursor: "pointer",
                                        border: "none",
                                        padding: 0,
                                    }}
                                />
                            </label>
                            <span style={{ fontSize: 10, color: "var(--pencil, #6B6660)", whiteSpace: "nowrap" }}>
                                Toca el lienzo y escribe · Enter para fijar
                            </span>
                        </div>
                    </>
                )}

                {divider}

                <button
                    type="button"
                    className="ebx-btn"
                    onClick={undo}
                    disabled={past.length === 0}
                    style={{ ...iconButtonStyle(false), opacity: past.length === 0 ? 0.35 : 1 }}
                    title="Deshacer (Ctrl+Z)"
                >
                    <RotateCcw size={18} strokeWidth={2} />
                </button>
                <button
                    type="button"
                    className="ebx-btn"
                    onClick={redo}
                    disabled={future.length === 0}
                    style={{ ...iconButtonStyle(false), opacity: future.length === 0 ? 0.35 : 1 }}
                    title="Rehacer (Ctrl+Shift+Z)"
                >
                    <RotateCw size={18} strokeWidth={2} />
                </button>
                <button
                    type="button"
                    className="ebx-btn"
                    onClick={clearDrawing}
                    disabled={strokes.length === 0}
                    style={{ ...iconButtonStyle(false), opacity: strokes.length === 0 ? 0.35 : 1 }}
                    title="Borrar dibujo (no afecta a calendarios, notas, tablas ni textos)"
                >
                    <Trash2 size={18} strokeWidth={2} />
                </button>

                {divider}

                <button
                    type="button"
                    className="ebx-btn"
                    onClick={() => zoomBy(0.85)}
                    style={iconButtonStyle(false)}
                    title="Alejar (-)"
                >
                    <Minus size={18} strokeWidth={2} />
                </button>
                <button
                    type="button"
                    className="ebx-btn"
                    onClick={resetView}
                    title="Restablecer vista (0)"
                    style={{
                        fontSize: 11,
                        fontWeight: 700,
                        color: "var(--pencil, #6B6660)",
                        background: "transparent",
                        border: "none",
                        borderRadius: 10,
                        padding: "4px 6px",
                        cursor: "pointer",
                        minWidth: 44,
                        fontFamily: "var(--font-display, sans-serif)",
                    }}
                >
                    {Math.round(viewport.scale * 100)}%
                </button>
                <button
                    type="button"
                    className="ebx-btn"
                    onClick={() => zoomBy(1.15)}
                    style={iconButtonStyle(false)}
                    title="Acercar (+)"
                >
                    <Plus size={18} strokeWidth={2} />
                </button>

                {divider}

                <button
                    type="button"
                    className={iconButtonClass(showCalendarPopup)}
                    onClick={() => setShowCalendarPopup((v) => !v)}
                    style={iconButtonStyle(showCalendarPopup)}
                    title="Insertar calendario mensual"
                >
                    <CalendarDays size={19} strokeWidth={2} />
                </button>
                <button
                    type="button"
                    className="ebx-btn"
                    onClick={addSticky}
                    style={iconButtonStyle(false)}
                    title="Añadir recordatorio"
                >
                    <StickyNote size={19} strokeWidth={2} />
                </button>
                <button
                    type="button"
                    className={iconButtonClass(showTablePopup)}
                    onClick={() => setShowTablePopup((v) => !v)}
                    style={iconButtonStyle(showTablePopup)}
                    title="Insertar tabla"
                >
                    <Table size={19} strokeWidth={2} />
                </button>
                <button
                    type="button"
                    className="ebx-btn"
                    onClick={addRoster}
                    style={iconButtonStyle(false)}
                    title="Insertar tabla de horario"
                >
                    <Users size={19} strokeWidth={2} />
                </button>

                {divider}

                <button
                    type="button"
                    className="ebx-btn"
                    onClick={exportDrawing}
                    disabled={isExporting}
                    style={{ ...iconButtonStyle(false), opacity: isExporting ? 0.5 : 1 }}
                    title="Exportar y descargar toda la pizarra como imagen (PNG)"
                >
                    {justExported ? (
                        <Check size={19} strokeWidth={2.4} color="#2A9D8F" />
                    ) : (
                        <Download size={19} strokeWidth={2} />
                    )}
                </button>
            </div>

            {showTablePopup && (
                <div
                    className="ebx-popup ebx-table-popup"
                    data-export-ignore="true"
                    style={{
                        position: "fixed",
                        left: "50%",
                        bottom: "calc(84px + env(safe-area-inset-bottom, 0px))",
                        transform: "translateX(-50%)",
                        maxWidth: "calc(100vw - 24px)",
                        flexWrap: "wrap",
                        justifyContent: "center",
                        zIndex: 21,
                        background: "var(--paper, #F2EBDC)",
                        border: cardBorder,
                        borderRadius: 12,
                        padding: "10px 12px",
                        boxShadow: cardShadow,
                        display: "flex",
                        alignItems: "center",
                        gap: 8,
                        fontFamily: "var(--font-body, sans-serif)",
                    }}
                >
                    <span style={{ fontSize: 12, color: "var(--pencil, #6B6660)" }}>Filas</span>
                    <input
                        type="number"
                        min={1}
                        max={12}
                        value={tableRowsInput}
                        onChange={(e) => setTableRowsInput(clamp(Number(e.target.value) || 1, 1, 12))}
                        style={{ width: 52, padding: "4px 6px", borderRadius: 8, border: "1px solid var(--rule, #e5ded0)", fontSize: 13 }}
                    />
                    <span style={{ fontSize: 12, color: "var(--pencil, #6B6660)" }}>Columnas</span>
                    <input
                        type="number"
                        min={1}
                        max={12}
                        value={tableColsInput}
                        onChange={(e) => setTableColsInput(clamp(Number(e.target.value) || 1, 1, 12))}
                        style={{ width: 52, padding: "4px 6px", borderRadius: 8, border: "1px solid var(--rule, #e5ded0)", fontSize: 13 }}
                    />
                    <button
                        type="button"
                        className="ebx-popup-btn"
                        onClick={addTable}
                        style={{
                            background: "var(--ink, #1A1D24)",
                            color: "var(--paper, #F2EBDC)",
                            border: "none",
                            borderRadius: 8,
                            padding: "5px 10px",
                            fontSize: 12,
                            fontWeight: 600,
                            cursor: "pointer",
                        }}
                    >
                        Insertar
                    </button>
                    <button
                        type="button"
                        className="ebx-btn"
                        onClick={() => setShowTablePopup(false)}
                        style={{ background: "transparent", border: "none", borderRadius: 8, color: "var(--pencil, #6B6660)", cursor: "pointer", padding: 4 }}
                        title="Cancelar"
                    >
                        <X size={16} />
                    </button>
                </div>
            )}

            {showCalendarPopup && (
                <div
                    className="ebx-popup ebx-calendar-popup"
                    data-export-ignore="true"
                    style={{
                        position: "fixed",
                        left: "50%",
                        bottom: "calc(84px + env(safe-area-inset-bottom, 0px))",
                        transform: "translateX(-50%)",
                        maxWidth: "calc(100vw - 24px)",
                        flexWrap: "wrap",
                        justifyContent: "center",
                        zIndex: 21,
                        background: "var(--paper, #F2EBDC)",
                        border: cardBorder,
                        borderRadius: 12,
                        padding: "10px 12px",
                        boxShadow: cardShadow,
                        display: "flex",
                        alignItems: "center",
                        gap: 8,
                        fontFamily: "var(--font-body, sans-serif)",
                    }}
                >
                    <span style={{ fontSize: 12, color: "var(--pencil, #6B6660)" }}>Ancho de la caja (px)</span>
                    <input
                        type="number"
                        min={MIN_CALENDAR_WIDTH}
                        max={MAX_CALENDAR_WIDTH}
                        step={10}
                        value={calendarWidthInput}
                        onChange={(e) => setCalendarWidthInput(e.target.value)}
                        style={{ width: 70, padding: "4px 6px", borderRadius: 8, border: "1px solid var(--rule, #e5ded0)", fontSize: 13 }}
                    />
                    <button
                        type="button"
                        className="ebx-popup-btn"
                        onClick={addCalendarBox}
                        style={{
                            background: "var(--ink, #1A1D24)",
                            color: "var(--paper, #F2EBDC)",
                            border: "none",
                            borderRadius: 8,
                            padding: "5px 10px",
                            fontSize: 12,
                            fontWeight: 600,
                            cursor: "pointer",
                        }}
                    >
                        Insertar
                    </button>
                    <button
                        type="button"
                        className="ebx-btn"
                        onClick={() => setShowCalendarPopup(false)}
                        style={{ background: "transparent", border: "none", borderRadius: 8, color: "var(--pencil, #6B6660)", cursor: "pointer", padding: 4 }}
                        title="Cancelar"
                    >
                        <X size={16} />
                    </button>
                </div>
            )}

            {/* Estado de guardado */}
            {saveUrl && (
                <div
                    data-export-ignore="true"
                    style={{
                        position: "absolute",
                        left: 16,
                        bottom: 14,
                        zIndex: 10,
                        display: "flex",
                        alignItems: "center",
                        gap: 6,
                        fontSize: 11,
                        color: "var(--pencil, #6B6660)",
                        fontFamily: "var(--font-body, sans-serif)",
                        opacity: isSaving || justSaved ? 1 : 0,
                        transition: "opacity 0.2s ease",
                    }}
                >
                    <span
                        style={{
                            width: 6,
                            height: 6,
                            borderRadius: "50%",
                            background: isSaving ? "#F1C761" : "#2A9D8F",
                        }}
                    />
                    {isSaving ? "Guardando…" : "Guardado"}
                </div>
            )}

            {/* Editor de eventos del calendario */}
            {activeEvent && (
                <div
                    data-export-ignore="true"
                    style={{
                        position: "fixed",
                        top: "50%",
                        left: "50%",
                        transform: "translate(-50%, -50%)",
                        zIndex: 100,
                        background: "var(--paper, #F2EBDC)",
                        border: "1px solid rgba(26,29,36,0.14)",
                        borderRadius: 16,
                        padding: "16px",
                        boxShadow: cardShadowLift,
                        display: "flex",
                        flexDirection: "column",
                        gap: 12,
                        minWidth: 280,
                        fontFamily: "var(--font-body, sans-serif)",
                    }}
                >
                    <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                        <span style={{ fontWeight: 700, fontSize: 14 }}>
                            Evento: {activeEvent.day} {MONTH_NAMES[activeEvent.month]} {activeEvent.year}
                        </span>
                        <button
                            type="button"
                            className="ebx-btn"
                            onClick={() => setActiveEvent(null)}
                            style={{ border: "none", background: "transparent", borderRadius: 8, cursor: "pointer", padding: 4 }}
                        >
                            <X size={16} />
                        </button>
                    </div>

                    <textarea
                        value={
                            calendars
                                .find((c) => c.id === activeEvent.calId)
                                ?.events[`${activeEvent.year}-${activeEvent.month}-${activeEvent.day}`]?.text || ""
                        }
                        onChange={(e) =>
                            updateCalendarEvent(
                                activeEvent.calId,
                                activeEvent.day,
                                activeEvent.month,
                                activeEvent.year,
                                { text: e.target.value },
                            )
                        }
                        placeholder="Escribe tu recordatorio aquí..."
                        style={{
                            width: "100%",
                            height: 80,
                            padding: 8,
                            borderRadius: 10,
                            border: "1px solid var(--rule, #e5ded0)",
                            fontSize: 13,
                            fontFamily: "inherit",
                            resize: "none",
                        }}
                    />

                    <div style={{ display: "flex", alignItems: "center", gap: 8 }}>
                        <span style={{ fontSize: 12, color: "var(--pencil, #6B6660)" }}>Color:</span>
                        <div style={{ display: "flex", gap: 6 }}>
                            {CALENDAR_EVENT_PALETTE.map((c) => (
                                <div
                                    key={c}
                                    className="ebx-swatch"
                                    onClick={() =>
                                        updateCalendarEvent(
                                            activeEvent.calId,
                                            activeEvent.day,
                                            activeEvent.month,
                                            activeEvent.year,
                                            { color: c },
                                        )
                                    }
                                    style={{
                                        width: 18,
                                        height: 18,
                                        borderRadius: "50%",
                                        background: c,
                                        cursor: "pointer",
                                        border:
                                            calendars
                                                .find((c2) => c2.id === activeEvent.calId)
                                                ?.events[`${activeEvent.year}-${activeEvent.month}-${activeEvent.day}`]?.color === c
                                                ? "2px solid var(--ink, #1A1D24)"
                                                : "1px solid rgba(0,0,0,0.2)",
                                    }}
                                />
                            ))}
                        </div>
                    </div>
                </div>
            )}

            {/* Editor de turno del cuadrante de horario */}
            {activeShift && (() => {
                const roster = rosters.find((r) => r.id === activeShift.rosterId);
                if (!roster) return null;
                return (
                    <ShiftEditor
                        roster={roster}
                        memberId={activeShift.memberId}
                        dayIndex={activeShift.dayIndex}
                        onMutate={(fn) => mutateRoster(roster.id, fn)}
                        onClose={() => setActiveShift(null)}
                    />
                );
            })()}
        </div>
    );
}