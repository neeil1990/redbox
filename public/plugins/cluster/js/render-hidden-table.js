function renderHiddenTable(data) {
    let iterator = 0
    $.each(data, function (key, result) {
        let clusterIterator = 0
        $.each(result, function (phrase, information) {
            if (phrase !== 'finallyResult') {
                iterator++
                clusterIterator++

                let phraseForm = 'phrased' in information ? information['phrased']['number'] : '0'
                let targetForm = 'target' in information ? information['target']['number'] : '0'
                let baseForm = 'based' in information ? information['based']['number'] : '0'
                let groupName = 'groupName' in result['finallyResult'] ? result['finallyResult']['groupName'] : ' '

                let relevance
                if ('link' in information) {
                    relevance = information['link']
                } else if ('relevance' in information && information['relevance'] !== null) {
                    relevance = information['relevance'][0]
                } else {
                    relevance = ''
                }

                $('#hidden-table-tbody').append(
                    '<tr class="render">' +
                    '   <td class="border-0">' + iterator + '</td>' +
                    '   <td class="border-0">' + clusterIterator + '</td>' +
                    '   <td class="border-0">' + phrase + '</td>' +
                    '   <td class="border-0">' + groupName + '</td>' +
                    '   <td class="border-0" id="hidden-relevance-phrase-' + phrase.replaceAll(' ', '-') + '">' + relevance + '</td>' +
                    '   <td class="border-0">' + baseForm + '</td>' +
                    '   <td class="border-0">' + phraseForm + '</td>' +
                    '   <td class="border-0">' + targetForm + '</td>' +
                    '</tr>'
                )
            }
        })
    })

    $('#block-for-downloads-files').show()

    let table = $('#hidden-result-table').DataTable({
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

    return table;
}
