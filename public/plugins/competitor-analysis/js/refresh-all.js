function refreshAll() {
    $('.btn.btn-secondary.pull-left').prop('disabled', true);
    $('.top-sites').hide()
    $('.nested').hide()
    $('.positions').hide()
    $('.tag-analysis').hide()
    $('#sites-block').hide()
    $('.urls.mt-5').hide()
    $('#render-bar').hide()
    $('#recommendations-block').hide()
    $('.render').remove()

    $('#dualbox-phrases-block').html('')
    if ($('.custom-select.rounded-0.count').val() === '20') {
        $('.extra-th').show()
    } else {
        $('.extra-th').hide()
    }

    $('#positions').dataTable().fnDestroy();
    $('#urls-table').dataTable().fnDestroy();

    setProgressBarStyles(0)
    $("#progress-bar").show(300)
}
