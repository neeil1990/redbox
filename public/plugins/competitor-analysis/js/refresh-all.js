function refreshAll() {
    $('.btn.btn-secondary.pull-left').prop('disabled', true);
    setProgressBarStyles(0)
    $("#progress-bar").show(300)

    $('#stage').text(getXMLMessage())
    $('.top-sites').hide()
    $('.nested').hide()
    $('.positions').hide()
    $('.tag-analysis').hide()
    $('.render').remove()
    $('#positions_wrapper').remove()

    if ($('.custom-select.rounded-0.count').val() === '20') {
        $('.extra-th').show()
    } else {
        $('.extra-th').hide()
    }
}
