function renderHiddenFast(data) {
    let iterator = 0
    $.each(data, function (key, result) {
        let clusterIterator = 0
        $.each(result, function (phrase, information) {
            if (phrase !== 'finallyResult') {
                iterator++
                clusterIterator++

                $('#hidden-fast-table-tbody').append(
                    '<tr class="render">' +
                    '   <td class="border-0">' + iterator + '</td>' +
                    '   <td class="border-0">' + clusterIterator + '</td>' +
                    '   <td class="border-0">' + phrase + '</td>' +
                    '</tr>'
                )
            }
        })
    })

    $('#hidden-result-fast').dataTable({
        'order': [[0, "asc"]],
        'bPaginate': false,
        'dom': 'lBfrtip',
        'buttons': [
            'copy', 'csv', 'excel'
        ]
    })

    $('#hidden-result-fast_length').remove()
    $('#hidden-result-fast_filter').remove()
    $('.dt-button').addClass('btn btn-secondary')
    $('.dt-buttons').addClass('pb-3')
}
