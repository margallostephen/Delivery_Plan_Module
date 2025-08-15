function populateTable(deliveryTable, datepicker, staticCols) {
    $("#deliveryTable").hide();
    $("#loader").show();
    $("#noDataMessage").hide();
    $("#exportExcelBtn").prop("disabled", true);

    $.ajax({
        url: 'data.php',
        method: 'POST',
        data: { selected_date: datepicker.val() },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            const tableData = response.delivery_plan;
            importDatetime = response.latestImportDatetime;

            $("#importLabel").text(
                `DELIVERY PLAN LIST${importDatetime ? ' as of (' + importDatetime + ')' : ''}`
            );

            let i = 7;
            let tabulatorCols = [];

            const startColDate = response.firstColDate;
            const lastColDate = response.lastColDate;

            const planDateCols = pushQuantityColumn(startColDate, lastColDate, i);
            tabulatorCols = tabulatorCols.concat(planDateCols.cols);
            i = planDateCols.nextIndex;

            tabulatorCols.push({
                title: "FG",
                field: numToAlpha(i++),
                hozAlign: "right",
                headerFilter: "input",
                vertAlign: "middle",
                headerHozAlign: "center",
                formatter: "money",
                formatterParams: {
                    thousand: ',',
                    decimal: '.',
                    precision: 0
                }
            });

            const balDateCols = pushQuantityColumn(startColDate, lastColDate, i, true);
            tabulatorCols = tabulatorCols.concat(balDateCols.cols);
            const allCols = staticCols.concat(tabulatorCols);

            console.log(allCols)

            if (tableData.length > 0) {
                deliveryTable.setColumns(allCols);
                deliveryTable.setData(tableData).then(() => {
                    $("#loader").hide();
                    $("#noDataMessage").hide();
                    $("#deliveryTable").show();
                });
            } else {
                $("#loader").hide();
                $("#deliveryTable").hide();
                $("#noDataMessage").show();
            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
            $("#loader").hide();
        },
        complete: function () {
            $("#exportExcelBtn").prop("disabled", false);
        }
    });
}