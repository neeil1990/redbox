function getData(save = $('#save').val(), progressId = $('#progressId').val()) {
    return {
        _token: $('meta[name="csrf-token"]').attr('content'),
        save: save,
        progressId: progressId,
        region: $('#region').val(),
        count: $('#count').val(),
        phrases: $('#phrases').val(),
        clusteringLevel: $('#clusteringLevel').val(),
        engineVersion: $('#engineVersion').val(),
        searchBase: $('#searchBase').is(':checked'),
        searchPhrases: $('#searchPhrases').is(':checked'),
        searchTarget: $('#searchTarget').is(':checked'),
        domain: $('#domain-textarea').val(),
        comment: $('#comment-textarea').val(),
        sendMessage: $('#sendMessage').val(),
        brutForce: $('#brutForce').is(':checked'),
        searchRelevance: $('#searchRelevance').is(':checked'),
        searchEngine: $('#searchEngine').val(),

    };
}

function setProgressBarStyles(percent) {
    percent = percent > 100 ? 100 : percent;

    $('.progress-bar').css({
        width: percent + '%'
    })
    $('.progress-bar').html(percent + '%');
}

$('#save').on('change', function () {
    renderExtraBlock()
})

function renderExtraBlock() {
    if ($('#save').val() === '1') {
        $('#extra-block').show()
    } else {
        $('#extra-block').hide()
    }
}
