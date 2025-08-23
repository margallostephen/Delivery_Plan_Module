async function autoSetupTable(table) {
    $('#refreshTableBtn').trigger('click');

    await new Promise(r => table.on('dataLoaded', r));

    if (localStorage.getItem("colRange") === "1MonthRange") {
        $('#toggleExtraDatesBtn').trigger("click");
    }

    if (localStorage.getItem("dataToSet") === "allRows") {
        $('#toggleRowsBtn').trigger("click");
    }

    await new Promise(r => setTimeout(r, 500));

    const holder = $(table.element).find('.tabulator-tableHolder')[0];
    $(holder).animate({ scrollLeft: holder.scrollWidth }, 500);

    $('#toggleAutoPaginateBtn').trigger("click");
}