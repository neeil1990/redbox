function renderScanedSitesList(sites) {
    $('.pb-3.sites').show(300)
    let ul = $('#scaned-sites')
    $.each(sites, function (key, value) {
        ul.append(
            "<li class='render'>" + value + "</li>"
        )
    });
}
