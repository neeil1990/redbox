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
            '<div class="card render mt-3" style="width: 300px; flex-shrink: 0">' +
            '   <div class="card-header separate-header border"><h3>' + phrase + '</h3></div>' +
            '   <div class="card-body p-0 d-flex flex-column">' +
            '       <div class="fixed-color d-flex p-2 border">' +
            '           <div class="font-weight-bold pr-2">#</div>' +
            '           <div class="font-weight-bold">Домен</div>' +
            '       </div>'

        let iterator = 1
        $.each(sites, function (link, object) {
            let url = new URL(link)
            let btnGroup = getBtnGroup(url)
            newTable +=
                '<div class="d-flex p-2 align-items-center justify-content-start border await-color" style="cursor: pointer; height: 75px" ' +
                'data-order="' + url['host'] + '" ' +
                'data-full-url="' + link + '" ' +
                'data-main-page="' + object['mainPage'] + '">' +
                '    <div class="pl-2 pr-2" style="width: 40px">' + iterator + '</div>' +
                '    <div class="fixed-lines word-wrap">' + link + '</div>' +
                '    <div>' + btnGroup + '</div>' +
                '</div>'
            domains.push(url['host'])
            links.push(link)

            iterator++
        })
        newTable += '</div></div>'

        $('#sites-tables').append(newTable)

        let uniqueDomains = [...new Set(domains)];

        let colors = getColorsArray()
        $.each(links, function (key, value) {
            setRandomColor($('[data-full-url="' + value + '"]'), colors.shift())
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
 * @returns {string}
 */
function getBtnGroup(url) {
    return '<div class="btn-group pl-1 p-0">' +
        '   <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-tool dropdown-toggle p-0" style="color: black;">' +
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
}

/**
 *
 * @param uniqueDomains array
 * @param links array
 */
function colorButtonsActions(uniqueDomains, links) {
    $('#coloredMainPages').unbind().on('click', function () {
        coloredButtons($(this))

        setRandomColor($('[data-main-page="false"]'), false, true)
        let colors = getColorsArray()
        setRandomColor($('[data-main-page="true"]'), colors.shift())
    });

    $('#coloredEloquentDomains').unbind().on('click', function () {
        coloredButtons($(this))
        setRandomColor($('.await-color'), false, true)

        let colors = getColorsArray()
        $.each(uniqueDomains, function (key, value) {
            setRandomColor($('[data-order="' + value + '"]'), colors.shift())
        })
    })

    $('#coloredEloquentUrls').unbind().on('click', function () {
        coloredButtons($(this))
        setRandomColor($('.await-color'), false, true)

        let colors = getColorsArray()
        $.each(links, function (key, value) {
            setRandomColor($('[data-full-url="' + value + '"]'), colors.shift())
        })
    })

    $('#coloredEloquentMyText').unbind().on('click', function () {
        coloredButtons($('#sites-block > div.site-block-buttons > button:nth-child(4)'))
        setRandomColor($('.await-color'), false, true)

        let myValues = $('#search-textarea').val()

        let myValuesAr = myValues.split("\n")

        let elems = []
        $.each($('.await-color'), function (key, value) {
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
        setRandomColor($('.await-color'), false, true)

        let agrigators = $('#search-agrigators').val()

        let agrigatorsAr = agrigators.split("\n")

        let elems = []
        $.each($('.await-color'), function (key, value) {
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
 * @param backgroundColor
 * @param defaultColor
 */
function setRandomColor(elem, backgroundColor = false, defaultColor = false) {
    if (defaultColor) {
        elem.css("background-color", "white");
        elem.css("color", "black");
        elem.css("text-shadow", "none");
        return;
    }

    if (elem.length > 1) {
        elem.css("background-color", backgroundColor);
    }
}

function setColorElems(elems) {
    let colors = getColorsArray()
    let color = colors.shift()

    $.each(elems, function (key, elem) {
        elem.css("background-color", color);
    })
}

function randomColor() {
    return Math.floor((Math.random() * 256));
}

/**
 * @param elem
 */
function coloredButtons(elem) {
    $('.colored-button').attr('class', 'btn btn-default colored-button')
    elem.attr('class', 'btn btn-secondary colored-button')
}

function showEquivalentElements() {
    let target = $('.await-color')

    target.unbind('mouseenter').mouseenter(function () {
        let background = $(this).css('background-color')
        $('.await-color').filter(function () {
            return validateColor(background, $(this))
        }).css("box-shadow", "inset 0 0 10px black");
    })

    target.unbind('mouseleave').mouseleave(function () {
        let background = $(this).css('background-color')
        $('.await-color').filter(function () {
            return validateColor(background, $(this))
        }).css("box-shadow", "none");
    })
}

function validateColor(background, target) {
    if (background === 'white' ||
        background === 'rgb(255, 255, 255)' ||
        background === 'rgba(0, 0, 0, 0)') {
        return false;
    }
    return target.css('background-color') === background &&
        target.attr('data-main-page') !== false &&
        typeof target.attr('data-main-page') !== 'undefined';
}

/**
 *
 * @returns {string[]}
 */
function getColorsArray() {
    let colorArray = [
        "rgba(220,51,10,0.6)",
        "rgba(121,25,6,0.6)",
        "rgba(214, 96, 110, 0.6)",
        "rgba(252, 170, 153, 0.6)",
        "rgba(214, 2, 86, 0.6)",
        "rgba(147,50,88, 0.6)",
        "rgba(247, 220, 163, 0.6)",
        "rgba(204, 118, 32, 0.6)",
        "rgba(255,89,0,0.6)",
        "rgba(164,58,1,0.6)",
        "rgba(73,28,1,0.6)",
        "rgba(178, 135, 33, 0.6)",
        "rgba(248,195,4,0.6)",
        "rgba(246,223,78,0.6)",
        "rgba(77,77,24,0.6)",
        "rgba(1,253,215,0.6)",
        "rgba(1,148,130,0.6)",
        "rgba(1,79,66,0.6)",
        "rgba(139, 150, 24, 0.6)",
        "rgba(154, 205, 50, 0.6)",
        "rgba(151, 186, 229, 0.6)",
        "rgba(0,69,255,0.6)",
        "rgba(1,45,152,0.6)",
        "rgba(0,24,75,0.6)",
        "rgba(157, 149, 226, 0.6)",
        "rgba(6, 136, 165, 0.6)",
        "rgba(64, 97, 206, 0.6)",
        "rgba(19,212,224, 0.6)",
        "rgba(2, 97, 214, 0.6)",
        "rgba(159, 112, 216, 0.6)",
        "rgba(239, 50, 223, 0.6)",
        "rgba(209, 46, 127, 0.6)",
        "rgba(194, 85, 237, 0.6)",
        "rgba(252, 194, 243, 0.6)",
        "rgba(244, 139, 200, 0.6)",
        "rgba(87,64,64, 0.6)",
        "rgba(26,25,25, 0.6)",
        "rgba(110,95,95, 0.6)",
    ]

    return colorArray.sort(() => Math.random() - 0.5);
}
