function renderScanedSitesList(sites) {
    var message
    $('.pb-3.sites').show(300)
    let ul = $('#scaned-sites-tbody')
    $.each(sites, function (key, value) {
        if (value['danger']) {
            message = "<td class='bg-warning'> Не удалось получить данные со страницы</td>"
        } else {
            message = "<td> Страница успешно проанализирована</td>"
        }
        ul.append(
            "<tr class='rendeer'>" +
                "<td>" + value['site'] + "</td>" +
                message +
            "</tr>"
        )
    });
}
