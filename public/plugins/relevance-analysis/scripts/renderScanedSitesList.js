function renderScanedSitesList(sites) {
    $('.pb-3.sites').show(300)
    let site
    let iterator = 1;
    let tbody = $('#scaned-sites-tbody')
    $.each(sites, function (key, value) {
        let warning = value['danger']
            ? "<td class='bg-warning'> Не удалось получить данные со страницы </td>"
            : "<td> Страница успешно проанализирована </td>"
        if (value['mainPage']) {
            if (!value['inRelevance']) {
                value['site'] += " <span class='text-muted'>(не попала в топ)</span>"
            }
            site = "<td style='background: #4eb767c4'>" + value['site'] + "</td>"
        } else {
            site = "<td>" + value['site'] + "</td>";
        }
        tbody.append(
            "<tr class='render'>" +
            "<td>" + iterator + "</td>" +
            site +
            "<td>" + value['width'] + "% </td>" +
            "<td> In progress..</td>" +
            warning +
            "</tr>"
        )
        iterator++
    });

    $(document).ready(function () {
        $('#scaned-sites').DataTable({
            "order": [[0, "asc"]],
            "pageLength": 25,
            "searching": true,
        });
    });
}

