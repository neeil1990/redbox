function renderPhrasesTable(phrases) {
    $('.phrases').show()
    let tBody = $('#phrasesTBody')
    $.each(phrases, function (key, item) {
        renderTr(tBody, key, item)
    })

    $(document).ready(function () {
        $('#phrases').DataTable({
            "order": [[1, "desc"]],
            "pageLength": 25,
            "searching": true,
        });
    });
    setTimeout(() => {
        $('#phrases').wrap("<div style='width: 100%; overflow-x: scroll; height:90vh;'></div>")
    }, 2000)
}

function renderTr(tBody, key, item) {
    let occurrences = item['occurrences']
    let links = '';
    $.each(occurrences, function (elem, value) {
        links += "<a href='" + value + "' target='_blank'>" + value + "</a><br>"
    });
    let tf = item['tf']
    let idf = item['idf']
    let numberOccurrences = item['numberOccurrences']
    let reSpam = item['reSpam']
    let avgInTotalCompetitors = item['avgInTotalCompetitors']
    let totalRepeatMainPage = item['totalRepeatMainPage']
    let avgInText = item['avgInText']
    let repeatInTextMainPage = item['repeatInTextMainPage']
    let avgInLink = item['avgInLink']
    let repeatInLinkMainPage = item['repeatInLinkMainPage']
    let repeatInTextMainPageWarning = repeatInTextMainPage == 0 ? "class='bg-warning-elem'" : ""
    let repeatInLinkMainPageWarning = repeatInLinkMainPage == 0 ? " class='bg-warning-elem'" : ""
    let totalInMainPage = repeatInLinkMainPage == 0 && repeatInTextMainPage == 0 ? " class='bg-warning-elem'" : ""
    tBody.append(
        "<tr class='render'>" +
        "<td>" + key + "</td>" +
        "<td>" + tf + "</td>" +
        "<td>" + idf + "</td>" +
        "<td>" + numberOccurrences + "" +
        "<span class='__helper-link ui_tooltip_w'>" +
        "    <i class='fa fa-paperclip'></i>" +
        "    <span class='ui_tooltip __right'>" +
        "        <span class='ui_tooltip_content'>" + links + "</span>" +
        "    </span>" +
        "</span>" +

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
