function exportExcel(tableData, importDatetime) {
    const now = new Date();
    const downloadDatetime =
        now.getFullYear().toString().slice(2) +
        (now.getMonth() + 1).toString().padStart(2, '0') +
        now.getDate().toString().padStart(2, '0') + '_' +
        now.getHours().toString().padStart(2, '0') +
        now.getMinutes().toString().padStart(2, '0') +
        now.getSeconds().toString().padStart(2, '0');

    $("#btn-text-export").text('Exporting...');
    $('#btn-dl-icon-export').removeClass('fa-download').addClass('fa-spinner fa-spin');
    $('#exportExcelBtn').prop('disabled', true);

    fetch('export_excel.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            data: tableData,
            importDatetime: importDatetime
        })
    }).then(response => {
        if (!response.ok) throw new Error("Network response was not ok.");
        return response.blob(); // get the binary blob
    }).then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${downloadDatetime}_Delivery_Plan.xlsx`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    }).catch(error => {
        console.error('Download failed:', error);
    }).finally(() => {
        $("#btn-text-export").text('Export Data');
        $('#btn-dl-icon-export').removeClass('fa-spinner fa-spin').addClass('fa-download');
        $('#exportExcelBtn').prop('disabled', false);
    });
}