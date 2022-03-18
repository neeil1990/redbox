function renderClouds(clouds) {
    $('.clouds').show()

    let a = arrayToObj(clouds.competitorsLinksCloud)
    let b = arrayToObj(clouds.mainPageLinksCloud)
    let c = arrayToObj(clouds.competitorsTextAndLinksCloud)
    let d = arrayToObj(clouds.competitorsTextCloud)
    let e = arrayToObj(clouds.mainPageTextWithLinksCloud)
    let f = arrayToObj(clouds.mainPageTextCloud)
    let g = arrayToObj(clouds.competitorsTfCloud)
    let h = arrayToObj(clouds.mainPageTfCloud)

    $("#competitorsLinksCloud").jQCloud(a)
    $("#mainPageLinksCloud").jQCloud(b);
    $("#competitorsTextAndLinksCloud").jQCloud(c);
    $("#competitorsTextCloud").jQCloud(d);
    $("#mainPageTextWithLinksCloud").jQCloud(e);
    $("#mainPageTextCloud").jQCloud(f);
    $("#competitorsTfCloud").jQCloud(g);
    $("#mainPageTfCloud").jQCloud(h);
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
