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
                getSuccessMessage(response.message, 5000)
                $.each(JSON.parse(response.object), function (key, value) {
                    let thoughLinks = ''
                    $.each(value['throughLinks'], function (tkey, tvalue) {
                        let url = new URL(tkey)
                        thoughLinks +=
                            '<tr>' +
                            '   <td>' + tvalue + '</td>' +
                            '   <td><a href="' + tkey + '" target="_blank" title="' + tkey + '"> ' + url.origin + ' </a></td>' +
                            '</tr>'
                    })
                    let newTable =
                        '<table>' +
                        '   <thead>' +
                        '       <tr>' +
                        '           <td>Количество повторений</td>' +
                        '           <td>Ссылка на сайт</td>' +
                        '       </tr>' +
                        '   </thead>' +
                        '   <tbody>' +
                        thoughLinks +
                        '   </tbody>' +
                        '</table>'

                    $('#though-table-body').append(
                        '<tr class="though-render">' +
                        '   <td class="col-2">' + key + '</td>' +
                        '   <td class="col-2">' + newTable + '</td>' +
                        '   <td class="col-2">' + value['tf'] + '</td>' +
                        '   <td class="col-2">' + value['idf'] + '</td>' +
                        '   <td class="col-1">' + value['repeatInTextMainPage'] + '</td>' +
                        '   <td class="col-1">' + value['repeatInLinkMainPage'] + '</td>' +
                        '   <td class="col-2" data-target="' + value['throughCount'] + '">' + value['throughCount'] + '/' + value['total'] + '</td>' +
                        '</tr>'
                    )
                });

                thoughTable.DataTable({
                    "order": [[2, "desc"]],
                    "pageLength": 50,
                    "searching": true,
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel'
                    ]
                });

                $(".dt-button").addClass('btn btn-secondary')

                setTimeout(() => {
                    $('#though-block').show()
                    scrollTo('#though-block')
                }, 500)
            } else if (response.code === 415) {
                getErrorMessage(response.message, 15000)
            }
        },
    });
})
