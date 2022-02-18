function renderScanedSitesList(sites) {
    var td
    $('.pb-3.sites').show(300)
    let tbody = $('#scaned-sites-tbody')
    $.each(sites, function (key, value) {
        if (value['danger']) {
            td = "<td class='bg-warning'> Не удалось получить данные со страницы</td>"
        } else {
            td = "<td> Страница успешно проанализирована</td>"
        }
        tbody.append(
            "<tr class='render'>" +
                "<td>" + value['site'] + "</td>" +
            td +
            "</tr>"
        )
    });
}
