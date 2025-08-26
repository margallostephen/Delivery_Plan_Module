function populateTable(deliveryTable, datepicker, staticCols) {
    $("#deliveryTable").hide();
    $("#loader").show();
    $("#noDataMessage").hide();
    $("#exportExcelBtn, #refreshTableBtn, #toggleExtraDatesBtn").prop("disabled", true);

    $.ajax({
        url: 'data.php',
        method: 'POST',
        data: { selected_date: datepicker.val() },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            const allDeliveryData = response.delivery_plan;
            const negativeDeliveryData = response.delivery_plan_negative;
            importDatetime = response.latestImportDatetime;
            const dateOnly = importDatetime?.split(" ")[0];

            const stored = localStorage.getItem("dataToSet") || "allRows";
            const tableData = stored === "allRows" ? allDeliveryData : negativeDeliveryData;

            localStorage.setItem("latest_import_datetime", dateOnly);
            localStorage.setItem("delivery_plan", JSON.stringify(allDeliveryData));
            localStorage.setItem("delivery_plan_negative", JSON.stringify(negativeDeliveryData));

            if (localStorage.getItem("tblModeToSet") === null) {
                localStorage.setItem("tblModeToSet", "negativeBalance");
            }

            if (localStorage.getItem("colRange") === null) {
                localStorage.setItem("colRange", "1MonthRange");
            }

            if (localStorage.getItem("dataToSet") === null) {
                localStorage.setItem("dataToSet", "allRows");
            }

            $("#importLabel").text(
                `DELIVERY PLAN LIST${importDatetime ? ' as of (' + importDatetime + ')' : ''}`
            );

            let i = 7;
            let tabulatorCols = [];

            const startColDate = response.firstColDate;
            const lastColDate = response.lastColDate;

            planDateCols = [];
            planDateCols = pushQuantityColumn(startColDate, lastColDate, i, tableData);
            tabulatorCols = tabulatorCols.concat(planDateCols.cols);
            i = planDateCols.nextIndex;

            tabulatorCols.push({
                title: "FG",
                field: numToAlpha(i++),
                hozAlign: "right",
                headerFilter: "input",
                vertAlign: "middle",
                headerHozAlign: "center",
                formatter: function (cell) {
                    const val = cell.getValue();

                    return val > 0
                        ? new Intl.NumberFormat('en-US').format(val)
                        : "-"
                }
            });

            balDateCols = [];
            balDateCols = pushQuantityColumn(startColDate, lastColDate, i, tableData, true);
            tabulatorCols = tabulatorCols.concat(balDateCols.cols);
            const allCols = staticCols.concat(tabulatorCols);

            console.log(allCols);

            if (tableData.length > 0) {
                deliveryTable.setColumns(allCols);

                deliveryTable.setData(tableData).then(() => {
                    $("#loader").hide();
                    $("#noDataMessage").hide();
                    $("#deliveryTable").show();
                    deliveryTable.setSort("an", "asc");

                    if (localStorage.getItem("colRange") === "5DaysRange") {
                        [...planDateCols.cols.slice(5), ...balDateCols.cols.slice(5)]
                            .forEach(col => deliveryTable.hideColumn(deliveryTable.getColumn(col.field)));
                    }
                });

                const $paginator = $(deliveryTable.element).find('.tabulator-paginator');
                const $autoPaginateBtn = $paginator.find('#toggleAutoPaginateBtn');
                const $toggleExtraDatesBtn = $paginator.find('#toggleExtraDatesBtn');
                const $toggleRowsBtn = $paginator.find("#toggleRowsBtn");
                const $setupTableBtn = $paginator.find("#setupTableBtn");
                const colRangeLocal = localStorage.getItem("colRange");
                const dataToSetLocal = localStorage.getItem("dataToSet");
                const tblModeToSet = localStorage.getItem("tblModeToSet");

                if ($autoPaginateBtn.length) {
                    const $icon = $autoPaginateBtn.find('i');
                    const $text = $autoPaginateBtn.find('span');

                    $icon.removeClass('fa-stop').addClass('fa-play');
                    $autoPaginateBtn.removeClass('btn-danger').addClass('btn-primary');
                    $text.text('Auto Paginate');
                } else {
                    $paginator.append(`
                        <button type="button" class="btn btn-sm btn-primary" id="toggleAutoPaginateBtn">
                            <i class="fa-solid fa-play"></i>
                            <span>Auto Paginate</span>
                        </button>
                    `);
                }

                $("#toggleAutoPaginateBtn").hide();

                const isFiveDays = colRangeLocal === "5DaysRange";

                if ($toggleExtraDatesBtn.length) {
                    const $icon = $toggleExtraDatesBtn.find('i');
                    const $text = $toggleExtraDatesBtn.find('span');

                    if (isFiveDays) {
                        $icon.removeClass('fa-calendar-week').addClass('fa-calendar-days');
                        $toggleExtraDatesBtn.removeClass('btn-danger').addClass('btn-inverse');
                        $text.text('Show 1 Month Range');
                    } else {
                        $icon.removeClass('fa-calendar-days').addClass('fa-calendar-week');
                        $toggleExtraDatesBtn.removeClass('btn-inverse').addClass('btn-danger');
                        $text.text('Show 5 Days Range');
                    }
                } else {
                    $paginator.append(`
                        <button type="button" class="btn btn-sm btn-${isFiveDays ? "inverse" : "danger"}" id="toggleExtraDatesBtn">
                            <i class="ace-icon fa fa-calendar-${isFiveDays ? "days" : "week"}"></i>
                            <span>Show ${isFiveDays ? "1 Month" : "5 Days"} Range</span>
                        </button>
                    `);
                }

                const isAllRows = dataToSetLocal === "allRows";

                if ($toggleRowsBtn.length) {
                    const $icon = $toggleRowsBtn.find('i');
                    const $text = $toggleRowsBtn.find('span');

                    if (isAllRows) {
                        $icon.removeClass('fa-list').addClass('fa-magnifying-glass-minus');
                        $toggleRowsBtn.removeClass('btn-light').addClass('btn-danger');
                        $text.text('Show Rows with Negative Balance');
                    } else {
                        $icon.removeClass('fa-magnifying-glass-minus').addClass('fa-list');
                        $toggleRowsBtn.removeClass('btn-danger').addClass('btn-light');
                        $text.text('Show All Rows');
                    }
                } else {
                    $paginator.append(`
                        <button type="button" class="btn btn-sm btn-${isAllRows ? "danger" : "light"}" id="toggleRowsBtn">
                            <i class="fa-solid fa-${isAllRows ? "magnifying-glass-minus" : "list"}"></i>
                            <span>Show ${isAllRows ? "Rows with Negative Balance" : "All Rows"}</span>
                        </button>
                    `);
                }

                const isNegativeBalTable = tblModeToSet === "negativeBalance";

                if ($setupTableBtn.length) {
                    const $icon = $setupTableBtn.find('i');
                    const $text = $setupTableBtn.find('span');

                    if (isNegativeBalTable) {
                        $icon.removeClass('fa-gauge-simple').addClass('fa-gauge-high');
                        $setupTableBtn.removeClass('btn-danger').addClass('btn-success');
                        $text.text('Negative Balance Table');
                    } else {
                        $icon.removeClass('fa-gauge-high').addClass('fa-gauge-simple');
                        $setupTableBtn.removeClass('btn-success').addClass('btn-danger');
                        $text.text('Raw Data Table');
                    }
                } else {
                    $paginator.append(`
                        <button type="button" class="btn btn-sm btn-${isNegativeBalTable ? "success" : "danger"}" id="setupTableBtn">
                            <i class="fa-solid fa-gauge-${isNegativeBalTable ? "high" : "simple"}"></i>
                            <span>${isNegativeBalTable ? "Negative Balance" : "Raw Data"} Table</span>
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
            $("#exportExcelBtn, #refreshTableBtn, #toggleExtraDatesBtn").prop("disabled", false);
        }
    });
}