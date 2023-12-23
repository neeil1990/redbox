function renderRecommendationsTable(recommendations, count, words) {
    $('#rec').show()
    let tBody = $('#recommendationsTBody')
    let newRows = ''

    $.each(recommendations, function (key, value) {
        let add = ''
        let remove = ''
        if (value['add'] != '0') {
            add = 'bg-warning-elem'
        } else {
            remove = 'bg-warning-elem'
        }
        let diapasonOrder = String(value['diapason'])
        diapasonOrder = diapasonOrder.substr(diapasonOrder.length - 2)

        let addOrder = String(value['add'])
        addOrder = addOrder.substr(addOrder.length - 2)

        let removeOrder = String(value['remove'])
        removeOrder = removeOrder.substr(removeOrder.length - 2)

        newRows +=
            "<tr class='render'>" +
            "<td class='text-center'> <i class='fa fa-trash remove-recommendation'></i> </td>" +
            "<td class='col-1'>" + key + "</td>" +
            "<td class='col-1'>" + value['tf'] + "</td>" +
            "<td class='col-2'>" + value['avg'] + "</td>" +
            "<td class='col-2'>" + value['onPage'] + "</td>" +
            "<td class='col-2' data-order='" + diapasonOrder + "'>" + value['diapason'] + "</td>" +
            "<td class='col-1'>" + value['spam'] + "</td>" +
            "<td class='col-1 " + add + "' data-order='" + addOrder + "'>" + value['add'] + "</td>" +
            "<td class='col-1 " + remove + "'  data-order='" + removeOrder + "'>" + value['remove'] + "</td>" +
            "</tr>"
    })

    tBody.html(newRows)

    if ($.fn.DataTable.fnIsDataTable($('#recommendations'))) {
        $('#recommendations').dataTable().fnDestroy();
    }

    let table = $('#recommendations').DataTable({
        "order": [[2, "desc"]],
        "pageLength": count,
        "searching": true,
        dom: 'lBfrtip',
        buttons: [
            'copy', 'csv', 'excel'
        ],
        language: {
            paginate: {
                "first": "«",
                "last": "»",
                "next": "»",
                "previous": "«"
            },
        },
        "oLanguage": {
            "sSearch": words.search + ":",
            "sLengthMenu": words.show + " _MENU_ " + words.records,
            "sEmptyTable": words.noRecords,
            "sInfo": words.showing + " " + words.from + "  _START_ " + words.to + " _END_ " + words.of + " _TOTAL_ " + words.entries,
        }
    });

    $('#recommendations tbody').on('click', '.fa.fa-trash.remove-recommendation', function () {
        table.row($(this).parents('tr')).remove().draw();
    });
}
