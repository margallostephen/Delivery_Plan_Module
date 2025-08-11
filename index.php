<?php require_once 'partials/header.php'; ?>

<div id="navbar" class="navbar navbar-default ace-save-state" style="background-color: #2234ae;
background-image: linear-gradient(315deg, #191714 -120%,  #2234ae 120%);">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar"
            style="background-color: darkblue; border: 0.5px solid black;">
            <span class="sr-only">Toggle sidebar</span>
        </button>
        <div class="navbar-header pull-left">
            <asd class="navbar-brand">
                <small>
                    <h1 id="litheader"
                        style="font-size: 20px; font-weight: bolder; margin: 0;padding: 0;  display: inline-block; font-family: Tahoma;">
                        <span class="white" id="id-text2">PRIMA TECH PHILS., INC. </span>
                    </h1>
                </small>
            </asd>
        </div>
    </div>
</div>

<body class="no-skin">
    <div class="main-container ace-save-state" id="main-container">
        <div id="sidebar" class="sidebar responsive ace-save-state">
            <ul class="nav nav-list">
                <li id="li_1">
                    <a href="/<?php $path_folder = "1_FGM";
                    echo $path_folder; ?>/">
                        <i class="menu-icon fa fa-home"></i>
                        <span class="menu-text"> MAIN </span>
                    </a>
                    <b class="arrow"></b>
                </li>

                <li id="li_4">
                    <a href="/<?php echo $path_folder; ?>/FMS03/">
                        <i class="menu-icon fa fa-tachometer"></i>
                        <span class="menu-text"> DASHBOARD </span>
                    </a>
                    <b class="arrow"></b>
                </li>

                <li id="li_2">
                    <a href="/<?php echo $path_folder; ?>/FMS01/">
                        <i class="menu-icon fa fa-th"></i>
                        <span class="menu-text"> RECORD </span>
                    </a>
                    <b class="arrow"></b>
                </li>

                <li id="li_3">
                    <a href="/<?php echo $path_folder; ?>/FMS02/">
                        <i class="menu-icon fa fa-usd"></i>
                        <span class="menu-text"> PRICE INFO </span>
                    </a>
                    <b class="arrow"></b>
                </li>
                <li class="sidebar-btn" id="dashboard">
                    <a href="<?php echo 'http://172.16.1.13:8000/1_DPS' ?>">
                        <i class="menu-icon fa fa-table"></i>
                        <span class="menu-text">DELIVERY PLAN</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
                <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state"
                    data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
            </div>
        </div>

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
                                                    <button type="button" class="btn btn-sm btn-inverse"
                                                        id="downloadFormatBtn">
                                                        <span>
                                                            <i id="btn-dl-icon" class="ace-icon fa fa-table"></i>
                                                        </span>
                                                        <span id="btn-text">
                                                            Generate Import Format
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        id="importExcelBtn">
                                                        <span>
                                                            <i class="ace-icon fa fa-upload"></i>
                                                        </span>
                                                        <span>
                                                            Import Data
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        id="exportExcelBtn" disabled>
                                                        <span>
                                                            <i id="btn-dl-icon-export"
                                                                class="ace-icon fa fa-download"></i>
                                                        </span>
                                                        <span id="btn-text-export">
                                                            Export Data
                                                        </span>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" id="clearAllFilterBtn"
                                                        disabled>
                                                        <span>
                                                            <i class="fa-solid fa-arrow-rotate-right"></i>
                                                        </span>
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
        <footer class="footer">
            <div class="footer-inner">
                <div class="footer-content">
                    <span class="bigger-120 blue bolder">
                        <?php echo "FG MANAGEMENT SYSTEM "; ?>
                    </span>
                    <span class="bigger-120">
                        © March 2023
                    </span>
                </div>
            </div>
        </footer>
    </div>

    <div id="modalImport" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Import Data to Table</h4>
                </div>
                <form id="importExcelForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="file" class="form-control" id="excelFileImport" accept=".xlsx,.xls" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary" id="submitImportExcelBtn">
                            <span id="execute_spinner" hidden>
                                <i class="ace-icon fa fa-spinner fa-spin white"></i>
                            </span>
                            <span id="execute_btn_text"> Submit</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-default closeModalBtn"
                            data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript" src="helpers/js/addResetFilter.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="helpers/js/createDatePicker.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="helpers/js/createTable.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="utils/js/letterKeyConverter.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="utils/js/createPlanQuantityColumn.js<?php echo randomNum(); ?>"></script>

<script type="text/javascript" src="ajax/populateTable.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="ajax/downloadFormat.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="ajax/exportExcel.js<?php echo randomNum(); ?>"></script>
<script type="text/javascript" src="ajax/importExcel.js<?php echo randomNum(); ?>"></script>

<script type="text/javascript">
    const staticCols = [
        {
            title: "CUSTOMER", field: "b", hozAlign: "left", vertAlign: "middle",
            headerFilter: "list",
            headerFilterPlaceholder: "Select",
            headerFilterParams: {
                valuesLookup: true,
            },
            frozen: true
        },
        { title: "PART NUMBER", field: "c", hozAlign: "left", vertAlign: "middle", headerFilter: "input", frozen: true },
        { title: "ITEM NAME", field: "d", hozAlign: "left", vertAlign: "middle", headerFilter: "input", frozen: true },
        { title: "REFERENCE", field: "e", hozAlign: "left", vertAlign: "middle", headerFilter: "input", formatter: "textarea" },
        {
            title: "LOCATION", field: "f", hozAlign: "left", vertAlign: "middle",
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
            formatter: function (cell, formatterParams, onRendered) {
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
    let deliveryTable = createTable('deliveryTable', staticCols);

    const datepicker = createDatePicker("datePicker", function ($picker) {
        populateTable(deliveryTable, $picker, staticCols);
    });

    $(document).ready(function () {
        populateTable(deliveryTable, datepicker, staticCols);

        addResetFilter(deliveryTable);

        $("#importExcelBtn").click(function () {
            $("#modalImport").modal("show");
        });

        $(".closeModalBtn").click(function () {
            const $modal = $(`#modalImport`);

            $modal.modal("hide");
            $(`#importExcelForm`)[0].reset();
        });

        $('#exportExcelBtn').on('click', function () {
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

                    exportExcel(tableData, importDatetime)
                }
            });
        });
    });
</script>
