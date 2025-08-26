function createTable(id, planColumns) {
    return new Tabulator(`#${id}`, {
        height: "740px",
        layout: "fitDataFill",
        autoResize: true,
        pagination: "local",
        paginationSize: 15,
        paginationCounter: "rows",
        paginationSizeSelector: [15, 100, 250, 500, 1000, true],
        columns: planColumns,
        groupBy: "b"
    });
}