function renderResultTableFast(data, count, target) {
    let iterator = 0
    let style
    let targetPhrase

    $.each(data, function (key, result) {
        let clusterId = (Math.random() + 1).toString(36).substring(7)
        let clusterIterator = 0
        let newTableRows = ''
        let clusterSites = ''
        let newRow = ''

        $.each(result['finallyResult']['sites'], function (site, count) {
            clusterSites +=
                '<div>' +
                '   <a href="' + site + '">' + new URL(site)['host'] + '</a> :' + count +
                '</div>'
        })

        $.each(result, function (phrase, information) {
            if (phrase !== 'finallyResult') {
                clusterIterator++
                iterator++

                let fullUrls = information['sites'].join("\r")
                let changedBg = "basedNormal" in information;
                let sites = ''

                $.each(information['sites'], function (key, site) {
                    sites += '<div>' +
                        '   <a href="' + site + '" target="_blank">' + new URL(site)['host'] + '</a>' +
                        '</div>'
                })

                if (changedBg) {
                    style = 'bg-cluster-warning'
                } else {
                    style = ''
                }

                let merge = ''
                if ('merge' in information) {
                    $.each(information['merge'], function (key, value) {
                        merge = '<span class="__helper-link ui_tooltip_w">' +
                            '      <i class="fa fa-question"></i>' +
                            '      <span class="ui_tooltip __right" style="min-width: 550px;">' +
                            '          <span class="ui_tooltip_content">' + key + '<br> ' + value + '</span>' +
                            '      </span>' +
                            '  </span>'
                    })
                }

                targetPhrase = changedBg ? information['basedNormal'] : phrase
                let title = changedBg ? `title='Ваша фраза "${phrase}" была изменена'` : ''

                newTableRows +=
                    '<tr class="fast-render">' +
                    '   <td>' + iterator + '</td> ' +
                    '   <td> ' + clusterIterator + '</td> ' +
                    '   <td class="' + style + '" ' + title + '> ' +
                    '       <div class="d-flex"> ' +
                    '          <div class="mr-2" id="cluster-id-' + clusterId + '">' + targetPhrase + '</div> ' +
                    '          <div>' +
                    '             <i class="fa fa-copy copy-full-urls" data-target="' + iterator + '" title="копировать полные ссылки сайтов"></i>' +
                    '             <div style="display: none" id="hidden-urls-block-' + iterator + '">' + fullUrls + '</div>' +
                    '             <span class="__helper-link ui_tooltip_w">' +
                    '                 <i class="fa fa-paperclip"></i>' +
                    '                 <span class="ui_tooltip __bottom" style="min-width: 250px;">' +
                    '                     <span class="ui_tooltip_content">' + sites + '</span>' +
                    '                 </span>' +
                    '             </span>' +
                    merge +
                    '          </div> ' +
                    '       </div>' +
                    '   </td> ' +
                    '</tr>'
            }
        })

        newRow +=
            '<tr class="fast-render">' +
            '   <td class="p-0">' +
            '       <table class="table table-hover text-nowrap no-footer render-table-fast" id="' + key.replaceAll(' ', '-').substr(0, 30) + '" style="width: 100%">' +
            '       <thead>' +
            '           <tr>' +
            '               <th title="Порядковый номер">#</th>' +
            '               <th title="Порядковый номер в кластере">##</th>' +
            '               <th>Ключевой запрос</th>' +
            '           </tr>' +
            '       </thead>' +
            '       <tbody>' + newTableRows + '</tbody>' +
            '       </table>' +
            '   </td>' +
            '</tr>'

        $('#clusters-fast-table-tbody').append(newRow)
        $('#clusters-table-fast').show()
        $('#placeForCountClusters').html(count)
        copyFullUrls()
    })

    // $(document).ready(function () {
    //     $.each($('.render-table-fast'), function (key, value) {
    //         $('#' + $(this).attr('id')).dataTable({
    //             'order': [[0, "asc"]],
    //             'bPaginate': false,
    //             'orderCellsTop': true,
    //             'sDom': '<"top"i>rt<"bottom"lp><"clear">'
    //         })
    //     })
    // });
}
