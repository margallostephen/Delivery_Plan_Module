function pushQuantityColumn(startDateStr, endDateStr, startIndex, balanceCol = false) {
    if (!startDateStr || !endDateStr) return { cols: [], nextIndex: startIndex };

    const [startYear, startMonth, startDay] = startDateStr.split('-').map(Number);
    const [endYear, endMonth, endDay] = endDateStr.split('-').map(Number);

    const dt = new Date(startYear, startMonth - 1, startDay);
    dt.setHours(0, 0, 0, 0);

    const end = new Date(endYear, endMonth - 1, endDay);
    end.setHours(0, 0, 0, 0);

    const cols = [];
    let i = startIndex;

    while (dt <= end) {
        const formattedDate = new Intl.DateTimeFormat('en-CA', {
            month: '2-digit',
            day: '2-digit'
        }).format(dt);
        const dayName = dt.toLocaleDateString(undefined, { weekday: 'short' });

        cols.push({
            title: `${formattedDate}<hr>${dayName}`,
            field: numToAlpha(i++),
            hozAlign: "right",
            vertAlign: "middle",
            headerHozAlign: "center",
            titleFormatter: "html",
            titleDownload: formattedDate,
            formatter: (cell) => {
                const val = cell.getValue();
                const el = cell.getElement();
                const isNegative = typeof val === 'number' && val < 0 && balanceCol;

                el.style.backgroundColor = isNegative ? "#ffcccc" : "";
                el.style.color = isNegative ? "red" : "";
                el.style.fontWeight = isNegative ? "bold" : "";

                return isNegative
                    ? `(${new Intl.NumberFormat('en-US').format(-val)})`
                    : (val > 0
                        ? new Intl.NumberFormat('en-US').format(val)
                        : "-");
            }
        });

        dt.setDate(dt.getDate() + 1);
    }

    return { cols, nextIndex: i };
}