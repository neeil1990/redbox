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
        mode: $('#start-analyse').attr('data-target'),
        brutForceCount: $('#brutForceCount').val(),
        reductionRatio: $('#reductionRatio').val(),
        defaultBrutForce: $('#defaultBrutForce').is(':checked'),
        ignoredWords: $('#ignoredWords').val(),
        ignoredDomains: $('#ignoredDomains').val(),
        gainFactor: $('#gainFactor').val(),
    };
}

function setProgressBarStyles(count) {
    $('#progress-bar-state').html('Просканировано: ' + count + ' из ');
}

$('#save').on('change', function () {
    if ($('#save').val() === '1') {
        $('#extra-block').show()
    } else {
        $('#extra-block').hide()
    }
})

function downloadSites(id, target, type) {
    if (type === 'download' && $("span[data-action='" + target + "']").html() !== ' ') {
        return;
    }

    $.ajax({
        type: "POST",
        url: "/download-cluster-sites",
        dataType: 'json',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            phrase: target,
            projectId: id,
        },
        success: function (response) {
            if (type === 'download') {
                let sitesBlock = ''
                if ('mark' in response && response['mark'] !== 0) {
                    $.each(response['mark'], function (site, boolean) {
                        if (boolean) {
                            sitesBlock +=
                                '<div class="text-muted">' +
                                '   <a href="' + site + '" target="_blank">' + new URL(site)['host'] + '</a> (игнорируемый)' +
                                '</div>'
                        } else {
                            sitesBlock +=
                                '<div>' +
                                '   <a href="' + site + '" target="_blank">' + new URL(site)['host'] + '</a>' +
                                '</div>'
                        }
                    })
                } else {
                    $.each(response['sites'], function (key, site) {
                        sitesBlock +=
                            '<div>' +
                            '   <a href="' + site + '" target="_blank">' + new URL(site)['host'] + '</a>' +
                            '</div>'
                    })
                }

                $("span[data-action='" + target + "']").html('')
                $("span[data-action='" + target + "']").append(sitesBlock)
            } else {
                if ('mark' in response && response['mark'] !== 0) {
                    let mark = [];
                    $.each(response['mark'], function (site, boolean) {
                        mark.push(site)
                    })

                    $('#hiddenForCopy').val(mark.join("\r"))
                } else {
                    $('#hiddenForCopy').val(response['sites'].join("\r"))
                }
                copyInBuffer()
            }
        },
        error: function (response) {
        }
    });
}

function downloadAllCompetitors(id, key) {
    if ($('#competitors' + key).html() === ' ') {
        $.ajax({
            type: "POST",
            url: "/download-cluster-competitors",
            dataType: 'json',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                key: key,
                projectId: id,
            },
            success: function (response) {
                let resultBlock = ''
                $.each(response['competitors'], function (site, count) {
                    resultBlock +=
                        '<div>' +
                        '   <a href="' + site + '">' + new URL(site)['host'] + '</a> :' + count +
                        '</div>'
                })
                $('#competitors' + key).html('')
                $('#competitors' + key).html(resultBlock)
            },
            error: function (response) {
            }
        });
    }
}

$(document).ready(function () {
    $('#searchRelevance').on('click', function () {
        isSearchRelevance()
    })
    isSearchRelevance()

    if ($('#brutForce').is(':checked')) {
        $('.brut-force').show(300)
    } else {
        $('.brut-force').hide(300)
    }
})

function isSearchRelevance() {
    if ($('#searchRelevance').is(':checked')) {
        $('#searchEngineBlock').show(300)
    } else {
        $('#searchEngineBlock').hide(300)
    }
}
