function refreshData(table) {
    $.ajax({
        url: 'data.php',
        method: 'POST',
        data: { selected_date: localStorage.getItem("latest_import_datetime") },
        dataType: 'json',
        success: function (response) {
            const dataSetType = localStorage.getItem("dataToSet");

            table.setData(dataSetType == "allRows" ? response.delivery_plan : response.delivery_plan_negative);
            console.log("Data refreshed");
            table.setSort("an", "asc");

            if (localStorage.getItem("colRange") === "1MonthRange") {
                $('#toggleExtraDatesBtn').trigger("click");
            }

            if (localStorage.getItem("dataToSet") === "allRows") {
                $('#toggleRowsBtn').trigger("click");
            }

            const holder = $(table.element).find('.tabulator-tableHolder')[0];
            $(holder).animate({ scrollLeft: holder.scrollWidth }, 500);
        },
        error: function (error) {
            console.error('Error:', error);
        },
    });
}