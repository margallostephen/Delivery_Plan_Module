$("#importExcelForm").submit(function (e) {
    e.preventDefault();

    const fileInput = $("#excelFileImport")[0];

    if (!fileInput.files.length) {
        toastr.warning("Please select a file.", "Warning", {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 2000,
            extendedTimeOut: 1000,
        });
        return;
    }

    const formData = new FormData();
    formData.append("file", fileInput.files[0]);

    $.ajax({
        url: "import_excel.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: () => {
            $('#execute_spinner').show();
            $('#execute_btn_text').text('Importing...');
            $('#submitImportExcelBtn').prop('disabled', true);
        },
        success: (response) => {
            if (response.success) {
                toastr.success(response.message, "Success", {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 2000,
                    extendedTimeOut: 1000,
                });
                $("#modalImport").modal("hide");
                $("#importExcelForm")[0].reset();

                populateTable(deliveryTable, datepicker, staticCols);
            } else {
                toastr.error(response.message, "Error");
                console.error(response);
            }
        },
        error: (error) => {
            toastr.error("An error occurred.", "Error");
            console.log(error);
        },
        complete: () => {
            $('#execute_spinner').hide();
            $('#execute_btn_text').text('EXECUTE');
            $('#submitImportExcelBtn').prop('disabled', false).text("Submit");
        }
    });
});