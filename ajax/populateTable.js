function populateTable(deliveryTable, datepicker, staticCols) {
    $("#deliveryTable").hide();
    $("#loader").show();
    $("#noDataMessage").hide();
    $("#exportExcelBtn, #refreshTableBtn, #toggleExtraDates").prop("disabled", true);

    $.ajax({
        url: 'data.php',
        method: 'POST',
        data: { selected_date: datepicker.val() },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            const tableData = response.delivery_plan;
            importDatetime = response.latestImportDatetime;

            localStorage.setItem("delivery_plan", JSON.stringify(tableData));
            localStorage.setItem("delivery_plan_negative", JSON.stringify(response.delivery_plan_negative));

            $("#importLabel").text(
                `DELIVERY PLAN LIST${importDatetime ? ' as of (' + importDatetime + ')' : ''}`
            );

            let i = 7;
            let tabulatorCols = [];

            const startColDate = response.firstColDate;
            const lastColDate = response.lastColDate;

            planDateCols = [];
            planDateCols = pushQuantityColumn(startColDate, lastColDate, i);
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

            balDateCols = [];
            balDateCols = pushQuantityColumn(startColDate, lastColDate, i, true);
            tabulatorCols = tabulatorCols.concat(balDateCols.cols);
            const allCols = staticCols.concat(tabulatorCols);

            if (tableData.length > 0) {
                deliveryTable.setColumns(allCols);
                deliveryTable.setData(tableData).then(() => {
                    $("#loader").hide();
                    $("#noDataMessage").hide();
                    $("#deliveryTable").show();
                });

                $(deliveryTable.element)
                    .find('.tabulator-paginator #toggleAutoPaginate')
                    .length || $(deliveryTable.element)
                        .find('.tabulator-paginator')
                        .append(`
                            <button id="toggleAutoPaginate" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-play"></i>
                                <span>Auto Paginate</span>
                            </button>
                        `);

                const $paginator = $(deliveryTable.element).find('.tabulator-paginator');
                const $toggleExtraDatesBtn = $paginator.find('#toggleExtraDates');
                const $toggleRowsBtn = $paginator.find("#toggleRowsBtn")

                if ($toggleExtraDatesBtn.length) {
                    const $icon = $toggleExtraDatesBtn.find('i');
                    const $text = $toggleExtraDatesBtn.find('span');

                    $icon.removeClass('fa-calendar-days').addClass('fa-calendar-week');
                    $toggleExtraDatesBtn.removeClass('btn-inverse').addClass('btn-danger');
                    $text.text('Show 5 Days Range');
                } else {
                    $paginator.append(`
                        <button type="button" class="btn btn-sm btn-danger" id="toggleExtraDates">
                            <i class="ace-icon fa fa-calendar-week"></i>
                            <span>Show 5 Days Range</span>
                        </button>
                    `);
                }

                if ($toggleRowsBtn.length) {
                    const $icon = $toggleRowsBtn.find('i');
                    const $text = $toggleRowsBtn.find('span');

                    $icon.removeClass('fa-list').addClass('fa-magnifying-glass-minus');
                    $toggleRowsBtn.removeClass('btn-light').addClass('btn-danger');
                    $text.text('Show Rows with Negative Balance');
                } else {
                    $paginator.append(`
                            <button type="button" class="btn btn-sm btn-danger" id="toggleRowsBtn">
                                <i class="fa-solid fa-magnifying-glass-minus"></i>
                                <span>Show Row with Negative Balance</span>
                            </button>
                    `);
                }
            } else {
                $("#loader").hide();
                $("#deliveryTable").hide();
                $("#noDataMessage").show();
            }
        },
        error: function (error) {
            console.error('Error:', error);
            $("#loader").hide();
        },
        complete: function () {
            $("#refreshTableBtn").find("span").text("Refresh Table");
            $("#refreshTableBtn i").removeClass("fa-spin");
            $("#exportExcelBtn, #refreshTableBtn, #toggleExtraDates").prop("disabled", false);
        }
    });
}