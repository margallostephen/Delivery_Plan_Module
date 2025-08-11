function createTable(id, planColumns) {
    return new Tabulator(`#${id}`, {
        layout: "fitDataFill",
        autoResize: true,
        pagination: "local",
        paginationSize: 50,
        paginationCounter: "rows",
        paginationSizeSelector: [100, 250, 500, 1000, true],
        columns: planColumns
    });
}