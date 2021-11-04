window.onload = function () {
    let block = $('#example_length')
    if (localStorage.getItem('entries-option') !== undefined) {
        block.children().children().children().each(function () {
            if (this.value === localStorage.getItem('entries-option')) {
                $(this).parent().val(this.value).change();
            }
        });
    }

    block.children().children().change(function () {
        localStorage.setItem('entries-option', $('#example_length').children().children().val())
    });
}

