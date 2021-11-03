$('.checkbox').click(function () {
    if ($('.checkbox').is(':checked')) {
        $('#phrase').prop('required', true);
        $('.keyword-phrase').show(300)
        $('#notification').hide(300)
    } else {
        $('#phrase').prop('required', false);
        $('.keyword-phrase').hide(300)
        $('#notification').show(300)
    }
})
