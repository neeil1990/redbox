function renderClouds(competitors, mainPage, tfCompClouds = null) {
    $('.clouds').show()

    let a = arrayToObj(competitors.links)
    let d = arrayToObj(competitors.text)
    let c = arrayToObj(competitors.textAndLinks)
    let g = arrayToObj(competitors.totalTf)
    let k = arrayToObj(competitors.textTf)
    let o = arrayToObj(competitors.linkTf)

    let b = arrayToObj(mainPage.links)
    let f = arrayToObj(mainPage.text)
    let e = arrayToObj(mainPage.textWithLinks)
    let h = arrayToObj(mainPage.totalTf)
    let l = arrayToObj(mainPage.textTf)
    let m = arrayToObj(mainPage.linkTf)

    $("#competitorsLinksCloud").jQCloud(a)
    $("#competitorsTextCloud").jQCloud(d);
    $("#competitorsTextAndLinksCloud").jQCloud(c);
    $("#competitorsTfCloud").jQCloud(g);
    $("#competitorsTextTfCloud").jQCloud(k);
    $("#competitorsLinksTfCloud").jQCloud(o);

    $("#mainPageLinksCloud").jQCloud(b);
    $("#mainPageTextWithLinksCloud").jQCloud(e);
    $("#mainPageTextCloud").jQCloud(f);
    $("#mainPageTfCloud").jQCloud(h);
    $("#mainPageTextTfCloud").jQCloud(l);
    $("#mainPageLinksTfCloud").jQCloud(m);

    if (tfCompClouds !== null) {
        $('#competitorsTfClouds').show()
        var iterator = 1
        $.each(tfCompClouds, function (key, value) {
            let item = arrayToObj(value)
            $('#clouds').append(
                "<div style='width: 50%;'>" +
                "<span>" + key + "</span>" +
                "<div id='cloud" + iterator + "' style='height: 400px; width: 100%; padding-top: 10px; padding-bottom: 10px'></div>" +
                "</div>"
            )
            $("#cloud" + iterator).jQCloud(item)
            iterator++
        });
    }
}

function arrayToObj(array) {
    let length = array.count
    let a = [], b = {};
    for (let i = 0; i < length; i++) {
        b = array[i]
        a.push(b);
    }
    return a;
}
