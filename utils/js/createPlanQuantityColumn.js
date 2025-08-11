function pushQuantityColumn(startDateStr, startIndex, balanceCol = false) {
    const [year, month, day] = startDateStr.split('-').map(Number);

    const dt = new Date(year, month - 1, day);
    dt.setHours(0, 0, 0, 0);

    const end = new Date(year, month, 0);
    end.setHours(0, 0, 0, 0);

    const cols = [];
    let i = startIndex;

    while (dt <= end) {
        const formattedDate = new Intl.DateTimeFormat('en-CA').format(dt);
        const dayName = dt.toLocaleDateString(undefined, { weekday: 'short' });

        cols.push({
            title: `${dayName}<hr>${formattedDate}`,
            field: numToAlpha(i++),
            hozAlign: "right",
            vertAlign: "middle",
            headerHozAlign: "center",
            titleFormatter: "html",
            titleDownload: formattedDate,
            formatter: (cell) => {
                const val = cell.getValue();
                const el = cell.getElement();
                el.style.backgroundColor = val < 0 && balanceCol ? "#ffcccc" : "";
                el.style.color = val < 0 && balanceCol ? "red" : "";
                el.style.fontWeight = val < 0 && balanceCol ? "bold" : "";

                return (typeof val === 'number' && val < 0 && balanceCol)
                    ? `(${new Intl.NumberFormat('en-US').format(-val)})`
                    : (typeof val === 'number' ? new Intl.NumberFormat('en-US').format(val) : val);
            }
        });

        dt.setDate(dt.getDate() + 1);
    }

    return { cols, nextIndex: i };
}