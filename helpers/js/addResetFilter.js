function addResetFilter(table) {
    $("#clearAllFilterBtn").off("click").on("click", function () {
        const $btn = $(this);
        $btn.prop("disabled", true);

        const $icon = $btn.find(".fa-solid.fa-arrow-rotate-right");
        const $text = $("#btn-clear-text");

        $icon.addClass("fa-spin");
        $text.text("Resetting...");

        setTimeout(() => {
            table.getColumns()
                .filter(col => col.getDefinition()?.headerFilter)
                .forEach(col => col.setHeaderFilterValue(""));

            $icon.removeClass("fa-spin");
            $text.text("Reset Filter");
        }, 500);
    });

    table.on("dataFiltering", filters => {
        const activeFields = filters.map(f => f.field);

        table.getColumns().forEach(col => {
            const field = col.getField();
            const colElement = $(`.tabulator-col[tabulator-field="${field}"]`);
            const filterElement = colElement.find(".tabulator-header-filter");
            const headerContent = $(col.getElement()).find('.tabulator-col-content');
            const clearIcon = colElement.find(".clear-icon");

            if (!clearIcon.length) {
                const icon = $('<i class="fa fa-remove clear-icon" style="cursor:pointer; margin-right:5px;"></i>');
                icon.on("click", e => {
                    e.stopPropagation();
                    table.setHeaderFilterValue(field, "");
                });
                filterElement.before(icon);
            }

            headerContent.toggleClass("tabulator-header-highlight", activeFields.includes(field));
            colElement.find(".clear-icon").toggle(activeFields.includes(field));
        });

        $("#btn-clear-text")
            .text(
                activeFields.length > 0
                    ? `Reset (${activeFields.length}) Filter${activeFields.length > 1 ? 's' : ''}`
                    : "Reset Filter"
            );

        $("#clearAllFilterBtn").prop("disabled", activeFields.length === 0);
    });
}
