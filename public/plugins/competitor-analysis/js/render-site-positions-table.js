/**
 *
 * @param domainsPosition
 */
function renderSitePositionsTable(domainsPosition) {
    $.each(domainsPosition, function (key, value) {
        $('#positions-tbody').append(
            '<tr class="render">' +
            '  <td>' + key + '</td>' +
            '  <td>' + value['topPercent'] + ' <span class="text-muted">' + value['text'] + '</span></td>' +
            '  <td>' + value['avg'] + '</td>' +
            '<tr>'
        )
    })

    $('.positions').show()

    $(document).ready(function () {
        // todo почему-то генерируются пустые строки
        $('tr:empty').remove();

        $('#positions').dataTable({
            "order": [[2, "asc"]],
        })
    })
}
