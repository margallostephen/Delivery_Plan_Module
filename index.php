<?php require_once 'partials/header.php'; ?>

<body class="no-skin">
    <?php require_once 'partials/navbar.php'; ?>

    <div class="main-container ace-save-state" id="main-container">
        <?php require_once 'partials/sidebar.php'; ?>

        <div class="main-content">
            <div class="main-content-inner">
                <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                    <div class="ace-settings-container" id="ace-settings-container" style="display:none;">
                        <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                            <i class="ace-icon fa fa-cog bigger-130"></i>
                        </div>
                        <div class="ace-settings-box clearfix" id="ace-settings-box">
                            <div class="pull-left width-50">
                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2 ace-save-state"
                                        id="ace-settings-navbar" autocomplete="off">
                                    <label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
                                </div>
                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2 ace-save-state"
                                        id="ace-settings-sidebar" autocomplete="off">
                                    <label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
                                </div>
                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2 ace-save-state"
                                        id="ace-settings-breadcrumbs" autocomplete="off">
                                    <label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
                                </div>
                            </div>
                            <div class="pull-left width-50">
                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover"
                                        autocomplete="off">
                                    <label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
                                </div>
                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact"
                                        autocomplete="off">
                                    <label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
                                </div>
                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight"
                                        autocomplete="off">
                                    <label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="breadcrumb" style="display: flex; align-items: center;">
                        <i class="menu-icon fa fa-home bigger-200"></i>
                        <li>
                            <a href="http://172.16.1.13:8000/1_FGM/">FG MANAGEMENT SYSTEM</a>
                        </li>
                        <li class="active"> DELIVERY PLAN</li>
                    </ul>
                </div>
                <div class="page-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="widget-box widget-color-orange">
                                <div class="widget-header widget-header-small">
                                    <h6 class="widget-title" style=" display: inline-flex;">
                                        <b id="importLabel" style="color:black;">DELIVERY PLAN LIST</b>
                                    </h6>
                                </div>
                                <div class="widget-body">
                                    <div class="widget-main">
                                        <div class="p-4">
                                            <div class="header-menu-con">
                                                <input type="text" id="datePicker" autocomplete="off"
                                                    placeholder="Select a Delivery Plan Date">
                                                <div class="table-btn-container">
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        id="importExcelBtn">
                                                        <i class="ace-icon fa fa-upload"></i>
                                                        <span>
                                                            Import Data
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        id="exportExcelBtn" disabled>
                                                        <i class="fa-solid fa fa-file-export" id="btn-dl-icon-export"></i>
                                                        <span id="btn-text-export">
                                                            Export Data
                                                        </span>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" id="refreshTableBtn"
                                                        disabled>
                                                        <i class="fa-solid fa-arrows-rotate"></i>
                                                        <span id="btn-refresh-text">
                                                            Refresh Table
                                                        </span>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" id="clearAllFilterBtn"
                                                        disabled>
                                                        <i class="fa-solid fa-arrow-rotate-right"></i>
                                                        <span id="btn-clear-text">
                                                            Reset Filter
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="loader" class="loader-container">
                                                <div class="spinner"></div>
                                                <strong id="loadingText">Loading</strong>
                                            </div>
                                            <div>
                                                <div id="deliveryTable" hidden></div>
                                                <div id="noDataMessage">
                                                    <strong>No Delivery Plan Imported at the Selected Date</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'partials/footer.php'; ?>
    </div>

    <?php require_once 'modals/importModal.php'; ?>
</body>

<script type="text/javascript" src="helpers/js/addResetFilter.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="helpers/js/createDatePicker.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="helpers/js/createTable.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="helpers/js/autoPaginateTable.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="utils/js/letterKeyConverter.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="utils/js/createPlanQuantityColumn.js<?php echo randomNum(); ?>"></script>

<script type="text/javascript" src="ajax/populateTable.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="ajax/downloadFormat.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="ajax/exportExcel.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="ajax/importExcel.js<?php echo randomNum(); ?>"></script>

<script type="text/javascript">
    const staticCols = [{
            title: "CUSTOMER",
            field: "b",
            hozAlign: "left",
            vertAlign: "middle",
            headerFilter: "list",
            headerFilterPlaceholder: "Select",
            headerFilterParams: {
                valuesLookup: true,
            },
            frozen: true
        },
        {
            title: "PART NUMBER",
            field: "c",
            hozAlign: "left",
            vertAlign: "middle",
            headerFilter: "input",
            frozen: true
        },
        {
            title: "ITEM NAME",
            field: "d",
            hozAlign: "left",
            vertAlign: "middle",
            headerFilter: "input",
            frozen: true
        },
        {
            title: "REFERENCE",
            field: "e",
            hozAlign: "left",
            vertAlign: "middle",
            headerFilter: "input",
            formatter: "textarea"
        },
        {
            title: "LOCATION",
            field: "f",
            hozAlign: "left",
            vertAlign: "middle",
            headerFilter: "list",
            headerFilterPlaceholder: "Select",
            headerFilterParams: {
                valuesLookup: true,
            },
        },
        {
            title: "BACKLOG",
            field: "g",
            hozAlign: "right",
            vertAlign: "middle",
            headerFilter: "input",
            formatter: function(cell, formatterParams, onRendered) {
                const value = cell.getValue();
                const el = cell.getElement();

                const formatted = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);

                if (value > 0) {
                    el.style.color = "red";
                    el.style.fontWeight = "bold";
                }

                return !isNaN(value) && value !== '' ? formatted : value;
            }
        },
    ];

    let tableData;
    let importDatetime;
    let autoPaginateId;
    let planDateCols;
    let balDateCols;
    let showingNegative = false;
    let deliveryTable = createTable('deliveryTable', staticCols);

    const datepicker = createDatePicker("datePicker", function($picker) {
        populateTable(deliveryTable, $picker, staticCols);
    });

    $(document).ready(function() {
        populateTable(deliveryTable, datepicker, staticCols);

        addResetFilter(deliveryTable);

        $("#importExcelBtn").click(function() {
            $("#modalImport").modal("show");
        });

        $(".closeModalBtn").click(function() {
            const $modal = $(`#modalImport`);

            $modal.modal("hide");
            $(`#importExcelForm`)[0].reset();
        });

        $('#exportExcelBtn').on('click', function() {
            if (deliveryTable.getData().length == 0) {
                return toastr.warning("No available data to export", "Warning", {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 2000,
                    extendedTimeOut: 1000,
                });
            }

            Swal.fire({
                title: "Export Type",
                text: "Do you want to export filtered or all data?",
                icon: "question",
                iconColor: "#3498DB",
                showDenyButton: true,
                confirmButtonColor: "#FFB752",
                denyButtonColor: "#87B87F",
                confirmButtonText: "Filtered Data",
                denyButtonText: "All Data",
            }).then((result) => {
                let tableData;

                if (!result.isDismissed) {
                    tableData = deliveryTable.getData("active");

                    if (result.isDenied) {
                        tableData = deliveryTable.getData();
                    }

                    exportExcel(tableData, importDatetime);
                }
            });
        });

        $("#refreshTableBtn").on("click", function() {
            const $btn = $(this);
            $("#refreshTableBtn i").addClass("fa-spin");
            $btn.find("span").text("Refreshing...");
            populateTable(deliveryTable, datepicker, staticCols);
        });

        $(document).on('click', '#toggleExtraDates', function() {
            const $btn = $(this);
            const $icon = $btn.find('i');
            const $text = $btn.find('span');

            [...planDateCols.cols.slice(5), ...balDateCols.cols.slice(5)]
            .forEach(col => deliveryTable.getColumn(col.field).toggle());

            const isOneMonth = $btn.hasClass('btn-inverse');

            if (isOneMonth) {
                $icon.removeClass('fa-calendar-days').addClass('fa-calendar-week');
                $btn.removeClass('btn-inverse').addClass('btn-danger');
                $text.text('Show 5 Days Range');
            } else {
                $icon.removeClass('fa-calendar-week').addClass('fa-calendar-days');
                $btn.removeClass('btn-danger').addClass('btn-inverse');
                $text.text('Show 1 Month Range');
            }
        });

        $(document).on('click', '#toggleRowsBtn', function() {
            const delivery_plan = JSON.parse(localStorage.getItem("delivery_plan"));
            const delivery_plan_negative = JSON.parse(localStorage.getItem("delivery_plan_negative"));
            const $btn = $(this);
            const $icon = $btn.find('i');
            const $text = $btn.find('span');

            if (showingNegative) {
                deliveryTable.setData(delivery_plan);
                $icon.removeClass('fa-list').addClass('fa-magnifying-glass-minus');
                $btn.removeClass('btn-light').addClass('btn-danger');
                $text.text('Show Rows with Negative Balance');
            } else {
                deliveryTable.setData(delivery_plan_negative);
                $icon.removeClass('fa-magnifying-glass-minus').addClass('fa-list');
                $btn.removeClass('btn-danger').addClass('btn-light');
                $text.text('Show All Rows');
            }
            showingNegative = !showingNegative;
        });

    });
</script>