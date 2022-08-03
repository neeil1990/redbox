$('#main_history_table').DataTable({
    "order": [[0, "desc"]],
    "pageLength": 10,
    "searching": true,
    dom: 'lBfrtip',
    buttons: [
        'copy', 'csv', 'excel'
    ]
});

$(".dt-button").addClass('btn btn-secondary')

$('.repeat-scan-unique-sites').on('click', function () {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/repeat-scan-unique-sites",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: $(this).attr('data-target'),
        },
        success: function (response) {
            if (response.code === 200) {
                getSuccessMessage(response.message)
                $.each(response.object, function (key, value) {
                    $('#history-state-' + value).html(
                        '<p>Обрабатывается..</p>' +
                        '<div class="text-center" id="preloaderBlock">' +
                        '        <div class="three col">' +
                        '            <div class="loader" id="loader-1"></div>' +
                        '        </div>' +
                        '</div>'
                    )
                })
            } else if (response.code === 415) {
                getErrorMessage(response.message)
            }
        },
    });
})

$('.start-through-analyse').on('click', function () {
    $('.though-render').remove()
    let thoughTable = $('#though-table')
    thoughTable.dataTable().fnDestroy();
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/start-through-analyse",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: $(this).attr('data-target'),
        },
        success: function (response) {
            if (response.code === 200) {
                getSuccessMessage(response.message)
                $.each(JSON.parse(response.object), function (key, value) {
                    let thoughLinks = ''
                    $.each(value['throughLinks'], function (tkey, tvalue) {
                        thoughLinks += '<div><a href="' + tkey + '" target="_blank" title="'+ tkey +'"> ' + tvalue + ' </a></div>'
                    })
                    $('#though-table-body').append(
                        '<tr class="though-render">' +
                        '   <td class="col-2">' + key + '</td>' +
                        '   <td class="col-2">' + thoughLinks + '</td>' +
                        '   <td class="col-2">' + value['tf'] + '</td>' +
                        '   <td class="col-2">' + value['idf'] + '</td>' +
                        '   <td class="col-1">' + value['repeatInTextMainPage'] + '</td>' +
                        '   <td class="col-1">' + value['repeatInLinkMainPage'] + '</td>' +
                        '   <td class="col-2" data-target="' + value['throughCount'] + '">' + value['throughCount'] + '/' + value['total'] + '</td>' +
                        '</tr>'
                    )
                });

                thoughTable.DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 50,
                    "searching": true,
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel'
                    ]
                });

                $(".dt-button").addClass('btn btn-secondary')

                thoughTable.show()

            } else if (response.code === 415) {
                getErrorMessage(response.message, 15000)
            }
        },
    });
})
