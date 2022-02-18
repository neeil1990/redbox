function renderUnigramTable(unigramTable) {
    $('.pb-3.unigram').show()

    let tBody = $('#unigramTBody')
    $.each(unigramTable, function (key, wordWorm) {
        let className = wordWorm['total']['danger'] ? "bg-warning" : ""
        let tf = wordWorm['total']['tf']
        let idf = wordWorm['total']['idf']
        let avgInText = wordWorm['total']['avgInText']
        let avgInLink = wordWorm['total']['avgInLink']
        tf = Math.floor(tf * 100) / 100
        idf = Math.floor(idf * 100) / 100
        avgInText = Math.floor(avgInText * 100) / 100
        avgInLink = Math.floor(avgInLink * 100) / 100
        tBody.append(
            "<tr class='render'>" +
            "<td class='" + className + "' onclick='showWordWorms(this)' data-target='" + key + "'>" +
            "<i class='fa fa-plus'></i>" +
            "</td>" +
            "<td>" + key + "</td>" +
            "<td>" + tf + "</td>" +
            "<td>" + idf + "</td>" +
            "<td>" + wordWorm['total']['numberOccurrences'] + "</td>" +
            "<td>" + wordWorm['total']['reSpam'] + "</td>" +
            "<td>" + avgInText + "</td>" +
            "<td>" + wordWorm['total']['repeatInTextMainPage'] + "</td>" +
            "<td>" + avgInLink + "</td>" +
            "<td>" + wordWorm['total']['repeatInLinkMainPage'] + "</td>" +
            "</tr>"
        )
        $.each(wordWorm, function (word, stats) {
            if (word !== 'total') {
                var classN = ''
                if (stats['repeatInTextMainPage'] === 0 || stats['repeatInLinkMainPage'] === 0) {
                    classN = 'bg-warning'
                }
                tBody.append(
                    "<tr style='display: none; background-color: #f4f6f9;' data-order='" + key + "' class='render'>" +
                    "<td class='" + classN + "' onclick='hideWordWorms(this)' data-target='" + key + "'>" +
                    "<i class='fa fa-minus'></i>" +
                    "</td>" +
                    "<td>" + word + "</td>" +
                    "<td>" + stats['tf'] + "</td>" +
                    "<td>" + stats['idf'] + "</td>" +
                    "<td>" + stats['numberOccurrences'] + "</td>" +
                    "<td>" + stats['reSpam'] + "</td>" +
                    "<td>" + stats['avgInText'] + "</td>" +
                    "<td>" + stats['repeatInTextMainPage'] + "</td>" +
                    "<td>" + stats['avgInLink'] + "</td>" +
                    "<td>" + stats['repeatInLinkMainPage'] + "</td>" +
                    "</tr>"
                )
            }
        })
    })
    $("#unigram").dataTable().fnDestroy();

    $('#unigram').DataTable({
        "order": [[6, "desc"]],
        "pageLength": 50,
        "ordering": false
    });
}

function showWordWorms(elem) {
    let target = $(elem).attr('data-target')
    if ($('tr[data-order=' + target + ']').is(':visible')) {
        $('tr[data-order=' + target + ']').hide()
    } else {
        $('tr[data-order=' + target + ']').show()
    }
}

function hideWordWorms(elem) {
    let target = $(elem).attr('data-target')
    if ($('tr[data-order=' + target + ']').is(':visible')) {
        $('tr[data-order=' + target + ']').hide()
    } else {
        $('tr[data-order=' + target + ']').show()
    }
}
