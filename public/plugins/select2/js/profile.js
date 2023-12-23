$('#saveInBufferButton').click(function () {
    document.getElementById('special-token').select()
    document.execCommand('copy')
    document.getElementById('special-token').blur()
    $('.success-message').show(300)
    setTimeout(() => {
        $('.success-message').hide(300)
    }, 5000)
});
