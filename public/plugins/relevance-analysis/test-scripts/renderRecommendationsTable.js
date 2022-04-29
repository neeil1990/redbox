function renderRecommendationsTable(recommendations) {
    $('.pb-3.recommendations').show()
    let tBody = $('#recommendationsTBody')

    $.each(recommendations, function (key, value) {
        var add = ''
        var remove = ''
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

        tBody.append(
            "<tr>" +
            "<td class='text-center'> <i class='fa fa-trash remove-recommendation'></i> </td>" +
            "<td class='col-1'>" + key + "</td>" +
            "<td class='col-1'>" + value['tf'] + "</td>" +
            "<td class='col-2'>" + value['onPage'] + "</td>" +
            "<td class='col-2'>" + value['avg'] + "</td>" +
            "<td class='col-2' data-order='" + diapasonOrder + "'>" + value['diapason'] + "</td>" +
            "<td class='col-1'>" + value['spam'] + "</td>" +
            "<td class='col-1 " + add + "' data-order='" + addOrder + "'>" + value['add'] + "</td>" +
            "<td class='col-1 " + remove + "'  data-order='" + removeOrder + "'>" + value['remove'] + "</td>" +
            "</tr>"
        )
    })

    var table = $('#recommendations').DataTable({
        "order": [[1, "desc"]],
        "pageLength": 10,
        "searching": true,
        dom: 'lBfrtip',
        buttons: [
            'copy', 'csv', 'excel'
        ]
    });

    setTimeout(() => {
        $('#recommendations').wrap("<div style='width: 100%; overflow-x: scroll; max-height:90vh;'></div>")
    }, 500);


    $('#recommendations tbody').on('click', '.fa.fa-trash.remove-recommendation', function () {
        table.row($(this).parents('tr')).remove().draw();
    });
}
