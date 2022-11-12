function renderResultTable(data) {
    let iterator = 0
    let style
    let phrased
    let target
    let based
    let targetPhrase

    $.each(data, function (key, result) {
        let clusterIterator = 0
        let newTableRows = ''
        let clusterSites = ''
        let newRow = ''
        let clusterId = (Math.random() + 1).toString(36).substring(7)

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

                let changedBg = "basedNormal" in information;
                let sites = ''
                let fullUrls = information['sites'].join("\r")

                $.each(information['sites'], function (key, site) {
                    sites +=
                        '<div>' +
                        '   <a href="' + site + '" target="_blank">' + new URL(site)['host'] + '</a>' +
                        '</div>'
                })

                if (changedBg) {
                    style = 'bg-cluster-warning'
                } else {
                    style = ''
                }

                phrased = "phrased" in information ? information['phrased']['number'] : '0'
                target = "target" in information ? information['target']['number'] : '0'
                based = information['based']['number']

                targetPhrase = changedBg ? information['basedNormal'] : phrase
                let title = changedBg ? `title='Ваша фраза "${phrase}" была изменена'` : ''

                newTableRows +=
                    '<tr>' +
                    '   <td class="border-0">' + iterator + '</td> ' +
                    '   <td class="border-0"> ' + clusterIterator + '</td> ' +
                    '   <td class="border-0 ' + style + '" ' + title + '> ' +
                    '       <div class="d-flex"> ' +
                    '          <div class="col-10  cluster-id-' + clusterId + '">' + targetPhrase + '</div> ' +
                    '          <div class="col-2">' +
                    '             <i class="fa fa-copy copy-full-urls" data-target="' + iterator + '" title="копировать полные ссылки сайтов"></i>' +
                    '             <div style="display: none" id="hidden-urls-block-' + iterator + '">' + fullUrls + '</div>' +
                    '             <span class="__helper-link ui_tooltip_w">' +
                    '                 <i class="fa fa-paperclip"></i>' +
                    '                 <span class="ui_tooltip __bottom" style="min-width: 250px;">' +
                    '                     <span class="ui_tooltip_content">' + sites + '</span>' +
                    '                 </span>' +
                    '             </span>' +
                    '          </div> ' +
                    '       </div>' +
                    '   </td> ' +
                    '   <td class="border-0 group-' + clusterId + '">' + result['finallyResult']['groupName'] + '</td>' +
                    '   <td class="border-0 base-' + clusterId + '" data-target="' + based + '">' + based + '</td>' +
                    '   <td class="border-0 phrase-' + clusterId + '" data-target="' + phrased + '">' + phrased + '</td>' +
                    '   <td class="border-0 target-' + clusterId + '" data-target="' + target + '">' + target + '</td>' +
                    '</tr>'
            }
        })

        newRow +=
            '<tr class="render">' +
            '   <td class="p-0">' +
            '       <table class="table table-hover text-nowrap render-table" id="render-table' + key + '" style="width: 100%">' +
            '       <thead>' +
            '           <tr>' +
            '               <th colspan="4"></th>' +
            '               <th class="centered-text" colspan="3">Частотность</th>' +
            '           </tr>' +
            '           <tr>' +
            '               <th style="min-width: 60px; max-width: 80px;" title="Порядковый номер">#</th>' +
            '               <th style="min-width: 60px; max-width: 80px;" title="Порядковый номер в кластере">##</th>' +
            '               <th>Ключевой запрос</th>' +
            '               <th>Группа</i></th>' +
            '               <th style="max-width: 93px; min-width: 93px;">Базовая</th>' +
            '               <th style="max-width: 93px; min-width: 93px;">"Фразовая"</th>' +
            '               <th style="max-width: 93px; min-width: 93px;">"!Точная"</th>' +
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

