/**
 * @param analysedSites
 */
function renderTopSites(analysedSites) {
    $.each(analysedSites, function (phrase, sites) {
        let tr = '<tr style="height: 80px" class="render">'
        tr += '<td>' + phrase + '</td>'
        $.each(sites, function (site, info) {
            let url = new URL(site)

            let btnGroup =
                '<div class="btn-group">' +
                '   <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-tool dropdown-toggle">' +
                '   <i class="fas fa-external-link-alt"></i>' +
                '   </button>' +
                '       <div role="menu" class="dropdown-menu dropdown-menu-left">' +

                '       <a target="_blank" class="dropdown-item" href="' + url['href'] + '">' +
                '       <i class="fas fa-external-link-alt"></i> Перейти на посадочную страницу</a>' +

                '       <a target="_blank" class="dropdown-item" href="' + url['origin'] + '">' +
                '       <i class="fas fa-external-link-alt"></i> Перейти на сайт</a>' +
                '       <a target="_blank" class="dropdown-item" href="/redirect-to-text-analyzer/' + url['origin'].replace(/\\|\//g, 'abc') + '">' +
                '       <i class="fas fa-external-link-alt"></i> Проанализировать текст</a>' +

                '   </div>' +
                '</div>'

            let danger = info['danger'] ? 'danger' : ''

            let infoBlock

            if (info['danger']) {
                infoBlock = '<div class="text-danger mt-2">Сайт защищен от сбора информации, советуем проанализировать вручную</div>'
            } else {
                infoBlock = ''
                $.each(info['meta'], function (key, values) {
                    if (values.length > 0) {
                        infoBlock +=
                            '<div class="mt-2">' +
                            '   <span class="text-info">' + key + '</span>' +
                            '   <br>' +
                            '   <span>' +
                            '      ' + values.join('<br>') +
                            '   </span>' +
                            '</div>'
                    }

                })
            }

            infoBlock = getStub(url['host'], btnGroup, infoBlock)

            tr += '<td class="' + danger + '">' + infoBlock + '</td>'

        })

        tr += '</tr>'

        $('#top-sites-body').append(tr)
    })

    $('.top-sites.mt-5').show()
}

/**
 *
 * @param host
 * @param btnGroup
 * @param html
 * @returns {string}
 */
function getStub(host, btnGroup, html) {

    return '<div class="card direct-chat direct-chat-primary collapsed-card" style="background: transparent !important; box-shadow: none; border: none">' +
        '        <div class="card-header ui-sortable-handle" style="padding: 0 !important; border: 0">' +
        '            <div class="d-flex justify-content-between">' +
                        '<div>' + host + btnGroup + '</div>' +
        '                <button type="button" class="btn btn-tool" data-card-widget="collapse">' +
        '                    <i class="fas fa-plus"></i>' +
        '                </button>' +
        '            </div>' +
        '        </div>' +
        '        <div class="card-body" style="display: none;">' +
        '            ' + html +
        '        </div>' +
        '    </div>';
}
