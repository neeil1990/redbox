var countRows = 1

$('.text-info').click(function () {
    $('.express-form').hide(300)
    $('.simplified-form').show(300)
});
$('.express').click(function () {
    $('.express-form').show(300)
    $('.simplified-form').hide(300)
});
$('#addRow').click(function () {
    $('#removeRow').show(100)
    countRows++
    $('#countRows').val(countRows)
    $('#example2 tbody').append(
        '<tr id="tr-id-' + countRows + '">' +
        '<td><input type="text" name="site_donor_' + countRows + '" class="form form-control" required placeholder="Сайт донор"></td>' +
        '<td><input type="text" name="link_' + countRows + '" class="form form-control" required placeholder="Ссылка"></td>' +
        '<td><input type="text" name="anchor_' + countRows + '" class="form form-control" required placeholder="Анкор"></td>' +
        '<td><select class="custom-select rounded-0" name="nofollow_' + countRows + '" id=""><option value="1">Yes</option><option value="0">No</option></select></td>' +
        '<td><select class="custom-select rounded-0" name="noindex_' + countRows + '" id=""><option value="1">Yes</option><option value="0">No</option></select></td>' +
        '<td><select class="custom-select rounded-0" name="yandex_' + countRows + '" id=""><option value="1">Yes</option><option value="0">No</option></select></td>' +
        '<td><select class="custom-select rounded-0" name="google_' + countRows + '" id=""><option value="1">Yes</option><option value="0">No</option></select></td>' +
        '</tr>'
    );
});

$('#removeRow').click(function () {
    $('#tr-id-' + countRows).remove();
    countRows--;
    if (countRows == 1) {
        $('#removeRow').hide(100)
    }
});
