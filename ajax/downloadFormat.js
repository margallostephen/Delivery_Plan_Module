$("#downloadFormatBtn").click(function () {
    $("#btn-text").text('Generating...');
    $('#btn-dl-icon').removeClass('fa-table').addClass('fa-spinner fa-spin');
    $('#downloadFormatBtn').prop('disabled', true);

    fetch('/Delivery_Plan_Module/download_format.php', {
        method: 'POST',
    })
        .then(res => res.blob())
        .then(blob => {
            const url = URL.createObjectURL(blob);
            const a = Object.assign(document.createElement('a'), {
                href: url, download: 'Delivery_Plan_Import_Format.xlsx'
            });
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
            toastr.success("Generated the import format file.", "Success", {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 2000,
                extendedTimeOut: 1000,
                showMethod: 'show',
                hideMethod: 'hide',
                showDuration: 0,
                hideDuration: 0
            });
        })
        .catch(() => toastr.error("Generate failed.", "Error", {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 2000,
        }))
        .finally(() => {
            $("#btn-text").text('Generate Excel Format');
            $('#btn-dl-icon').removeClass('fa-spinner fa-spin').addClass('fa-table');
            $('#downloadFormatBtn').prop('disabled', false);
        });
});