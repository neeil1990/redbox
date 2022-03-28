function renderScanedSitesList(sites, coverageInfo = null) {
    $('.pb-3.sites').show(300)
    let percent200 = coverageInfo['200'] / 100
    let percent600 = coverageInfo['600'] / 100
    let site
    let iterator = 1;
    let tbody = $('#scaned-sites-tbody')
    $.each(sites, function (key, value) {
        let tf200 = value['tf200']
        let objectPercent200 = tf200 / percent200
        let tf600 = value['tf600']
        let objectPercent600 = tf600 / percent600
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
            "<td>" + value['width'] + "% </td>" +
            "<td data-order='" + objectPercent200 + "'>" + objectPercent200.toFixed(1) + "% <span class='text-muted'>(" + tf200.toFixed(4) + ")</span> </td>" +
            "<td data-order='" + objectPercent600 + "'>" + objectPercent600.toFixed(1) + "% <span class='text-muted'>(" + tf600.toFixed(4) + ")</span> </td>" +
            "<td>" + value['percentCoverageWords'] + "% </td>" +
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

    if (coverageInfo !== null) {
        $('#total200tf').html(
            "<p>" + "<span>Общая сумма tf (топ 200): </span>" + coverageInfo['200'] + "</p>" +
            "<p>" + "<span>Общая сумма tf (топ 600): </span>" + coverageInfo['600'] + "</p>"
        )
    }
}

