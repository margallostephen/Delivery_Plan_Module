const AutoPaginator = {
    id: null,
    start(table, interval = 10000) {
        this.stop();

        this.id = setInterval(() => {
            const currentPage = table.getPage();
            const maxPage = table.getPageMax();

            if (currentPage === maxPage) {
                table.setPage(1);
                refreshData(table);
            } else {
                table.setPage(currentPage + 1);
            }
        }, interval);
    },
    stop() {
        if (this.id) {
            clearInterval(this.id);
            this.id = null;
        }
    }
};

$(document).on('click', '#toggleAutoPaginateBtn', function () {
    const $btn = $(this);
    const $icon = $btn.find('i');
    const $text = $btn.find('span');

    if ($icon.hasClass('fa-play')) {
        AutoPaginator.start(deliveryTable);
        $btn.toggleClass('btn-primary btn-danger');
        $icon.removeClass('fa-play').addClass('fa-stop');
        $text.text('Stop Auto Paginate');
    } else {
        AutoPaginator.stop();
        $btn.toggleClass('btn-danger btn-primary');
        $icon.removeClass('fa-stop').addClass('fa-play');
        $text.text('Auto Paginate');
    }
});
