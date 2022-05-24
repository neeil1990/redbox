let mainHistory = $('#main_history_table').DataTable({
    "order": [[0, "desc"]],
    "pageLength": 10,
    "searching": true,
    dom: 'lBfrtip',
    buttons: [
        'copy', 'csv', 'excel'
    ]
});

$(".dt-button").addClass('btn btn-secondary')

$(".group-name-input").change(function () {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/change-group-name",
        data: {
            id: $(this).attr('data-target'),
            name: $(this).val()
        },
        success: function () {
            $('#toast-container').show(300)
            $('#message-info').html('Название успешно изменено')
            setInterval(function () {
                $('#toast-container').hide(300)
            }, 3000)
        },
    });
});
