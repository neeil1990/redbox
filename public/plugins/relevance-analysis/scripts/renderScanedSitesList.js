function renderScanedSitesList(sites, tfTotal = null) {
    $('.pb-3.sites').show(300)
    let percent = tfTotal / 100
    let site
    let iterator = 1;
    let tbody = $('#scaned-sites-tbody')
    $.each(sites, function (key, value) {
        let objectPercent = value['tf'] / percent
        let tf = value['tf']
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
            "<td>" + objectPercent.toFixed(1) + "% <span class='text-muted'>(" + tf.toFixed(4) + ")</span> </td>" +
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

    if (tfTotal !== null) {
        $('#total200tf').html("<span>Общая сумма tf: </span>" + tfTotal)
    }
}

