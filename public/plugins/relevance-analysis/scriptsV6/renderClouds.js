function renderClouds(competitors, mainPage, tfCompClouds, hide) {
    $('.clouds').show()
    $('#competitorsTfClouds').show()
    sessionStorage.setItem('competitors', JSON.stringify(competitors))
    sessionStorage.setItem('mainPage', JSON.stringify(mainPage))
    sessionStorage.setItem('tfCompClouds', JSON.stringify(tfCompClouds))
    sessionStorage.setItem('hideBool', hide)
}

$("#tf-idf-clouds").click(function () {
    console.log('lck')
    if (!$('.tf-idf-clouds').is(':visible')) {
        $('.tf-idf-clouds').show()
        if (!generatedTfIdf) {
            let competitors = JSON.parse(sessionStorage.getItem('competitors'))
            let mainPage = JSON.parse(sessionStorage.getItem('mainPage'))
            let g = arrayToObj(competitors.totalTf)
            let k = arrayToObj(competitors.textTf)
            let o = arrayToObj(competitors.linkTf)
            let h = arrayToObj(mainPage.totalTf)
            let l = arrayToObj(mainPage.textTf)
            let m = arrayToObj(mainPage.linkTf)
            $("#mainPageTfCloud").jQCloud(h);
            $("#mainPageTextTfCloud").jQCloud(l);
            $("#mainPageLinksTfCloud").jQCloud(m);
            $("#competitorsTfCloud").jQCloud(g);
            $("#competitorsTextTfCloud").jQCloud(k);
            $("#competitorsLinksTfCloud").jQCloud(o);
        }
    } else {
        $('.tf-idf-clouds').hide()
    }
    generatedTfIdf = true
});

$("#text-clouds").click(function () {
    if (!$('.text-clouds').is(':visible')) {
        $('.text-clouds').show()
        if (!generatedText) {
            let competitors = JSON.parse(sessionStorage.getItem('competitors'))
            let mainPage = JSON.parse(sessionStorage.getItem('mainPage'))
            let a = arrayToObj(competitors.links)
            let d = arrayToObj(competitors.text)
            let c = arrayToObj(competitors.textAndLinks)
            let b = arrayToObj(mainPage.links)
            let f = arrayToObj(mainPage.text)
            let e = arrayToObj(mainPage.textWithLinks)

            $("#competitorsLinksCloud").jQCloud(a)
            $("#competitorsTextCloud").jQCloud(d);
            $("#competitorsTextAndLinksCloud").jQCloud(c);


            $("#mainPageLinksCloud").jQCloud(b);
            $("#mainPageTextWithLinksCloud").jQCloud(e);
            $("#mainPageTextCloud").jQCloud(f);
        }
    } else {
        $('.text-clouds').hide()
    }
    generatedText = true
});

$('#coverage-clouds-button').click(function () {
    let tfCompClouds = JSON.parse(sessionStorage.getItem('tfCompClouds'))
    if (!$('#coverage-clouds').is(':visible')) {
        $('#coverage-clouds').show()
        $('#coverage-clouds').css({
            'display': 'flex',
            'flex-wrap': 'wrap',
            'margin-top': '15px'
        })
        if (!generatedCompetitorCoverage) {
            var iterator = 1
            $.each(tfCompClouds, function (key, value) {
                let btnGroup =
                    "<div class='btn-group'>" +
                    "        <button type='button' data-toggle='dropdown' aria-expanded='false' class='text-dark btn btn-tool dropdown-toggle'>" +
                    "            <i class='fas fa-external-link-alt'></i>" +
                    "        </button> " +
                    "       <div role='menu' class='dropdown-menu dropdown-menu-left'>" +
                    "            <a target='_blank' class='dropdown-item' href='" + key + "'>" +
                    "                <i class='fas fa-external-link-alt'></i> Перейти на посадочную страницу</a>" +
                    "            <span class='dropdown-item add-in-ignored-domains' style='cursor: pointer'" +
                    "                  data-target='" + key + "'>" +
                    "                <i class='fas fa-external-link-alt'></i>" +
                    "                Добавить в игнорируемые домены" +
                    "            </span>" +
                    "        </div>" +
                    "</div>";
                let item = arrayToObj(value)
                $('#coverage-clouds').append(
                    "<div style='width: 50%;' class='render'>" +
                    "<div><span class='competitor-cloud'>" + key + "</span>" + btnGroup + "</div>" +
                    "<div id='cloud" + iterator + "' style='height: 400px; width: 100%; padding-top: 10px; padding-bottom: 10px'></div>" +
                    "</div>"
                )
                $("#cloud" + iterator).jQCloud(item)
                iterator++
            });
            $('.add-in-ignored-domains').click(function () {
                let url = new URL($(this).attr('data-target'))
                let textarea = $('.form-control.ignoredDomains')
                let string = textarea.val()
                if (!string.includes(url.hostname)) {
                    let domain = (url.hostname).replace('www.', '')
                    if (textarea.val().slice(-1) === "\n") {
                        textarea.val(textarea.val() + domain + "\n")
                    } else {
                        textarea.val(textarea.val() + "\n" + domain + "\n")
                    }

                    let toastr = $('.toast-top-right.success-message.lock-word');
                    toastr.show(300)
                    $('#lock-word').html('Домен "' + domain + '" добавлен в игнорируемые')
                    setTimeout(() => {
                        toastr.hide(300)
                    }, 3000)
                }
            });
            // ------------------------------------------------------------
            var links = []
            $.each($('.ignored-site'), function (key, value) {
                let text = $(value).children('td').eq(1).children('div').eq(0).children('div').eq(0).children('a').eq(0).attr('href')
                links.push(text)
            });
            var compClouds = $('.competitor-cloud')

            $('#showOrHideIgnoredClouds').click(function () {
                $.each(compClouds, function (key, value) {
                    for (let i = 0; i < links.length; i++) {
                        if (links[i] == $(value).html()) {
                            let object = $(value).parent().parent()
                            if (object.is(':visible')) {
                                object.hide()
                            } else {
                                object.show()
                            }
                        }
                    }
                });
            });
            // --------------------------------------------------------------

            let hide = sessionStorage.getItem('hideBool')
            if (hide === 'yes') {
                $('#showOrHideIgnoredClouds').trigger('click');
            }
        }
    } else {
        $('#coverage-clouds').hide()
    }
    generatedCompetitorCoverage = true


});

function arrayToObj(array) {
    let length = array.count
    let a = [], b = {};
    for (let i = 0; i < length; i++) {
        b = array[i]
        a.push(b);
    }
    return a;
}

function showOrHideIgnoredDomains() {
    let hide = Boolean(sessionStorage.getItem('hideBool'))
    if (hide) {
        $('#showOrHideIgnoredClouds').trigger('click');
    }
}
