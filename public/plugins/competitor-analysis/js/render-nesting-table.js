function renderNestingTable(response) {
    $('.mainPageCounter').html(response.nesting['mainPageCounter'])
    $('.mainPagePercent').html(response.nesting['mainPagePercent'] + '%')
    $('.nestedPageCounter').html(response.nesting['nestedPageCounter'])
    $('.nestedPagePercent').html(response.nesting['nestedPagePercent'] + '%')
    $('.nested').show()
}
