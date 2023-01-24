$('#main_history_table').DataTable({
    "order": [[0, "desc"]],
    "pageLength": 10,
    "searching": true,
    dom: 'lBfrtip',
    buttons: [
        'copy', 'csv', 'excel'
    ],
    language: {
        lengthMenu: "_MENU_",
        search: "_INPUT_",
        paginate: {
            "first": "«",
            "last": "»",
            "next": "»",
            "previous": "«"
        },
    },
    'info': false,
    "oLanguage": {
        "sEmptyTable": "No records"
    }
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
            } else if (response.code === 415) {
                getErrorMessage(response.message, 15000)
            }
        },
    });
})
