function renderScanedSitesList(sites, link) {
    var site
    let iterator = 1;
    let inList = false;
    $('.pb-3.sites').show(300)
    let tbody = $('#scaned-sites-tbody')
    $.each(sites, function (key, value) {
        let warning = value['danger']
            ? "<td class='bg-warning'> Не удалось получить данные со страницы </td>"
            : "<td> Страница успешно проанализирована </td>"
        if (value['mainPage']) {
            site = "<td class='bg-success'>" + value['site'] + "</td>"
            inList = true
        } else {
            site = "<td>" + value['site'] + "</td>";
        }
        tbody.append(
            "<tr class='render'>" +
            "<td>" + iterator + "</td>" +
            site +
            warning +
            "</tr>"
        )
        iterator++
    });

    if (!inList) {
        tbody.append(
            "<tr class='render'>" +
            "<td>" + iterator + "</td>" +
            "<td>" + link + "</td>" +
            "<td class='bg-warning'> Ваша страница не попала в топ </td>" +
            "</tr>"
        )
    }
}
