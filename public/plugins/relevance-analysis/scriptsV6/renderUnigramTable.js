function renderUnigramTable(unigramTable, count, resultId = 0) {
    console.log('resultId: ' + resultId)
    sessionStorage.setItem('childTableRows', JSON.stringify(unigramTable))
    $('.pb-3.unigram').show()
    let tBody = $('#unigramTBody')
    $.each(unigramTable, function (key, wordWorm) {
        renderMainTr(tBody, key, wordWorm)
    })

    $(document).ready(function () {
        var table = $('#unigram').DataTable({
            "order": [[2, "desc"]],
            "pageLength": count,
            "searching": true,
            dom: 'lBfrtip',
            buttons: [
                'copy', 'csv', 'excel'
            ]
        });
        $('#unigram').wrap("<div style='width: 100%; overflow-x: scroll; max-height:90vh;'></div>")
        //Добавляем только в ниграм
        $.each($(".dt-buttons"), function (key, value) {
            if (key === 1) {
                $(this).append("<a class='btn btn-secondary mr-1 ml-1' href='/show-child-words/" + resultId + "' target='_blank'>Child Words</a>");
                if (resultId !== 0) {
                    $(this).append("<a class='btn btn-secondary mr-1 ml-1' href='/show-missing-words/" + resultId + "' target='_blank'>Missing Words</a>");
                }
            }
        });

        function isUnigram(min, max, target, settings) {
            if (settings.nTable.id !== 'unigram') {
                return true;
            }

            return (isNaN(min) && isNaN(max)) ||
                (isNaN(min) && target <= max) ||
                (min <= target && isNaN(max)) ||
                (min <= target && target <= max);
        }

        $.fn.dataTable.ext.search.push(function (settings, data) {
            var maxTF = parseFloat($('#maxTF').val());
            var minTF = parseFloat($('#minTF').val());
            var TF = parseFloat(data[2]);
            return isUnigram(minTF, maxTF, TF, settings)
        });
        $('#minTF, #maxTF').keyup(function () {
            table.draw();
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
        });

        $.fn.dataTable.ext.search.push(function (settings, data) {
            var minIdf = parseFloat($('#minIdf').val());
            var maxIdf = parseFloat($('maxIdf').val());
            var IDF = parseFloat(data[3]);
            return isUnigram(minIdf, maxIdf, IDF, settings)
        });
        $('#minIdf, #maxIdf').keyup(function () {
            table.draw();
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
        });

        $.fn.dataTable.ext.search.push(function (settings, data) {
            var minInter = parseFloat($('#minInter').val());
            var maxInter = parseFloat($('#maxInter').val());
            var inter = parseFloat(data[4])
            return isUnigram(minInter, maxInter, inter, settings)
        });
        $('#minInter, #maxInter').keyup(function () {
            table.draw();
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
        });

        $.fn.dataTable.ext.search.push(function (settings, data) {
            var minReSpam = parseFloat($('#minReSpam').val());
            var maxReSpam = parseFloat($('#maxReSpam').val());
            var reSpam = parseFloat(data[5])
            return isUnigram(minReSpam, maxReSpam, reSpam, settings)
        });
        $('#minReSpam, #maxReSpam').keyup(function () {
            table.draw();
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
        });

        $.fn.dataTable.ext.search.push(function (settings, data) {
            var minAVG = parseFloat($('#minAVG').val());
            var maxAVG = parseFloat($('#maxAVG').val());
            var AVG = parseFloat(data[6])
            return isUnigram(minAVG, maxAVG, AVG, settings)
        });
        $('#minAVG, #maxAVG').keyup(function () {
            table.draw();
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
        });

        $.fn.dataTable.ext.search.push(function (settings, data) {
            var minAVGText = parseFloat($('#minAVGText').val());
            var maxAVGText = parseFloat($('#maxAVGText').val());
            var count = parseFloat(data[7])
            return isUnigram(minAVGText, maxAVGText, count, settings)
        });
        $('#minAVGText, #maxAVGText').keyup(function () {
            table.draw();
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
        });

        $.fn.dataTable.ext.search.push(function (settings, data) {
            var minInYourPage = parseFloat($('#minInYourPage').val());
            var maxInYourPage = parseFloat($('#maxInYourPage').val());
            var count = parseFloat(data[8])
            return isUnigram(minInYourPage, maxInYourPage, count, settings)
        });
        $('#minInYourPage, #maxInYourPage').keyup(function () {
            table.draw();
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
        });

        $.fn.dataTable.ext.search.push(function (settings, data) {
            var minTextIYP = parseFloat($('#minTextIYP').val());
            var maxTextIYP = parseFloat($('#maxTextIYP').val());
            var count = parseFloat(data[9])
            return isUnigram(minTextIYP, maxTextIYP, count, settings)
        });
        $('#minTextIYP, #maxTextIYP').keyup(function () {
            table.draw();
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
        });

        $.fn.dataTable.ext.search.push(function (settings, data) {
            var minAVGLink = parseFloat($('#minAVGLink').val());
            var maxAVGLink = parseFloat($('#maxAVGLink').val());
            var count = parseFloat(data[10])
            return isUnigram(minAVGLink, maxAVGLink, count, settings)
        });
        $('#minAVGLink, #maxAVGLink').keyup(function () {
            table.draw();
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
        });

        $.fn.dataTable.ext.search.push(function (settings, data) {
            var minLinkIYP = parseFloat($('#minLinkIYP').val());
            var maxLinkIYP = parseFloat($('#maxLinkIYP').val());
            var count = parseFloat(data[11])
            return isUnigram(minLinkIYP, maxLinkIYP, count, settings)
        });
        $('#minLinkIYP, #maxLinkIYP').keyup(function () {
            table.draw();
            $.each($('[generated-child=true]'), function () {
                $(this).attr('generated-child', false)
            })
        });
    });
}

function renderMainTr(tBody, key, wordWorm) {
    let links = '';
    $.each(wordWorm['total']['occurrences'], function (elem, value) {
        let url = new URL(elem);
        links += "<a href='" + elem + "' target='_blank'>" + url.host + "</a>(" + value + ")<br>"
    });
    let className = wordWorm['total']['danger'] ? "bg-warning-elem" : ""
    let tf = crop(wordWorm['total']['tf'])
    let idf = crop(wordWorm['total']['idf'])
    let numberOccurrences = crop(wordWorm['total']['numberOccurrences'])
    let reSpam = crop(wordWorm['total']['reSpam'])
    let avgInTotalCompetitors = wordWorm['total']['avgInTotalCompetitors']
    let totalRepeatMainPage = wordWorm['total']['totalRepeatMainPage']
    let avgInText = wordWorm['total']['avgInText']
    let repeatInTextMainPage = wordWorm['total']['repeatInTextMainPage']
    let avgInLink = wordWorm['total']['avgInLink']
    let repeatInLinkMainPage = wordWorm['total']['repeatInLinkMainPage']
    let repeatInTextMainPageWarning = repeatInTextMainPage == 0 ? "class='bg-warning-elem'" : ""
    let repeatInLinkMainPageWarning = repeatInLinkMainPage == 0 ? " class='bg-warning-elem'" : ""
    let totalInMainPage = repeatInLinkMainPage == 0 && repeatInTextMainPage == 0 ? " class='bg-warning-elem'" : ""
    let lockBlock =
        "    <span class='lock-block'>" +
        "        <i class='fa fa-solid fa-plus-square-o lock' data-target='" + key + "' onclick='addWordInIgnore($(this))'></i>" +
        "        <i class='fa fa-solid fa-minus-square-o unlock' data-target='" + key + "' style='display:none;' onclick='removeWordFromIgnored($(this))'></i>" +
        "    </span>";
    tBody.append(
        "<tr class='render'>" +
        "   <td class='" + className + "' onclick='showWordWorms($(this))' data-target='" + key + "'>" +
        "      <i class='fa fa-plus'></i>" +
        "   </td>" +
        "   <td>" + key + lockBlock + "</td>" +
        "   <td>" + tf + "</td>" +
        "   <td>" + idf + "</td>" +
        "   <td>" + numberOccurrences + "" +
        "   <span class='__helper-link ui_tooltip_w'>" +
        "       <i class='fa fa-paperclip'></i>" +
        "       <span class='ui_tooltip __right' style='min-width: 250px; max-width: 450px;'>" +
        "           <span class='ui_tooltip_content'>" + links + "</span>" +
        "       </span>" +
        "   </span>" +

        "</td>" +
        "<td>" + reSpam + "</td>" +

        "<td>" + avgInTotalCompetitors + "</td>" +
        "<td " + totalInMainPage + ">" + totalRepeatMainPage + "</td>" +

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
    let links = '';
    $.each(stats['occurrences'], function (elem, value) {
        let url = new URL(elem)
        links += "<a href='" + elem + "' target='_blank'>" + url.host + "</a>(" + value + ") <br>"
    });
    let tf = crop(stats['tf'])
    let idf = crop(stats['idf'])
    let numberOccurrences = crop(stats['numberOccurrences'])
    let reSpam = stats['reSpam']
    let avgInText = stats['avgInText']
    let avgInTotalCompetitors = stats['avgInTotalCompetitors']
    let totalRepeatMainPage = stats['totalRepeatMainPage']
    let repeatInTextMainPage = stats['repeatInTextMainPage']
    let avgInLink = stats['avgInLink']
    let repeatInLinkMainPage = stats['repeatInLinkMainPage']
    if (repeatInTextMainPage == 0) {
        var textWarn = "class='bg-warning-elem'"
        var bgWarn = "class='bg-warning-elem'"
    }
    if (repeatInLinkMainPage == 0) {
        var linkWarn = "class='bg-warning-elem'"
        var bgWarn = "class='bg-warning-elem'"
    }
    if (repeatInLinkMainPage == 0 && repeatInTextMainPage == 0) {
        var bgTotalWarn = "class='bg-warning-elem'"
    }
    let lockBlock =
        "    <span class='lock-block'>" +
        "        <i class='fa fa-solid fa-plus-square-o lock' data-target='" + word + "' onclick='addWordInIgnore($(this))'></i>" +
        "        <i class='fa fa-solid fa-minus-square-o unlock' data-target='" + word + "' style='display:none;' onclick='removeWordFromIgnored($(this))'></i>" +
        "    </span>";
    elem.after(
        "<tr style='background-color: #f4f6f9;' data-order='" + key + "' class='render child-table-row'>" +
        "<td " + bgWarn + " onclick='hideWordWorms($(this))' data-target='" + key + "'>" +
        "<i class='fa fa-minus'></i>" +
        "</td>" +
        "<td>" + word + lockBlock + "</td>" +
        "<td>" + tf + "</td>" +
        "<td>" + idf + "</td>" +
        "<td>" + numberOccurrences + "" +
        "<span class='__helper-link ui_tooltip_w'>" +
        "    <i class='fa fa-paperclip'></i>" +
        "    <span class='ui_tooltip __right' style='min-width: 250px; max-width: 450px;'>" +
        "        <span class='ui_tooltip_content'>" + links + "</span>" +
        "    </span>" +
        "</span>" +

        "</td>" +
        "<td>" + reSpam + "</td>" +
        "<td>" + avgInTotalCompetitors + "</td>" +
        "<td " + bgTotalWarn + ">" + totalRepeatMainPage + "</td>" +
        "<td>" + avgInText + "</td>" +
        "<td " + textWarn + ">" + repeatInTextMainPage + "</td>" +
        "<td>" + avgInLink + "</td>" +
        "<td " + linkWarn + ">" + repeatInLinkMainPage + "</td>" +
        "</tr>"
    )
}

function showWordWorms(elem) {
    if (elem.attr('generated-child') === 'true') {
        hideWordWorms(elem)
    } else {
        let obj = JSON.parse(sessionStorage.childTableRows)
        let target = elem.attr('data-target')
        let parent = elem.parent()
        elem.attr('generated-child', true)
        $.each(reverseObj(obj[target]), function (word, stats) {
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

function crop(number, decimal = false) {
    let position
    let string = number.toString()
    if (decimal) {
        return number.toFixed(1)
    } else {
        if (number[5] === '.') {
            position = 6
        } else {
            position = 7
        }
    }

    return string.substring(0, position)
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

function reverseObj(obj) {
    let newObj = {}
    let reverseObj = Object.keys(obj).reverse();
    reverseObj.forEach(function (i) {
        newObj[i] = obj[i];
    })
    return newObj;
}

function addWordInIgnore(elem) {
    if ($('#switchMyListWords').is(':checked') === false) {
        $('#switchMyListWords').prop('checked', true);
        $('.form-group.required.list-words.mt-1').show(300);
    }
    let word = elem.attr('data-target')
    let textarea = $('.form-control.listWords')
    let toastr = $('.toast-top-right.success-message.lock-word');
    if (textarea.val().slice(-1) === "\n" || textarea.val().slice(-1) === '') {
        textarea.val(textarea.val() + word + "\n")
    } else {
        textarea.val(textarea.val() + "\n" + word + "\n")
    }
    toastr.show(300)
    $('#lock-word').html('Слово "' + word + '" добавлено в игнорируемые')
    setTimeout(() => {
        toastr.hide(300)
    }, 3000)
    elem.hide()
    elem.parent().children().eq(1).show()
}

function removeWordFromIgnored(elem) {
    let word = elem.attr('data-target')
    let textarea = $('.form-control.listWords')
    let text = textarea.val()
    let result = '';
    $.each(text.split("\n"), function (key, value) {
        if (value !== word) {
            result += value + "\n"
        }
    });
    textarea.val(result.trim())
    let toastr = $('.toast-top-right.success-message.lock-word');
    toastr.show(300)
    $('#lock-word').html('Слово "' + word + '" удалено из игнорируемых')
    setTimeout(() => {
        toastr.hide(300)
    }, 3000)
    elem.hide()
    elem.parent().children().eq(0).show()
}
