function renderUnigramTable(unigramTable) {
    sessionStorage.setItem('childTableRows', JSON.stringify(unigramTable))
    $('.pb-3.unigram').show()
    let tBody = $('#unigramTBody')
    $.each(unigramTable, function (key, wordWorm) {
        renderMainTr(tBody, key, wordWorm)
    })

    $(document).ready(function () {
        var table = $('#unigram').DataTable({
            "order": [[2, "desc"]],
            "pageLength": 50,
            "searching": true,
        });

        $('#minTF, #maxTF').keyup(function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    var maxTF = parseFloat($('#maxTF').val());
                    var minTF = parseFloat($('#minTF').val());
                    var TF = parseFloat(data[2]);
                    if ((isNaN(minTF) && isNaN(maxTF)) ||
                        (isNaN(minTF) && TF <= maxTF) ||
                        (minTF <= TF && isNaN(maxTF)) ||
                        (minTF <= TF && TF <= maxTF)) {
                        return true;
                    }
                    return false;
                }
            );
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
            table.draw();
        });
        $('#minIdf, #maxIdf').keyup(function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    var maxIdf = parseFloat($('#maxIdf').val());
                    var minIdf = parseFloat($('#minIdf').val());
                    var IDF = parseFloat(data[3]);
                    if (
                        (isNaN(minIdf) && isNaN(maxIdf)) ||
                        (isNaN(minIdf) && IDF <= maxIdf) ||
                        (minIdf <= IDF && isNaN(maxIdf)) ||
                        (minIdf <= IDF && IDF <= maxIdf)
                    ) {
                        return true;
                    }
                    return false;
                }
            );
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
            table.draw();
        });
        $('#minInter, #maxInter').keyup(function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    var maxInter = parseFloat($('#maxInter').val());
                    var minInter = parseFloat($('#minInter').val());
                    var inter = parseFloat(data[4])
                    if ((isNaN(minInter) && isNaN(maxInter)) ||
                        (isNaN(minInter) && inter <= maxInter) ||
                        (minInter <= inter && isNaN(maxInter)) ||
                        (minInter <= inter && inter <= maxInter)) {
                        return true;
                    }
                    return false;
                }
            );
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
            table.draw();
        });
        $('#minReSpam, #maxReSpam').keyup(function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    var maxReSpam = parseFloat($('#maxReSpam').val());
                    var minReSpam = parseFloat($('#minReSpam').val());
                    var reSpam = parseFloat(data[5])
                    if ((isNaN(minReSpam) && isNaN(maxReSpam)) ||
                        (isNaN(minReSpam) && reSpam <= maxReSpam) ||
                        (minReSpam <= reSpam && isNaN(maxReSpam)) ||
                        (minReSpam <= reSpam && reSpam <= maxReSpam)) {
                        return true;
                    }
                    return false;
                }
            );
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
            table.draw();
        });
        $('#minAVG, #maxAVG').keyup(function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    var maxAVG = parseFloat($('#maxAVG').val());
                    var minAVG = parseFloat($('#minAVG').val());
                    var AVG = parseFloat(data[6])
                    if ((isNaN(minAVG) && isNaN(maxAVG)) ||
                        (isNaN(minAVG) && AVG <= maxAVG) ||
                        (minAVG <= AVG && isNaN(maxAVG)) ||
                        (minAVG <= AVG && AVG <= maxAVG)) {
                        return true;
                    }
                    return false;
                }
            );
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
            table.draw();
        });
        $('#minAVGText, #maxAVGText').keyup(function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    var maxAVGText = parseFloat($('#maxAVGText').val());
                    var minAVGText = parseFloat($('#minAVGText').val());
                    var count = parseFloat(data[7])
                    if ((isNaN(minAVGText) && isNaN(maxAVGText)) ||
                        (isNaN(minAVGText) && count <= maxAVGText) ||
                        (minAVGText <= count && isNaN(maxAVGText)) ||
                        (minAVGText <= count && count <= maxAVGText)) {
                        return true;
                    }
                    return false;
                }
            );
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
            table.draw();
        });
        $('#minInYourPage, #maxInYourPage').keyup(function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    var maxInYourPage = parseFloat($('#maxInYourPage').val());
                    var minInYourPage = parseFloat($('#minInYourPage').val());
                    var count = parseFloat(data[8])
                    if ((isNaN(minInYourPage) && isNaN(maxInYourPage)) ||
                        (isNaN(minInYourPage) && count <= maxInYourPage) ||
                        (minInYourPage <= count && isNaN(maxInYourPage)) ||
                        (minInYourPage <= count && count <= maxInYourPage)) {
                        return true;
                    }
                    return false;
                }
            );
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
            table.draw();
        });
        $('#minTextIYP, #maxTextIYP').keyup(function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    var maxTextIYP = parseFloat($('#maxTextIYP').val());
                    var minTextIYP = parseFloat($('#minTextIYP').val());
                    var count = parseFloat(data[9])
                    if ((isNaN(minTextIYP) && isNaN(maxTextIYP)) ||
                        (isNaN(minTextIYP) && count <= maxTextIYP) ||
                        (minTextIYP <= count && isNaN(maxTextIYP)) ||
                        (minTextIYP <= count && count <= maxTextIYP)) {
                        return true;
                    }
                    return false;
                }
            );
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
            table.draw();
        });
        $('#minAVGLink, #maxAVGLink').keyup(function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    var maxAVGLink = parseFloat($('#maxAVGLink').val());
                    var minAVGLink = parseFloat($('#minAVGLink').val());
                    var count = parseFloat(data[10])
                    if ((isNaN(minAVGLink) && isNaN(maxAVGLink)) ||
                        (isNaN(minAVGLink) && count <= maxAVGLink) ||
                        (minAVGLink <= count && isNaN(maxAVGLink)) ||
                        (minAVGLink <= count && count <= maxAVGLink)) {
                        return true;
                    }
                    return false;
                }
            );
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
            table.draw();
        });
        $('#minLinkIYP, #maxLinkIYP').keyup(function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    var maxLinkIYP = parseFloat($('#maxLinkIYP').val());
                    var minLinkIYP = parseFloat($('#minLinkIYP').val());
                    var count = parseFloat(data[11])
                    if ((isNaN(minLinkIYP) && isNaN(maxLinkIYP)) ||
                        (isNaN(minLinkIYP) && count <= maxLinkIYP) ||
                        (minLinkIYP <= count && isNaN(maxLinkIYP)) ||
                        (minLinkIYP <= count && count <= maxLinkIYP)) {
                        return true;
                    }
                    return false;
                }
            );
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
            table.draw();
        });
    });
}


function renderMainTr(tBody, key, wordWorm) {
    let className = wordWorm['total']['danger'] ? "bg-warning-elem" : ""
    let tf = substringNumber(wordWorm['total']['tf'])
    let idf = substringNumber(wordWorm['total']['idf'])
    let numberOccurrences = substringNumber(wordWorm['total']['numberOccurrences'])
    let reSpam = substringNumber(wordWorm['total']['reSpam'])
    let avgInTotalCompetitors = substringNumber(wordWorm['total']['avgInTotalCompetitors'])
    let totalRepeatMainPage = substringNumber(wordWorm['total']['totalRepeatMainPage'])
    let avgInText = substringNumber(wordWorm['total']['avgInText'])
    let repeatInTextMainPage = substringNumber(wordWorm['total']['repeatInTextMainPage'])
    let repeatInTextMainPageWarning = repeatInTextMainPage === '0' ? "class='bg-warning-elem'" : ""
    let avgInLink = substringNumber(wordWorm['total']['avgInLink'])
    let repeatInLinkMainPage = substringNumber(wordWorm['total']['repeatInLinkMainPage'])
    let repeatInLinkMainPageWarning = repeatInLinkMainPage === '0' ? " class='bg-warning-elem'" : ""
    tBody.append(
        "<tr class='render'>" +
        "<td class='" + className + "' onclick='showWordWorms($(this))' data-target='" + key + "'>" +
        "<i class='fa fa-plus'></i>" +
        "</td>" +
        "<td>" + key + "</td>" +
        "<td>" + tf + "</td>" +
        "<td>" + idf + "</td>" +
        "<td>" + numberOccurrences + "</td>" +
        "<td>" + reSpam + "</td>" +

        "<td>" + avgInTotalCompetitors + "</td>" +
        "<td>" + totalRepeatMainPage + "</td>" +

        "<td>" + avgInText + "</td>" +
        "<td " + repeatInTextMainPageWarning + ">" + repeatInTextMainPage + "</td>" +

        "<td>" + avgInLink + "</td>" +
        "<td " + repeatInLinkMainPageWarning + ">" + repeatInLinkMainPage + "</td>" +
        "</tr>"
    )
}

function renderChildTr(elem, key, word, stats) {
    if (word === 'total') {
        return;
    }
    let bgWarn = ''
    let textWarn = ''
    let linkWarn = ''
    let tf = substringNumber(stats['tf'])
    let idf = substringNumber(stats['idf'])
    let numberOccurrences = substringNumber(stats['numberOccurrences'])
    let reSpam = substringNumber(stats['reSpam'])
    let avgInText = substringNumber(stats['avgInText'])
    let avgInTotalCompetitors = substringNumber(stats['avgInTotalCompetitors'])
    let totalRepeatMainPage = substringNumber(stats['totalRepeatMainPage'])
    let repeatInTextMainPage = substringNumber(stats['repeatInTextMainPage'])
    let avgInLink = substringNumber(stats['avgInLink'])
    let repeatInLinkMainPage = substringNumber(stats['repeatInLinkMainPage'])
    if (repeatInTextMainPage === '0') {
        textWarn = "class='bg-warning-elem'"
        bgWarn = "class='bg-warning-elem'"
    }
    if (repeatInLinkMainPage === '0') {
        linkWarn = "class='bg-warning-elem'"
        bgWarn = "class='bg-warning-elem'"
    }
    elem.after(
        "<tr style='background-color: #f4f6f9;' data-order='" + key + "' class='render child-table-row'>" +
        "<td " + bgWarn + " onclick='hideWordWorms($(this))' data-target='" + key + "'>" +
        "<i class='fa fa-minus'></i>" +
        "</td>" +
        "<td>" + word + "</td>" +
        "<td>" + tf + "</td>" +
        "<td>" + idf + "</td>" +
        "<td>" + numberOccurrences + "</td>" +
        "<td>" + reSpam + "</td>" +
        "<td>" + avgInTotalCompetitors + "</td>" +
        "<td>" + avgInText + "</td>" +
        "<td " + textWarn + ">" + repeatInTextMainPage + "</td>" +
        "<td>" + totalRepeatMainPage + "</td>" +
        "<td>" + avgInLink + "</td>" +
        "<td " + linkWarn + ">" + repeatInLinkMainPage + "</td>" +
        "</tr>"
    )
}

function showWordWorms(elem) {
    if ($(elem).attr('generated-child') === 'true') {
        hideWordWorms(elem)
    } else {
        let obj = JSON.parse(sessionStorage.childTableRows)
        let target = $(elem).attr('data-target')
        let parent = elem.parent()
        $(elem).attr('generated-child', true)
        $.each(obj[target], function (word, stats) {
            renderChildTr(parent, target, word, stats)
        })
        elem.addClass('show-children')
    }
}

function hideWordWorms(elem) {
    let target = elem.attr('data-target')
    let objects = $('[data-target = ' + target + ']')
    $.each(objects, function () {
        if ($(this).attr('generated-child')) {
            $(this).attr('generated-child', false)
        }
    })
    $('tr[data-order=' + target + ']').remove()
}

$('th.sorting').click(() => {
    $('.child-table-row').remove();
    let objects = $('.show-children')
    $.each(objects, function () {
        $(this).attr('generated-child', false)
    })
})

function substringNumber(string) {
    var position
    let number = string.toString()
    if (string[5] === '.') {
        position = 4
    } else {
        position = 5
    }

    return number.substring(0, position)
}

$('#unigram > thead > tr > th').click(() => {
    $.each($('[generated-child=true]'), function () {
        $(this).attr('generated-child', false)
    })
});

$('#filters').click(() => {
    if ($('.pb-2.filters').is(':visible')) {
        $('.pb-2.filters').hide()
    } else {
        $('.pb-2.filters').show()
    }
})
