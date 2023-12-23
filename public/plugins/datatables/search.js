function search(table, datatable = true, relevance = false) {
    if (localStorage.getItem('redbox_localstorage_item')) {
        let search = new URL(localStorage.getItem('redbox_localstorage_item'))['host']

        if (datatable) {
            table.search(search).draw()
            if (relevance) {
                setTimeout(() => {
                    $('span.project_name').eq(0).trigger('click')
                }, 2000)
            }
        } else {
            let textarea = $("textarea:contains('" + search + "')");

            if (textarea.length > 0) {
                $('html, body').animate({
                    scrollTop: textarea.offset().top
                }, 1000);

                textarea.select();
            }
        }

        localStorage.removeItem('redbox_localstorage_item')
    }
}
