function createDatePicker(id, onSelectCallback) {
    return $(`#${id}`).datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        showAnim: "fadeIn",
        maxDate: 0,
        showButtonPanel: false,
        onSelect: function () {
            if (typeof onSelectCallback === "function") {
                onSelectCallback($(this));
            }
        }
    });
}
