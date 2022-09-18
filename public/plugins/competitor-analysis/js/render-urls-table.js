function renderUrlsTable(urls) {
    $.each(urls, function (key, value) {

        let hideBlock = '<div class="card direct-chat direct-chat-primary collapsed-card pt-2 mb-0" style="background: transparent !important; box-shadow: none; border: none">' +
            '        <div class="card-header ui-sortable-handle" style="padding: 0 !important; border: 0">' +
            '            <div class="d-flex justify-content-between">' +
            '                <button type="button" class="btn btn-tool" data-card-widget="collapse">' +
            '                    <i class="fas fa-eye"></i>' +
            '                </button>' +
            '            </div>' +
            '        </div>' +
            '        <div class="card-body pl-2 pt-2">' +
            '            ' + (value['phrases']).join("<br>") +
            '        </div>' +
            '    </div>';

        $('#urls-tbody').append(
            "<tr class='render'>" +
            "   <td class='col-9'>" + key + "</td>" +
            "   <td class='col-2'>" + hideBlock + "</td>" +
            "   <td class='col-1'>" + value['count'] + "</td>" +
            "</tr>"
        )
    })

    $(document).ready(function (){
        $('#urls-table').dataTable({
            "order": [[2, "desc"]],
            "pageLength": 10,
            "searching": true,
        })
    })

    $('.urls.mt-5').show()
}
