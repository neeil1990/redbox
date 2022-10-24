function renderHiddenTable(data) {
    let iterator = 0
    $.each(data, function (key, result) {
        let clusterIterator = 0
        $.each(result, function (phrase, information) {
            if (phrase !== 'finallyResult') {
                iterator++
                clusterIterator++

                let based = 'phrased' in information ? information['phrased']['number'] : ' '
                let target = 'target' in information ? information['target']['number'] : ' '

                $('#hidden-table-tbody').append(
                    '<tr class="render">' +
                    '   <td class="border-0">' + iterator + '</td>' +
                    '   <td class="border-0">' + clusterIterator + '</td>' +
                    '   <td class="border-0">' + phrase + '</td>' +
                    '   <td class="border-0">' + result['finallyResult']['groupName'] + '</td>' +
                    '   <td class="border-0">' + information['based']['number'] + '</td>' +
                    '   <td class="border-0">' + based + '</td>' +
                    '   <td class="border-0">' + target + '</td>' +
                    '</tr>'
                )
            }
        })
    })

    $('#block-for-downloads-files').show()

    $('#hidden-result-table').dataTable({
        'order': [[5, "desc"]],
        'bPaginate': false,
        'orderCellsTop': true,
        'dom': 'lBfrtip',
        'buttons': [
            'copy', 'csv', 'excel'
        ]
    })

    $('#hidden-result-table_filter').remove()
    $('.dt-button').addClass('btn btn-secondary')
    $('.dt-buttons').addClass('pb-3')
}
