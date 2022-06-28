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
