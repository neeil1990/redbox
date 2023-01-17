function renderResultTable(data) {
    let iterator = 0
    let copyGroupBool = true
    let copyRelevanceBool = false;

    $.each(data, function (key, result) {
        let clusterIterator = 0
        let newTableRows = ''
        let newRow = ''
        let clusterId = (Math.random() + 1).toString(36).substring(7)
        let allRelevanceUrls = []
        let groupHeader = ''
        let groupName = ''
        let relevanceHeader = ''
        let saveUrlButton = ''

        $.each(result, function (phrase, information) {
            if (phrase !== 'finallyResult') {
                iterator++
                clusterIterator++
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


                if ('groupName' in result['finallyResult']) {
                    groupHeader = '<th style="border-top-width: 2px;min-width: 250px">Группа</th>'
                    groupName = '<td class="border-0 group-' + clusterId + '">' + result['finallyResult']['groupName'] + '</td>'
                } else {
                    copyGroupBool = false
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

                let relevance = ''
                if ('link' in information) {
                    relevance = '<td class="border-0 relevance-' + clusterId + '"> <a href="' + information['link'] + '" target="_blank">' + information['link'] + ' </a></td>'
                    relevanceHeader = '<th style="border-top-width: 2px;">Релевантные url </th>'
                    copyRelevanceBool = true
                } else if ('relevance' in information && information['relevance'] !== 0) {
                    $.each(information['relevance'], function (key, value) {
                        relevance += '<option value="' + value + '">' + value + '</option>'
                        allRelevanceUrls.push(value)
                    })

                    relevance = '<div class="d-flex">' +
                        '<select style="border-radius: 0 !important;" class="custom-select" id="' + phrase.replaceAll(' ', '-') + '">' + relevance + '</select>' +
                        '<button style="border-radius: 0 !important;" class="btn btn-secondary save-relevance-url" data-order="' + phrase + '"><i class="fa fa-save" style="color: white"></i></button>' +
                        '</div>'

                    relevance = '<td class="border-0 relevance-' + clusterId + '"> ' + relevance + '</td>'
                    relevanceHeader = '<th style="border-top-width: 2px;">Релевантные url </th>'
                    copyRelevanceBool = true
                }

                if (allRelevanceUrls.length > 0) {
                    saveUrlButton = '<button class="btn btn-secondary save-all-urls" ' +
                        'data-toggle="modal" data-target="#saveUrlsModal" ' +
                        'data-urls="' + [...new Set(allRelevanceUrls)] + '">' +
                        '   Сохранить url ' +
                        '</button>'
                }

                let title
                let style
                let string
                if ("basedNormal" in information &&
                    (information["basedNormal"] === false || information["basedNormal"] !== true)
                ) {
                    style = 'bg-cluster-warning'
                    title = `title='Ваша фраза "${phrase}" была изменена'`
                    string = information['based']['phrase']
                } else {
                    style = ''
                    title = ''
                    string = phrase
                }

                newTableRows +=
                    '<tr>' +
                    '   <td class="border-0">'
                    + iterator +
                    '</td> ' +
                    '   <td class="border-0"> ' + clusterIterator + '</td> ' +
                    '   <td class="border-0 ' + style + '" ' + title + '> ' +
                    '       <div class="d-flex justify-content-between"> ' +
                    '          <div class="cluster-id-' + clusterId + '">' + string + '</div> ' +
                    '          <div class="ml-1">' +
                    '             <i class="fa fa-copy copy-full-urls" data-action="' + string + '"></i>' +
                    '             <span class="__helper-link ui_tooltip_w">' +
                    '                 <i class="fa fa-paperclip" data-action="' + string + '"></i>' +
                    '                 <span class="ui_tooltip __bottom" style="min-width: 250px;">' +
                    '                     <span class="ui_tooltip_content" data-action="' + string + '"> </span>' +
                    '                 </span>' +
                    '             </span>' +
                    merge +
                    '          </div> ' +
                    '       </div>' +
                    '   </td> ' +
                    groupName +
                    relevance +
                    '   <td class="border-0 base-' + clusterId + '" data-target="' + baseForm + '">' + baseForm + '</td>' +
                    '   <td class="border-0 phrase-' + clusterId + '" data-target="' + phraseForm + '">' + phraseForm + '</td>' +
                    '   <td class="border-0 target-' + clusterId + '" data-target="' + targetForm + '">' + targetForm + '</td>' +
                    '</tr>'
            }
        })

        let groupButton

        if (copyGroupBool) {
            groupButton = '<p class="copy-group col mr-1" data-target="' + clusterId + '" data-toggle="collapse">' +
                '   <i class="fa fa-copy pr-1"></i>группу' +
                '</p>'
        } else {
            groupButton = ''
        }

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
            groupHeader +
            relevanceHeader +
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
            '            </p>'
            + groupButton +
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
            '            <div class="col-6"> ' +
            '                <a class="btn btn-secondary all-competitors" data-action="' + key + '" data-toggle="collapse"' +
            '                   href="#competitors' + key + '" role="button" aria-expanded="false"' +
            '                   aria-controls="competitors' + key + '">' +
            '                   Конкуренты' +
            '               </a>' +
            '            </div>' +
            '           <div class="col-6">' + saveUrlButton +
            '           </div>' +
            '       </div>' +
            '       <div class="collapse" id="competitors' + key + '"> </div>' +
            '   </td>' +
            '</tr>'

        $('#clusters-table-tbody').append(newRow)
    })

    copyBased()
    copyPhrases()
    copyTarget()
    copyCluster()

    if (copyGroupBool) {
        copyGroup()
    }

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
