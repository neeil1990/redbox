function renderTextTable(avg, mainPage) {
    $('.pb-3.text').show()

    $('#avgCountWords').html(Math.round(avg.countWords))
    $('#mainPageCountWords').html(Math.round(mainPage.countWords))

    $('#avgCountSymbols').html(Math.round(avg.countSymbols))
    $('#mainPageCountSymbols').html(Math.round(mainPage.countSymbols))

    $('#avgCountSymbolsWithoutSpaces').html(Math.round(avg.countSymbolsWithoutSpaces))
    $('#mainPageCountSymbolsWithoutSpaces').html(Math.round(mainPage.countSymbolsWithoutSpaces))
}
