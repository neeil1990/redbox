/**
 * @param analysedSites
 */
function renderTopSites(analysedSites) {
    $.each(analysedSites, function (phrase, sites) {
        let tr = '<tr style="height: 80px" class="render">'
        tr += '<td>' + phrase + '</td>'
        $.each(sites, function (site, info) {
            let url = new URL(site)

            let btnGroup = getBtnGroup(url)

            let danger = info['danger'] ? 'danger' : ''

            let infoBlock

            if (info['danger']) {
                infoBlock = '<div class="text-danger mt-2">Сайт защищен от сбора информации, советуем проанализировать вручную</div>'
                infoBlock = getStub(url['host'], btnGroup, infoBlock, true)
            } else {
                infoBlock = ''
                $.each(info['meta'], function (key, values) {
                    if (values.length > 0) {
                        infoBlock +=
                            '<div class="mt-2">' +
                            '   <span class="text-info">' + key + '</span>' +
                            '   <br>' +
                            '   <span>' +
                            '      ' + values.join('<br>') +
                            '   </span>' +
                            '</div>'
                    }

                })
                infoBlock = getStub(url['host'], btnGroup, infoBlock)
            }


            tr += '<td class="' + danger + '">' + infoBlock + '</td>'

        })

        tr += '</tr>'

        $('#top-sites-body').append(tr)
    })

    $('.top-sites.mt-5').show()
}

function renderTopSitesV2(analysedSites) {
    let domains = [];
    let links = [];
    $.each(analysedSites, function (phrase, sites) {
        let newTable = '' +
            '<div class="card render mt-3" style="min-width: 350px;">' +
            '   <div class="card-header"><h3>' + phrase + '</h3></div>' +
            '   <div class="card-body p-0">' +
            '      <table class="table">' +
            '          <thead>' +
            '          <tr class="fixed-color">' +
            '              <th style="width: 10px">#</th>' +
            '              <th>Домен</th>' +
            '          </tr>' +
            '          </thead>' +
            '          <tbody>'

        let iterator = 1
        $.each(sites, function (link, object) {
            let url = new URL(link)
            let btnGroup = getBtnGroup(url, true)
            newTable +=
                '<tr style="height: 100px !important; cursor: pointer" ' +
                'data-order="' + url['host'] + '" ' +
                'data-full-url="' + link + '" ' +
                'data-main-page="' + object['mainPage'] + '">' +
                '    <td>' + iterator + '</td>' +
                '    <td class="word-wrap">' + (link).substr(0, 70) + btnGroup + '</td>' +
                '</tr>'
            iterator++
            domains.push(url['host'])
            links.push(link)
        })
        newTable += '</tbody></table></div></div>'

        $('#sites-tables').append(newTable)

        let uniqueDomains = [...new Set(domains)];

        $.each(uniqueDomains, function (key, value) {
            setRandomColor($('[data-order="' + value + '"]'))
        })

        colorButtonsActions(uniqueDomains, links)
    })

    $('#sites-block').show()
    showEquivalentElements()
}

/**
 *
 * @param host
 * @param btnGroup
 * @param html
 * @param showBlock
 * @returns {string}
 */
function getStub(host, btnGroup, html, showBlock = false) {

    if (showBlock) {
        return '<div class="card direct-chat direct-chat-primary" style="background: transparent !important; box-shadow: none; border: none">' +
            '        <div class="card-header ui-sortable-handle" style="padding: 0 !important; border: 0">' +
            '            <div class="d-flex justify-content-between">' +
            '<div>' + host + btnGroup + '</div>' +
            '                <button type="button" class="btn btn-tool" data-card-widget="collapse">' +
            '                    <i class="fas fa-minus"></i>' +
            '                </button>' +
            '            </div>' +
            '        </div>' +
            '        <div class="card-body">' +
            '            ' + html +
            '        </div>' +
            '    </div>';
    }

    return '<div class="card direct-chat direct-chat-primary collapsed-card" style="background: transparent !important; box-shadow: none; border: none">' +
        '        <div class="card-header ui-sortable-handle" style="padding: 0 !important; border: 0">' +
        '            <div class="d-flex justify-content-between">' +
        '<div>' + host + btnGroup + '</div>' +
        '                <button type="button" class="btn btn-tool" data-card-widget="collapse">' +
        '                    <i class="fas fa-plus"></i>' +
        '                </button>' +
        '            </div>' +
        '        </div>' +
        '        <div class="card-body" style="display: none;">' +
        html +
        '        </div>' +
        '    </div>';
}

/**
 *
 * @param url
 * @param colorElems = false
 * @returns {string}
 */
function getBtnGroup(url, colorElems = false) {
    if (colorElems) {
        return '<div class="btn-group">' +
            '   <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-tool dropdown-toggle" style="color: black;">' +
            '   <i class="fas fa-external-link-alt"></i>' +
            '   </button>' +
            '       <div role="menu" class="dropdown-menu dropdown-menu-left">' +

            '       <a target="_blank" class="dropdown-item" href="' + url['href'] + '" style="text-shadow: none">' +
            '       <i class="fas fa-external-link-alt"></i> Перейти на посадочную страницу</a>' +

            '       <a target="_blank" class="dropdown-item" href="' + url['origin'] + '" style="text-shadow: none">' +
            '       <i class="fas fa-external-link-alt"></i> Перейти на сайт</a>' +

            '       <a style="text-shadow: none" target="_blank" class="dropdown-item" href="/redirect-to-text-analyzer/' + url['origin'].replace(/\\|\//g, 'abc') + '">' +
            '       <i class="fas fa-external-link-alt"></i> Проанализировать текст</a>' +

            '   </div>' +
            '</div>'
    } else {
        return '<div class="btn-group">' +
            '   <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-tool dropdown-toggle">' +
            '   <i class="fas fa-external-link-alt"></i>' +
            '   </button>' +
            '       <div role="menu" class="dropdown-menu dropdown-menu-left">' +

            '       <a target="_blank" class="dropdown-item" href="' + url['href'] + '">' +
            '       <i class="fas fa-external-link-alt"></i> Перейти на посадочную страницу</a>' +

            '       <a target="_blank" class="dropdown-item" href="' + url['origin'] + '">' +
            '       <i class="fas fa-external-link-alt"></i> Перейти на сайт</a>' +

            '       <a target="_blank" class="dropdown-item" href="/redirect-to-text-analyzer/' + url['origin'].replace(/\\|\//g, 'abc') + '">' +
            '       <i class="fas fa-external-link-alt"></i> Проанализировать текст</a>' +

            '   </div>' +
            '</div>'
    }
}

/**
 *
 * @param uniqueDomains array
 * @param links array
 */
function colorButtonsActions(uniqueDomains, links) {
    $('#coloredMainPages').unbind().on('click', function () {
        coloredButtons($(this))

        setRandomColor($('[data-main-page="false"]'), true)
        setRandomColor($('[data-main-page="true"]'))
    });

    $('#coloredEloquentDomains').unbind().on('click', function () {
        coloredButtons($(this))
        setRandomColor($('#sites-tables tr'), true)

        $.each(uniqueDomains, function (key, value) {
            setRandomColor($('[data-order="' + value + '"]'))
        })
    })

    $('#coloredEloquentUrls').unbind().on('click', function () {
        coloredButtons($(this))
        setRandomColor($('#sites-tables tr'), true)

        $.each(links, function (key, value) {
            setRandomColor($('[data-full-url="' + value + '"]'))
        })
    })

    $('#coloredEloquentMyText').unbind().on('click', function () {
        coloredButtons($('#sites-block > div.site-block-buttons > button:nth-child(4)'))
        setRandomColor($('#sites-tables tr'), true)

        let myValues = $('#search-textarea').val()

        let myValuesAr = myValues.split("\n")

        let elems = []
        $.each($('#sites-tables tr'), function (key, value) {
            let target = $(this).attr('data-full-url');
            if (target) {
                let elem = $(this);
                $.each(myValuesAr, function (linkKey, link) {
                    if (target.indexOf(link) !== -1) {
                        elems.push(elem)
                    }
                })
            }

        });

        setColorElems([...new Set(elems)])
    })

    $('#coloredAgrigatorsButton').unbind().on('click', function () {
        coloredButtons($('#sites-block > div.site-block-buttons > button:nth-child(6)'))
        setRandomColor($('#sites-tables tr'), true)

        let agrigators = $('#search-agrigators').val()

        let agrigatorsAr = agrigators.split("\n")

        let elems = []
        $.each($('#sites-tables tr'), function (key, value) {
            let target = $(this).attr('data-order');
            if (target) {
                if (agrigatorsAr.indexOf(target) !== -1) {
                    elems.push($(this))
                }
            }

        });

        setColorElems(elems)
    })
}

/**
 *
 * @param elem
 * @param defaultColor = false
 */
function setRandomColor(elem, defaultColor = false) {
    if (defaultColor) {
        elem.css("background-color", "white");
        elem.css("color", "black");
        elem.css("text-shadow", "none");
        return;
    }

    if (elem.length > 1) {
        let colorR = Math.floor((Math.random() * 256));
        let colorG = Math.floor((Math.random() * 256));
        let colorB = Math.floor((Math.random() * 256));

        elem.css("background-color", "rgba(" + colorR + "," + colorG + "," + colorB + ", .5)");
        // elem.css("color", "white");
        // elem.css("text-shadow", "1px 1px 1px black");
    }

}

function setColorElems(elems) {
    let colorR = Math.floor((Math.random() * 256));
    let colorG = Math.floor((Math.random() * 256));
    let colorB = Math.floor((Math.random() * 256));

    $.each(elems, function (key, elem) {
        elem.css("background-color", "rgba(" + colorR + "," + colorG + "," + colorB + ", .5)");
        // elem.css("color", "white");
        // elem.css("text-shadow", "1px 1px 1px black");
    })
}

/**
 * @param elem
 */
function coloredButtons(elem) {
    $('.colored-button').attr('class', 'btn btn-default colored-button')
    elem.attr('class', 'btn btn-secondary colored-button')
}

function showEquivalentElements() {
    let target = $('#sites-tables > div > div.card-body.p-0 > table > tbody > tr')

    target.unbind('mouseenter').mouseenter(function () {
        let background = $(this).css('background-color')
        $('tr').filter(function () {
            return $(this).css('background-color') === background &&
                $(this).attr('data-main-page') !== false &&
                typeof $(this).attr('data-main-page') !== 'undefined';

        }).css("box-shadow", "inset 0 0 10px black");
    })

    target.unbind('mouseleave').mouseleave(function () {
        let background = $(this).css('background-color')
        $('tr').filter(function () {
            return $(this).css('background-color') === background &&
                $(this).attr('data-main-page') !== false &&
                typeof $(this).attr('data-main-page') !== 'undefined';

        }).css("box-shadow", "none");
    })
}
