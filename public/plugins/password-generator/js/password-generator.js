function saveState() {
    let checkboxState = document.getElementsByClassName('checkbox')
    let numberState = document.getElementsByClassName('number')
    localStorage.setItem('numberState1', numberState[0].value)
    localStorage.setItem('checkState1', checkboxState[0].checked)
    localStorage.setItem('checkState2', checkboxState[1].checked)
    localStorage.setItem('checkState3', checkboxState[2].checked)
    localStorage.setItem('checkState4', checkboxState[3].checked)
    localStorage.setItem('checkState5', checkboxState[4].checked)
}

window.onload = function () {
    document.getElementsByClassName('number')[0].value = localStorage.getItem('numberState1')
    document.getElementById('checkbox1').checked = localStorage.getItem('checkState1') === 'true';
    document.getElementById('checkbox2').checked = localStorage.getItem('checkState2') === 'true';
    document.getElementById('checkbox3').checked = localStorage.getItem('checkState3') === 'true';
    document.getElementById('checkbox4').checked = localStorage.getItem('checkState4') === 'true';
    document.getElementById('checkbox5').checked = localStorage.getItem('checkState5') === 'true';
}

$('.__helper-link.ui_tooltip_w.btn.btn-default').click(function () {
    let text = $(this).find(':first-child.hidden-password').text()
    let textarea = document.createElement('textarea');
    document.body.appendChild(textarea);
    textarea.value = text.trim();
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);

    $('.success-message').show(300)
    setTimeout(() => {
        $('.success-message').hide(300)
    }, 5000)
});
