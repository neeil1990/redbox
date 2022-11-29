function renderResultTable(data) {
    let iterator = 0

    $.each(data, function (key, result) {
        let clusterIterator = 0
        let newTableRows = ''
        let clusterSites = ''
        let newRow = ''
        let clusterId = (Math.random() + 1).toString(36).substring(7)
        let minWidth = '120px'

        $.each(result['finallyResult']['sites'], function (site, count) {
            clusterSites +=
                '<div>' +
                '   <a href="' + site + '">' + new URL(site)['host'] + '</a> :' + count +
                '</div>'
        })

        $.each(result, function (phrase, information) {
            if (phrase !== 'finallyResult') {
                iterator++
                clusterIterator++

                let sites = ''
                let fullUrls = information['sites'].join("\r")

                $.each(information['sites'], function (key, site) {
                    sites +=
                        '<div>' +
                        '   <a href="' + site + '" target="_blank">' + new URL(site)['host'] + '</a>' +
                        '</div>'
                })

                let phraseForm = 0
                if ('phrased' in information) {
                    if (information['phrased'] === 0) {
                        phraseForm = 0
                    } else {
                        phraseForm = information['phrased']['number']
                    }
                }

                let targetForm = 0
                if ('target' in information) {
                    if (information['target'] === 0) {
                        targetForm = 0
                    } else {
                        targetForm = information['target']['number']
                    }
                }

                let baseForm = 0
                if ('based' in information) {
                    if (information['based'] === 0) {
                        baseForm = 0
                    } else {
                        baseForm = information['based']['number']
                    }
                }
                let groupName = 'groupName' in result['finallyResult'] ? result['finallyResult']['groupName'] : ' '

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

                let relevance = ''
                if ('link' in information) {
                    minWidth = '450px'
                    relevance = '<a href="' + information['link'] + '" target="_blank">' + information['link'] + '</a>'
                } else if ('relevance' in information && information['relevance'] !== 0) {
                    minWidth = '450px'
                    $.each(information['relevance'], function (key, value) {
                        relevance += '<option value="' + value + '">' + value + '</option>'
                    })

                    relevance = '<div class="d-flex">' +
                        '<select style="border-radius: 0 !important;" class="custom-select" id="' + phrase.replaceAll(' ', '-') + '">' + relevance + '</select>' +
                        '<button style="border-radius: 0 !important;" class="btn btn-secondary save-relevance-url" data-order="' + phrase + '"><i class="fa fa-save" style="color: white"></i></button>' +
                        '</div>'
                }

                let title
                let style
                if (
                    "basedNormal" in information &&
                    (information["basedNormal"] === false || information["basedNormal"] !== true)
                ) {
                    style = 'bg-cluster-warning'
                    title = `title='Ваша фраза "${phrase}" была изменена'`
                } else {
                    style = ''
                    title = ''
                }

                newTableRows +=
                    '<tr>' +
                    '   <td class="border-0">'
                    + iterator +
                    '</td> ' +
                    '   <td class="border-0"> ' + clusterIterator + '</td> ' +
                    '   <td class="border-0 ' + style + '" ' + title + '> ' +
                    '       <div class="d-flex justify-content-between"> ' +
                    '          <div class="cluster-id-' + clusterId + '">' + phrase + '</div> ' +
                    '          <div class="ml-1">' +
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
                    '   <td class="border-0 group-' + clusterId + '">' + groupName + '</td>' +
                    '   <td class="border-0 relevance-' + clusterId + '">' + relevance + '</td>' +
                    '   <td class="border-0 base-' + clusterId + '" data-target="' + baseForm + '">' + baseForm + '</td>' +
                    '   <td class="border-0 phrase-' + clusterId + '" data-target="' + phraseForm + '">' + phraseForm + '</td>' +
                    '   <td class="border-0 target-' + clusterId + '" data-target="' + targetForm + '">' + targetForm + '</td>' +
                    '</tr>'
            }
        })

        newRow +=
            '<tr class="render">' +
            '   <td class="p-0">' +
            '       <table class="table table-hover text-nowrap render-table" id="render-table' + key + '" style="width: 100%">' +
            '       <thead>' +
            '           <tr>' +
            '               <th colspan="4" style="border-bottom: 0; border-top: 0;"></th>' +
            '               <th class="centered-text border-0" colspan="3">Частотность</th>' +
            '           </tr>' +
            '           <tr>' +
            '               <th style="border-top-width: 2px;min-width: 25px;" title="Порядковый номер">#</th>' +
            '               <th style="border-top-width: 2px;min-width: 30px;" title="Порядковый номер в кластере">##</th>' +
            '               <th style="border-top-width: 2px;min-width: 250px;">Ключевой запрос</th>' +
            '               <th style="border-top-width: 2px;min-width: 250px;">Группа</i></th>' +
            '               <th style="border-top-width: 2px;min-width: ' + minWidth + '">Релевантные url</i></th>' +
            '               <th style="border-top-width: 2px;min-width: 70px;">Базовая</th>' +
            '               <th style="border-top-width: 2px;min-width: 100px;">"Фразовая"</th>' +
            '               <th style="border-top-width: 2px;min-width: 90px;">"!Точная"</th>' +
            '           </tr>' +
            '       </thead>' +
            '       <tbody>' + newTableRows + '</tbody>' +
            '       </table>' +
            '   </td>' +
            '   <td>' +
            '       <div class="row" style="cursor: pointer">' +
            '            <p class="copy-cluster-phrases col mr-1" data-target="' + clusterId + '" data-toggle="collapse">' +
            '                <i class="fa fa-copy pr-1"></i>ключевой запрос' +
            '            </p>' +
            '            <p class="copy-group col mr-1" data-target="' + clusterId + '" data-toggle="collapse">' +
            '                <i class="fa fa-copy pr-1"></i>группу' +
            '            </p>' +
            '       </div>' +
            '       <div class="row" style="cursor: pointer">' +
            '              <p class="copy-based col" data-target="' + clusterId + '" data-toggle="collapse">' +
            '                   <i class="fa fa-copy pr-1"></i>базовую' +
            '              </p>' +
            '              <p class="copy-phrase col" data-target="' + clusterId + '" data-toggle="collapse">' +
            '                   <i class="fa fa-copy pr-1"></i>фразовую' +
            '              </p>' +
            '              <p class="copy-target col" data-target="' + clusterId + '" data-toggle="collapse">' +
            '                   <i class="fa fa-copy pr-1"></i>точную' +
            '              </p>' +
            '       </div>' +
            '        <div class="row" style="cursor: pointer">' +
            '            <p> ' +
            '              <a class="btn btn-secondary" data-toggle="collapse"' +
            '                href="#competitors' + key + '" role="button" aria-expanded="false"' +
            '                aria-controls="competitors' + key + '">' +
            '                Конкуренты' +
            '               </a>' +
            '            </p>' +
            '       </div>' +
            '       <div class="collapse" id="competitors' + key + '">' + clusterSites + '</div>' +
            '   </td>' +
            '</tr>'

        $('#clusters-table-tbody').append(newRow)
    })

    copyBased()
    copyPhrases()
    copyTarget()
    copyCluster()

    copyGroup()
    copyFullUrls()
    $(document).ready(function () {
        $.each($('.render-table'), function (key, value) {
            $('#' + $(this).attr('id')).dataTable({
                'order': [[0, "asc"]],
                'bPaginate': false,
                'orderCellsTop': true,
                'sDom': '<"top"i>rt<"bottom"lp><"clear">'
            })
        })
    });

}

function copyCluster() {
    $('.copy-cluster-phrases').unbind().on('click', function () {
        $('#hiddenForCopy').css('display', 'block')
        $('#hiddenForCopy').val('')
        let iterator = $(this).attr('data-target')
        let trs = $('.cluster-id-' + iterator)

        $.each(trs, function (key, value) {
            let phrase = ($(this).html()).trim();
            if ($('#hiddenForCopy').val() === '') {
                $('#hiddenForCopy').val(phrase)
            } else {
                $('#hiddenForCopy').val($('#hiddenForCopy').val() + "\n" + phrase)
            }
        })

        copyInBuffer()
        successCopiedMessage()
    })
}

function copyGroup() {
    $('.copy-group').unbind().on('click', function () {
        $('#hiddenForCopy').css('display', 'block')
        $('#hiddenForCopy').val('')
        let iterator = $(this).attr('data-target')
        let trs = $('.group-' + iterator)

        $.each(trs, function (key, value) {
            let phrase = ($(this).html()).trim();
            if ($('#hiddenForCopy').val() === '') {
                $('#hiddenForCopy').val(phrase)
            } else {
                $('#hiddenForCopy').val($('#hiddenForCopy').val() + "\n" + phrase)
            }
        })

        copyInBuffer()
    })
}

function copyBased() {
    $('.copy-based').unbind().on('click', function () {
        $('#hiddenForCopy').css('display', 'block')
        $('#hiddenForCopy').val('')
        let iterator = $(this).attr('data-target')
        let trs = $('.base-' + iterator)

        $.each(trs, function (key, value) {
            let phrase = ($(this).html()).trim();
            if ($('#hiddenForCopy').val() === '') {
                $('#hiddenForCopy').val(phrase)
            } else {
                $('#hiddenForCopy').val($('#hiddenForCopy').val() + "\n" + phrase)
            }
        })

        copyInBuffer()
    })
}

function copyPhrases() {
    $('.copy-phrase').unbind().on('click', function () {
        $('#hiddenForCopy').css('display', 'block')
        $('#hiddenForCopy').val('')
        let iterator = $(this).attr('data-target')
        let trs = $('.phrase-' + iterator)

        $.each(trs, function (key, value) {
            let phrase = ($(this).html()).trim();
            if ($('#hiddenForCopy').val() === '') {
                $('#hiddenForCopy').val(phrase)
            } else {
                $('#hiddenForCopy').val($('#hiddenForCopy').val() + "\n" + phrase)
            }
        })

        copyInBuffer()
    })
}

function copyTarget() {
    $('.copy-target').unbind().on('click', function () {
        $('#hiddenForCopy').css('display', 'block')
        $('#hiddenForCopy').val('')
        let iterator = $(this).attr('data-target')
        let trs = $('.target-' + iterator)

        $.each(trs, function (key, value) {
            let phrase = ($(this).html()).trim();
            if ($('#hiddenForCopy').val() === '') {
                $('#hiddenForCopy').val(phrase)
            } else {
                $('#hiddenForCopy').val($('#hiddenForCopy').val() + "\n" + phrase)
            }
        })

        copyInBuffer()
    })
}

function copyInBuffer() {
    successCopiedMessage()
    $('#hiddenForCopy').css('display', 'block')

    let text = document.getElementById("hiddenForCopy");
    text.select();
    document.execCommand("copy");
    $('#hiddenForCopy').css('display', 'none')
}

function copyFullUrls() {
    $('.copy-full-urls').unbind().on('click', function () {
        $('#hiddenForCopy').val($('#hidden-urls-block-' + $(this).attr('data-target')).html())
        copyInBuffer()
    })
}
