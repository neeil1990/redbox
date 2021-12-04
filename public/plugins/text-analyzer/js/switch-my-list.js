$('input#switchMyListWords').click(function () {
    if ($(this).is(':checked')) {
        $('.form-group.required.list-words.mt-1').show(300)
        $('.form-control.listWords').prop('required', true)
    } else {
        $('.form-group.required.list-words.mt-1').hide(300)
        $('.form-control.listWords').removeAttr('required')
    }
})
