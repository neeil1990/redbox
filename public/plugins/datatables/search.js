function search(table, datatable = true) {
    if (localStorage.getItem('redbox_localstorage_item')) {
        let search = new URL(localStorage.getItem('redbox_localstorage_item'))['host']

        if (datatable) {
            table.search(search).draw()
        } else {
            console.log(search)
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
