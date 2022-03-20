function renderClouds(competitors, mainPage) {
    console.log(competitors)
    console.log(mainPage)
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
    let l =  arrayToObj(mainPage.textTf)
    let m =  arrayToObj(mainPage.linkTf)

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
