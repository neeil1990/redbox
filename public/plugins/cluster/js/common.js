function getData(save = $('#save').val()) {
    return {
        _token: $('meta[name="csrf-token"]').attr('content'),
        save: save,
        region: $('#region').val(),
        count: $('#count').val(),
        phrases: $('#phrases').val(),
        clusteringLevel: $('#clusteringLevel').val(),
        engineVersion: $('#engineVersion').val(),
        searchBased: $('#searchBased').is(':checked'),
        searchPhrases: $('#searchPhrases').is(':checked'),
        searchTarget: $('#searchTarget').is(':checked'),
        progressId: $('#progressId').val()
    };
}

function setProgressBarStyles(percent) {
    percent = percent > 100 ? 100 : percent;

    $('.progress-bar').css({
        width: percent + '%'
    })
    $('.progress-bar').html(percent + '%');
}
