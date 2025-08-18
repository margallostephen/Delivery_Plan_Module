<div id="modalImport" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import Data to Table</h4>
            </div>
            <form id="importExcelForm">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-8">
                                <input type="file" class="form-control" id="excelFileImport" accept=".xlsx,.xls" />
                            </div>
                            <div class="col-lg-4">
                                <button type="button" class="btn btn-sm btn-inverse"
                                    id="downloadFormatBtn">
                                    <span>
                                        <i id="btn-dl-icon" class="ace-icon fa fa-table"></i>
                                    </span>
                                    <span id="btn-text">
                                        Generate Format
                                    </span>
                                </button>
                            </div>
                        </div>
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