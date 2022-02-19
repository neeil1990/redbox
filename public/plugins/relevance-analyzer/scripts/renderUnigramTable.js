function renderUnigramTable(unigramTable) {
    $('.pb-3.unigram').show()
    let tBody = $('#unigramTBody')

    $.each(unigramTable, function (key, wordWorm) {
        let className = wordWorm['total']['danger'] ? "bg-warning" : ""
        let tf = substringNumber(wordWorm['total']['tf'])
        let idf = substringNumber(wordWorm['total']['idf'])
        let numberOccurrences = substringNumber(wordWorm['total']['numberOccurrences'])
        let reSpam = substringNumber(wordWorm['total']['reSpam'])
        let avgInText = substringNumber(wordWorm['total']['avgInText'])
        let repeatInTextMainPage = substringNumber(wordWorm['total']['avgInText'])
        let avgInLink = substringNumber(wordWorm['total']['avgInLink'])
        let repeatInLinkMainPage = substringNumber(wordWorm['total']['repeatInLinkMainPage'])
        tBody.append(
            "<tr class='render'>" +
            "<td class='" + className + "' onclick='showWordWorms(this)' data-target='" + key + "'>" +
            "<i class='fa fa-plus'></i>" +
            "</td>" +
            "<td>" + key + "</td>" +
            "<td>" + tf + "</td>" +
            "<td>" + idf + "</td>" +
            "<td>" + numberOccurrences + "</td>" +
            "<td>" + reSpam + "</td>" +
            "<td>" + avgInText + "</td>" +
            "<td>" + repeatInTextMainPage + "</td>" +
            "<td>" + avgInLink + "</td>" +
            "<td>" + repeatInLinkMainPage + "</td>" +
            "</tr>"
        )
        $.each(wordWorm, function (word, stats) {
            if (word !== 'total') {
                var classN = ''
                if (stats['repeatInTextMainPage'] === 0 || stats['repeatInLinkMainPage'] === 0) {
                    classN = 'bg-warning'
                }
                let tf = substringNumber(stats['tf'])
                let idf = substringNumber(stats['idf'])
                let numberOccurrences = substringNumber(stats['numberOccurrences'])
                let reSpam = substringNumber(stats['reSpam'])
                let avgInText = substringNumber(stats['avgInText'])
                let repeatInTextMainPage = substringNumber(stats['avgInText'])
                let avgInLink = substringNumber(stats['avgInLink'])
                let repeatInLinkMainPage = substringNumber(stats['repeatInLinkMainPage'])
                tBody.append(
                    "<tr style='display: none; background-color: #f4f6f9;' data-order='" + key + "' class='render'>" +
                    "<td class='" + classN + "' onclick='hideWordWorms(this)' data-target='" + key + "'>" +
                    "<i class='fa fa-minus'></i>" +
                    "</td>" +
                    "<td>" + word + "</td>" +
                    "<td>" + tf + "</td>" +
                    "<td>" + idf + "</td>" +
                    "<td>" + numberOccurrences + "</td>" +
                    "<td>" + reSpam + "</td>" +
                    "<td>" + avgInText + "</td>" +
                    "<td>" + repeatInTextMainPage + "</td>" +
                    "<td>" + avgInLink + "</td>" +
                    "<td>" + repeatInLinkMainPage + "</td>" +
                    "</tr>"
                )
            }
        })
    })

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

function substringNumber(string) {
    let number = string.toString()
    return number.substring(0, 6)
}
