const createText = ({ x, y, text, fontSize = 18, strokeColor = '#1A1D24', width = 200, height = 24, fontWeight = 500 }) => ({
    type: 'text',
    x,
    y,
    text,
    fontSize,
    fontFamily: 1,
    fontWeight,
    textAlign: 'left',
    verticalAlign: 'top',
    baseline: 0,
    strokeColor,
    width,
    height,
    backgroundColor: 'transparent',
});

const createRectangle = ({ x, y, width, height, fill = 'transparent', stroke = '#1A1D24', strokeWidth = 1.5 }) => ({
    type: 'rectangle',
    x,
    y,
    width,
    height,
    strokeColor: stroke,
    backgroundColor: fill,
    fillStyle: 'hachure',
    strokeWidth,
    roughness: 1,
    roundness: { type: 3 },
});

export const generateCalendarElements = (month, year, x = 0, y = 0) => {
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDay = new Date(year, month, 1).getDay();
    const cellWidth = 60;
    const cellHeight = 60;
    const elements = [];

    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    elements.push(createText({
        x,
        y,
        text: `${monthNames[month]} ${year}`,
        fontSize: 20,
        width: 7 * cellWidth,
        height: 30,
        fontWeight: 600,
    }));

    const dayLabels = ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"];
    dayLabels.forEach((label, i) => {
        elements.push(createText({
            x: x + i * cellWidth,
            y: y + 30,
            text: label,
            fontSize: 14,
            width: cellWidth,
            height: 20,
        }));
    });

    let currentDay = 1;
    for (let row = 0; row < 6; row++) {
        for (let col = 0; col < 7; col++) {
            const posX = x + col * cellWidth;
            const posY = y + 50 + row * cellHeight;

            elements.push(createRectangle({
                x: posX,
                y: posY,
                width: cellWidth,
                height: cellHeight,
                fill: 'transparent',
                stroke: '#1A1D24',
                strokeWidth: 1,
            }));

            if (row === 0 && col < firstDay) {
                continue;
            }

            if (currentDay <= daysInMonth) {
                elements.push(createText({
                    x: posX + 10,
                    y: posY + 10,
                    text: currentDay.toString(),
                    fontSize: 14,
                    width: 20,
                    height: 20,
                }));
                currentDay++;
            }
        }
    }

    return elements;
};

export const generateStickyNoteElements = (x = 0, y = 0) => {
    return [
        createRectangle({
            x,
            y,
            width: 220,
            height: 160,
            fill: '#F1C761',
            stroke: '#1A1D24',
            strokeWidth: 1.5,
        }),
        createText({ x: x + 18, y: y + 18, text: 'Idea', fontSize: 18, width: 120, height: 24, fontWeight: 600 }),
        createText({ x: x + 18, y: y + 62, text: '• Escribe aquí', fontSize: 16, width: 180, height: 20 }),
        createText({ x: x + 18, y: y + 96, text: '• Añade tareas', fontSize: 16, width: 180, height: 20 }),
        createText({ x: x + 18, y: y + 130, text: '• Revisa prioridades', fontSize: 16, width: 180, height: 20 }),
    ];
};

export const generatePriorityLegendElements = (x = 0, y = 0) => {
    return [
        createRectangle({ x, y, width: 260, height: 150, fill: '#F2EBDC', stroke: '#1A1D24', strokeWidth: 1.5 }),
        createText({ x: x + 18, y: y + 18, text: 'Prioridades', fontSize: 18, width: 160, height: 24, fontWeight: 600 }),
        createRectangle({ x: x + 18, y: y + 52, width: 18, height: 18, fill: '#1F3FA8', stroke: '#1A1D24', strokeWidth: 1.5 }),
        createText({ x: x + 48, y: y + 48, text: 'Alta', fontSize: 16, width: 80, height: 20 }),
        createRectangle({ x: x + 18, y: y + 82, width: 18, height: 18, fill: '#F1C761', stroke: '#1A1D24', strokeWidth: 1.5 }),
        createText({ x: x + 48, y: y + 78, text: 'Media', fontSize: 16, width: 80, height: 20 }),
        createRectangle({ x: x + 18, y: y + 112, width: 18, height: 18, fill: '#D9D3C7', stroke: '#1A1D24', strokeWidth: 1.5 }),
        createText({ x: x + 48, y: y + 108, text: 'Baja', fontSize: 16, width: 80, height: 20 }),
    ];
};

export const generateShiftBoardElements = (x = 0, y = 0) => {
    return [
        createRectangle({ x, y, width: 360, height: 170, fill: '#F8F3E9', stroke: '#1A1D24', strokeWidth: 1.5 }),
        createText({ x: x + 18, y: y + 18, text: 'Turnos', fontSize: 18, width: 120, height: 24, fontWeight: 600 }),
        createText({ x: x + 18, y: y + 60, text: 'Lunes', fontSize: 14, width: 70, height: 20 }),
        createText({ x: x + 120, y: y + 60, text: '08:00 - 16:00', fontSize: 14, width: 150, height: 20 }),
        createText({ x: x + 18, y: y + 92, text: 'Martes', fontSize: 14, width: 70, height: 20 }),
        createText({ x: x + 120, y: y + 92, text: '09:00 - 17:00', fontSize: 14, width: 150, height: 20 }),
        createText({ x: x + 18, y: y + 124, text: 'Miércoles', fontSize: 14, width: 90, height: 20 }),
        createText({ x: x + 120, y: y + 124, text: '10:00 - 18:00', fontSize: 14, width: 150, height: 20 }),
    ];
};
