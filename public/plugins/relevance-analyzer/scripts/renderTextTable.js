function renderTextTable(avg, mainPage) {
    $('.pb-3.text').show()

    $('#avgCountWords').html(avg.countWords)
    $('#mainPageCountWords').html(mainPage.countWords)

    $('#avgCountSpaces').html(avg.countSpaces)
    $('#mainPageCountSpaces').html(mainPage.countSpaces)

    $('#avgCountSymbols').html(avg.countSymbols)
    $('#mainPageCountSymbols').html(mainPage.countSymbols)

    $('#avgCountSymbolsWithoutSpaces').html(avg.countSymbolsWithoutSpaces)
    $('#mainPageCountSymbolsWithoutSpaces').html(mainPage.countSymbolsWithoutSpaces)
}
