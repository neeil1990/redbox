function getData(save = $('#save').val(), progressId = $('#progressId').val()) {

    if ($('#start-analyse').attr('data-target') === 'classic') {
        return {
            _token: $('meta[name="csrf-token"]').attr('content'),
            save: $('#save_classic').val(),
            region: $('#region_classic').val(),
            phrases: $('#phrases_classic').val(),
            domain: $('#domain-textarea_classic').val(),
            sendMessage: $('#sendMessage_classic').val(),
            comment: $('#comment-textarea_classic').val(),
            clusteringLevel: $('#clusteringLevel_classic').val(),
            searchBase: $('#searchBase_classic').is(':checked'),
            searchTarget: $('#searchTarget_classic').is(':checked'),
            searchPhrases: $('#searchPhrases_classic').is(':checked'),
            searchRelevance: $('#searchRelevance_classic').is(':checked'),
            mode: 'classic',
            progressId: progressId,
        };

    } else {
        return {
            _token: $('meta[name="csrf-token"]').attr('content'),
            save: save,
            progressId: progressId,
            region: $('#region').val(),
            count: $('#count').val(),
            phrases: $('#phrases').val(),
            clusteringLevel: $('#clusteringLevel').val(),
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
            ignoredWords: $('#ignoredWords').val(),
            ignoredDomains: $('#ignoredDomains').val(),
            gainFactor: $('#gainFactor').val(),
        };
    }
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
                let element = $("span[data-action='" + target + "']")
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

                element.html('')
                element.append(sitesBlock)
            } else {
                if ('mark' in response && response['mark'] !== 0) {
                    let mark = [];
                    $.each(response['mark'], function (site, boolean) {
                        if (!boolean) {
                            mark.push(site)
                        }
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
    $('#searchRelevance_classic').on('click', function () {
        isSearchRelevanceClassic()
    })

    isSearchRelevance()
    isSearchRelevanceClassic()

    if ($('#brutForce').is(':checked')) {
        $('.brut-force').show(300)
    } else {
        $('.brut-force').hide(300)
    }

    if ($('#brutForce_classic').is(':checked')) {
        $('.brut-force_classic').show(300)
    } else {
        $('.brut-force_classic').hide(300)
    }
})

function isSearchRelevance() {
    if ($('#searchRelevance').is(':checked')) {
        $('#searchEngineBlock').show(300)
    } else {
        $('#searchEngineBlock').hide(300)
    }
}

function isSearchRelevanceClassic() {
    if ($('#searchRelevance_classic').is(':checked')) {
        $('#searchEngineBlock_classic').show(300)
    } else {
        $('#searchEngineBlock_classic').hide(300)
    }
}
