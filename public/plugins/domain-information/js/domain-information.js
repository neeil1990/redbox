window.onload = function () {
    $(document).ready(function () {
        $('#example').DataTable();
    });
    setTimeout(() => {
        var block = $('#example_length')
        if (localStorage.getItem('entries-information-option') !== undefined) {
            block.children().children().children().each(function () {
                if (this.value === localStorage.getItem('entries-information-option')) {
                    $(this).parent().val(this.value).change();
                }
            });
        }
        block.children().children().change(function () {
            localStorage.setItem('entries-information-option', $('#example_length').children().children().val())
        });
    }, 250)
}


