function renderResultTable(data) {
    let iterator = 0
    let style
    let phrased
    let target
    let targetPhrase

    $.each(data, function (key, result) {
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
                iterator++
                clusterIterator++

                let changedBg = "basedNormal" in information;
                let sites = ''

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

                targetPhrase = changedBg ? information['basedNormal'] : phrase
                let title = changedBg ? `title='Ваша фраза "${phrase}" была изменена'` : ''

                newTableRows +=
                    '<tr>' +
                    '   <td class="border-0">' + iterator + '</td> ' +
                    '   <td class="border-0"> ' + clusterIterator + '</td> ' +
                    '   <td class="border-0 ' + style + '" ' + title + '> ' +
                    '       <div class="d-flex"> ' +
                    '          <div class="col-11">' + targetPhrase + '</div> ' +
                    '          <div class="col-1">' +
                    '             <span class="__helper-link ui_tooltip_w">' +
                    '                 <i class="fa fa-paperclip"></i>' +
                    '                 <span class="ui_tooltip __right" style="min-width: 250px;">' +
                    '                     <span class="ui_tooltip_content">' + sites + '</span>' +
                    '                 </span>' +
                    '             </span>' +
                    '          </div> ' +
                    '       </div>' +
                    '   </td> ' +
                    '   <td class="border-0">' + result['finallyResult']['groupName'] + '</td>' +
                    '   <td class="border-0">' + information['based']['number'] + '</td>' +
                    '   <td class="border-0">' + phrased + '</td>' +
                    '   <td class="border-0">' + target + '</td>' +
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
            '               <th>Порядковый номер</th>' +
            '               <th>Порядковый номер в кластере</th>' +
            '               <th>Ключевой запрос <i class="fa fa-copy copy-cluster-phrases pr-1"></i> </th>' +
            '               <th>Группа <i class="fa fa-copy copy-group pr-1"></i> </th>' +
            '               <th>Базовая <i class="fa fa-copy copy-based pr-1"></i> </th>' +
            '               <th>"Фразовая" <i class="fa fa-copy copy-phrases pr-1"></i> </th>' +
            '               <th>"!Точная" <i class="fa fa-copy copy-target pr-1"></i> </th>' +
            '           </tr>' +
            '       </thead>' +
            '       <tbody>' + newTableRows + '</tbody>' +
            '       </table>' +
            '   </td>' +
            '   <td>' +
            '      <p>' +
            '           <a class="btn btn-secondary" data-toggle="collapse"' +
            '            href="#competitors' + key + '" role="button" aria-expanded="false"' +
            '            aria-controls="competitors' + key + '">' +
            '            Конкуренты' +
            '           </a>' +
            '       </p>' +
            '       <div class="collapse" id="competitors' + key + '">' + clusterSites + '</div>' +
            '   </td>' +
            '</tr>'

        $('#clusters-table-tbody').append(newRow)
    })

    copyCluster()
    copyGroup()
    copyBased()
    copyPhrases()
    copyTarget()

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
        let trs = $(this).parent().parent().parent().parent().children('tbody').children('tr');

        $.each(trs, function (key, value) {
            let phrase = ($(this).children('td:nth-of-type(3)').children('div.d-flex').children('div.col-11').html()).trim();
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
        let trs = $(this).parent().parent().parent().parent().children('tbody').children('tr');

        $.each(trs, function (key, value) {
            let phrase = ($(this).children('td:nth-of-type(4)').html()).trim();
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

function copyBased() {
    $('.copy-based').unbind().on('click', function () {
        $('#hiddenForCopy').css('display', 'block')
        $('#hiddenForCopy').val('')
        let trs = $(this).parent().parent().parent().parent().children('tbody').children('tr');

        $.each(trs, function (key, value) {
            let phrase = ($(this).children('td:nth-of-type(5)').html()).trim();
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

function copyPhrases() {
    $('.copy-phrases').unbind().on('click', function () {
        $('#hiddenForCopy').css('display', 'block')
        $('#hiddenForCopy').val('')
        let trs = $(this).parent().parent().parent().parent().children('tbody').children('tr');

        $.each(trs, function (key, value) {
            let phrase = ($(this).children('td:nth-of-type(6)').html()).trim();
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

function copyTarget() {
    $('.copy-target').unbind().on('click', function () {
        $('#hiddenForCopy').css('display', 'block')
        $('#hiddenForCopy').val('')
        let trs = $(this).parent().parent().parent().parent().children('tbody').children('tr');

        $.each(trs, function (key, value) {
            let phrase = ($(this).children('td:nth-of-type(7)').html()).trim();
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

function copyInBuffer() {
    let text = document.getElementById("hiddenForCopy");
    text.select();
    document.execCommand("copy");
    $('#hiddenForCopy').css('display', 'none')
}

function successCopiedMessage() {
    $('.toast.toast-success').show(300)
    $('.toast-message.success-msg').html("Successfully copied")
    setTimeout(() => {
        $('.toast.toast-success').hide(300)
    }, 3000)
}
