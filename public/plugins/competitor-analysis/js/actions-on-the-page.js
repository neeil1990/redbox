$('.domain').click(function () {
    if ($(this).parent().parent().children('div').eq(1).is(':visible')) {
        $(this).children('i').eq(0).css({
            "transform": "rotate(0deg)"
        })
        $(this).parent().parent().children('div').eq(1).hide()
    } else {
        $(this).children('i').eq(0).css({
            "transform": "rotate(90deg)"
        })
        $(this).parent().parent().children('div').eq(1).show()
    }
})

$('.close-icon').click(function () {
    let td = $(this).parent().parent().parent()
    td.children('span').eq(0).show()
})
