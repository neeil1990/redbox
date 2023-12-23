function renderSitePositionsTable(domainsPosition, pageLength) {
    $.each(domainsPosition, function (domain, info) {
        let hideBlock = '<div class="card direct-chat direct-chat-primary collapsed-card pt-2 mb-0" style="background: transparent !important; box-shadow: none; border: none">' +
            '        <div class="card-header ui-sortable-handle" style="padding: 0 !important; border: 0">' +
            '            <div class="d-flex justify-content-between">' +
            '                <button type="button" class="btn btn-tool pt-1 pb-2 pl-0 pr-0" data-card-widget="collapse">' +
            '                    <i class="fas fa-eye"></i>' +
            '                </button>' +
            '            </div>' +
            '        </div>' +
            '        <div class="card-body pt-2">' +
            '            ' + (info['phrases']).join("<br>") +
            '        </div>' +
            '    </div> ';

        $('#positions-tbody').append(
            '<tr class="render">' +
            '  <td>' + domain + '</td>' +
            '  <td data-order="' + info['topPercent'] + '">' + info['topPercent'] + '% <span class="text-muted"> ' + info['text'] + '</span> ' + hideBlock + '</td>' +
            '  <td>' + info['avg'] + '</td>' +
            '</tr>'
        )
    })
}
