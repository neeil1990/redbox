function renderNestingTable(nesting) {
    $('.mainPageCounter').html(nesting['mainPageCounter'])
    $('.mainPagePercent').html(nesting['mainPagePercent'] + '%')
    $('.nestedPageCounter').html(nesting['nestedPageCounter'])
    $('.nestedPagePercent').html(nesting['nestedPagePercent'] + '%')
    $('.nested').show()
}
