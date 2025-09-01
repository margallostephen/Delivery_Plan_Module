function updateHeaderCounts(table) {
    const data = table.getData("active");

    const cols = table.getColumns();
    let fgFound = false;

    cols.forEach(col => {
        const field = col.getField();
        if (!field) return;

        if (field === "am") {
            fgFound = true;
            return;
        }

        if (!fgFound) return;

        let negCount = 0;
        let posCount = 0;

        if (data.length > 0) {
            data.forEach(row => {
                const val = row[field];
                if (typeof val === "number") {
                    if (val < 0) negCount++;
                    if (val > 0) posCount++;
                }
            });
        }

        $(`#neg-count-${field}`).text(`(${negCount})`);
        $(`#pos-count-${field}`).text(posCount);
    });
}
