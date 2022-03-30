function renderScannedSitesList(sites, coverageInfo = null) {
    $('.pb-3.sites').show(300)
    let percent = coverageInfo / 100
    let site
    let iterator = 1;
    let tbody = $('#scaned-sites-tbody')
    $.each(sites, function (key, value) {
        let tf = value['tf']
        let objectPercent = tf / percent
        let warning = value['danger']
            ? "<td class='bg-warning'> Не удалось получить данные со страницы </td>"
            : "<td> Страница успешно проанализирована </td>"
        if (value['mainPage']) {
            if (!value['inRelevance']) {
                value['site'] += " <span class='text-muted'>(сайт не попал в топ)</span>"
            }
            site = "<td style='background: #4eb767c4'>" + value['site'] + "</td>"
        } else {
            site = "<td>" + value['site'] + "</td>";
        }
        tbody.append(
            "<tr class='render'>" +
            "<td>" + iterator + "</td>" +
            site +
            "<td>" + value['coverage'] + "% </td>" +
            "<td data-order='" + objectPercent + "'>" + objectPercent.toFixed(1) + "% <span class='text-muted'>(" + tf.toFixed(4) + ")</span> </td>" +
            "<td> In progress..</td>" +
            "<td>" + value['points'] + "</td>" +
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

