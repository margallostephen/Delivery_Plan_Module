async function autoSetupTable(table) {
    $('#refreshTableBtn').trigger('click');

    await new Promise(r => table.on('dataLoaded', r));

    $('#toggleRowsBtn, #toggleExtraDatesBtn').each((_, el) => el.click());

    await new Promise(r => setTimeout(r, 200));

    const holder = $(table.element).find('.tabulator-tableHolder')[0];
    holder.scrollTo({ left: holder.scrollWidth, behavior: 'smooth' });

    $('#toggleAutoPaginate').trigger("click");
}