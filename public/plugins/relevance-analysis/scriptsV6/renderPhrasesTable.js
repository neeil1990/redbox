function renderPhrasesTable(phrases, count, words) {
    $('.phrases').show()
    let newRows = ''
    $.each(phrases, function (key, item) {
        newRows += renderTr(key, item)
    })

    $('#phrasesTBody').html(newRows)

    $(document).ready(function () {
        if ($.fn.DataTable.fnIsDataTable($('#phrases'))) {
            $('#phrases').dataTable().fnDestroy();
        }

        var table = $('#phrases').DataTable({
            "order": [[1, "desc"]],
            "pageLength": count,
            "searching": true,
            dom: 'lBfrtip',
            buttons: [
                'copy', 'csv', 'excel'
            ],
            language: {
                paginate: {
                    "first": "«",
                    "last": "»",
                    "next": "»",
                    "previous": "«"
                },
            },
            "oLanguage": {
                "sSearch": words.search + ":",
                "sLengthMenu": words.show + " _MENU_ " + words.records,
                "sEmptyTable": words.noRecords,
                "sInfo": words.showing + " " + words.from + "  _START_ " + words.to + " _END_ " + words.of + " _TOTAL_ " + words.entries,
            }
        });

        setTimeout(() => {
            $('.buttons-html5').addClass('btn btn-secondary')

            function isPhrases(min, max, target, settings) {
                if (settings.nTable.id !== 'phrases') {
                    return true;
                }

                return (isNaN(min) && isNaN(max)) ||
                    (isNaN(min) && target <= max) ||
                    (min <= target && isNaN(max)) ||
                    (min <= target && target <= max);
            }

            $.fn.dataTable.ext.search.push(function (settings, data) {
                var minTF = parseFloat($('#phrasesMinTF').val());
                var maxTF = parseFloat($('#phrasesMaxTF').val());
                var TF = parseFloat(data[1]);
                return isPhrases(minTF, maxTF, TF, settings)
            });
            $('#phrasesMinTF, #phrasesMaxTF').keyup(function () {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                var minIdf = parseFloat($('#phrasesMinIdf').val());
                var maxIdf = parseFloat($('#phrasesMaxIdf').val());
                var IDF = parseFloat(data[2]);
                return isPhrases(minIdf, maxIdf, IDF, settings)
            });
            $('#phrasesMinIdf, #phrasesMaxIdf').keyup(function () {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                var minInter = parseFloat($('#phrasesMinInter').val());
                var maxInter = parseFloat($('#phrasesMaxInter').val());
                var inter = parseFloat(data[3])
                return isPhrases(minInter, maxInter, inter, settings)
            });
            $('#phrasesMinInter, #phrasesMaxInter').keyup(function () {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                var minReSpam = parseFloat($('#phrasesMinReSpam').val());
                var maxReSpam = parseFloat($('#phrasesMaxReSpam').val());
                var reSpam = parseFloat(data[4])
                return isPhrases(minReSpam, maxReSpam, reSpam, settings)
            });
            $('#phrasesMinReSpam, #phrasesMaxReSpam').keyup(function () {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                var minAVG = parseFloat($('#phrasesMinAVG').val());
                var maxAVG = parseFloat($('#phrasesMaxAVG').val());
                var AVG = parseFloat(data[5])
                return isPhrases(minAVG, maxAVG, AVG, settings)
            });
            $('#phrasesMinAVG, #phrasesMaxAVG').keyup(function () {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                var minAVGText = parseFloat($('#phrasesMinAVGText').val());
                var maxAVGText = parseFloat($('#phrasesMaxAVGText').val());
                var count = parseFloat(data[6])
                return isPhrases(minAVGText, maxAVGText, count, settings)
            });
            $('#phrasesMinAVGText, #phrasesMaxAVGText').keyup(function () {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                var minInYourPage = parseFloat($('#phrasesMinInYourPage').val());
                var maxInYourPage = parseFloat($('#phrasesMaxInYourPage').val());
                var count = parseFloat(data[7])
                return isPhrases(minInYourPage, maxInYourPage, count, settings)
            });
            $('#phrasesMinInYourPage, #phrasesMaxInYourPage').keyup(function () {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                var minTextIYP = parseFloat($('#phrasesMinTextIYP').val());
                var maxTextIYP = parseFloat($('#phrasesMaxTextIYP').val());
                var count = parseFloat(data[8])
                return isPhrases(minTextIYP, maxTextIYP, count, settings)
            });
            $('#phrasesMinTextIYP, #phrasesMaxTextIYP').keyup(function () {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                var minAVGLink = parseFloat($('#phrasesMinAVGLink').val());
                var maxAVGLink = parseFloat($('#phrasesMaxAVGLink').val());
                var count = parseFloat(data[9])
                return isPhrases(minAVGLink, maxAVGLink, count, settings)
            });
            $('#phrasesMinAVGLink, #phrasesMaxAVGLink').keyup(function () {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                var minLinkIYP = parseFloat($('#phrasesMinLinkIYP').val());
                var maxLinkIYP = parseFloat($('#phrasesMaxLinkIYP').val());
                var count = parseFloat(data[10])
                return isPhrases(minLinkIYP, maxLinkIYP, count, settings)
            });
            $('#phrasesMinLinkIYP, #phrasesMaxLinkIYP').keyup(function () {
                table.draw();
            });
        }, 400)

    });
}

function renderTr(key, item) {
    let links = '';

    $.each(item['occurrences'], function (elem, value) {
        let url = new URL(elem)
        links += "<a href='" + elem + "' target='_blank'>" + url.host + "</a>(" + value + ")<br>"
    });

    let repeatInTextMainPage = item['repeatInTextMainPage']
    let repeatInLinkMainPage = item['repeatInLinkMainPage']
    let repeatInTextMainPageWarning = repeatInTextMainPage == 0 ? "class='bg-warning-elem'" : ""
    let repeatInLinkMainPageWarning = repeatInLinkMainPage == 0 ? " class='bg-warning-elem'" : ""
    let totalInMainPage = repeatInLinkMainPage == 0 && repeatInTextMainPage == 0 ? " class='bg-warning-elem'" : ""

    return "<tr class='render'>" +
    "<td>" + key + "</td>" +
    "<td>" + item['tf'] + "</td>" +
    "<td>" + item['idf'] + "</td>" +
    "<td>" + item['numberOccurrences'] + "" +
    "<span class='__helper-link ui_tooltip_w'>" +
    "    <i class='fa fa-paperclip'></i>" +
    "    <span class='ui_tooltip __right' style='min-width: 250px; max-width: 450px;'>" +
    "        <span class='ui_tooltip_content'>" + links + "</span>" +
    "    </span>" +
    "</span>" +

    "</td>" +
    "<td>" + item['reSpam'] + "</td>" +

    "<td>" + item['avgInTotalCompetitors'] + "</td>" +
    "<td " + totalInMainPage + ">" + item['totalRepeatMainPage'] + "</td>" +

    "<td>" + item['avgInText'] + "</td>" +
    "<td " + repeatInTextMainPageWarning + ">" + repeatInTextMainPage + "</td>" +

    "<td>" + item['avgInLink'] + "</td>" +
    "<td " + repeatInLinkMainPageWarning + ">" + repeatInLinkMainPage + "</td>" +
    "</tr>"
}
