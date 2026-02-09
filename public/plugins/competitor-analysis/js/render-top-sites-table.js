function renderTopSites(analysedSites, messages) {
    $.each(analysedSites, function (phrase, sites) {
        let tr = '<tr style="height: 80px" class="render">'
        tr += '<td>' + phrase + '</td>'
        $.each(sites, function (site, info) {
            let url = new URL(site)

            let btnGroup = getBtnGroup(url, messages)

            let danger = info['danger'] ? 'danger' : ''

            let infoBlock

            if (info['danger']) {
                infoBlock = '<div class="text-danger mt-2">' + messages.protected + '</div>'
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

function renderTopSitesV2(analysedSites, messages) {
    let domains = [];
    let links = [];
    $.each(analysedSites, function (phrase, sites) {
        let newTable = '' +
            '<div class="card render mt-3" style="width: 300px; flex-shrink: 0">' +
            '   <div class="card-header separate-header border" data-toggle="tooltip" data-placement="top" title="' + phrase + '"><h3>' + phrase + '</h3></div>' +
            '   <div class="card-body p-0 d-flex flex-column">' +
            '       <div class="fixed-color d-flex p-2 border">' +
            '           <div class="font-weight-bold pr-2">#</div>' +
            '           <div class="font-weight-bold">' + messages.domain + '</div>' +
            '       </div>'

        let iterator = 1
        $.each(sites, function (link, object) {
            let url = new URL(link)
            let btnGroup = getBtnGroup(url, messages)
            newTable +=
                '<div class="d-flex p-2 align-items-center justify-content-start border await-color" style="cursor: pointer; height: 75px" ' +
                'data-order="' + url['host'] + '" ' +
                'data-full-url="' + link + '" ' +
                'data-main-page="' + object['mainPage'] + '" title="' + link + '">' +
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
        links = [...new Set(links)]
        let colors = getColorsArray()

        $.each(links, function (key, value) {
            setRandomColor(
                $('[data-full-url="' + value + '"]'),
                colors[Math.floor((Math.random() * colors.length))]
            )
        })

        colorButtonsActions([...new Set(domains)], links)
    })

    $('#sites-block').show()

    showEquivalentElements()

    let keyCount = Object.keys(analysedSites).length;
    let filename = `export-${keyCount}.xlsx`;
    let $exportButton = $('.site-block-buttons').find('#exportXLS');

    $exportButton.unbind().click(() => exportAnalysedSitesToExcel(analysedSites, filename));

    $('[data-toggle="tooltip"]').tooltip()
}

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

function getBtnGroup(url, messages) {
    return '<div class="btn-group pl-1 p-0">' +
        '   <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-tool dropdown-toggle p-0" style="color: black;">' +
        '   <i class="fas fa-external-link-alt"></i>' +
        '   </button>' +
        '       <div role="menu" class="dropdown-menu dropdown-menu-left">' +

        '       <a target="_blank" class="dropdown-item" href="' + url['href'] + '" style="text-shadow: none">' +
        '       <i class="fas fa-external-link-alt"></i> ' + messages.mainPage + '</a>' +

        '       <a target="_blank" class="dropdown-item" href="' + url['origin'] + '" style="text-shadow: none">' +
        '       <i class="fas fa-external-link-alt"></i> ' + messages.site + '</a>' +

        '       <a style="text-shadow: none" target="_blank" class="dropdown-item" href="/redirect-to-text-analyzer/' + url['href'].replace(/\\|\//g, 'abc') + '">' +
        '       <i class="fas fa-external-link-alt"></i> ' + messages.analyzeText + '</a>' +

        '   </div>' +
        '</div>'
}

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
            setRandomColor(
                $('[data-order="' + value + '"]'),
                colors[Math.floor((Math.random() * colors.length))]
            )
        })
    })

    $('#coloredEloquentUrls').unbind().on('click', function () {
        coloredButtons($(this))
        setRandomColor($('.await-color'), false, true)

        let colors = getColorsArray()
        $.each(links, function (key, value) {
            setRandomColor(
                $('[data-full-url="' + value + '"]'),
                colors[Math.floor((Math.random() * colors.length))]
            )
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

function setRandomColor(elem, backgroundColor = false, defaultColor = false) {
    if (defaultColor) {
        elem.css("background", "white");
        elem.css("color", "black");
        elem.css("text-shadow", "none");
        return;
    }

    if (elem.length > 1) {
        elem.css("background", 'linear-gradient(45deg,' + backgroundColor + ' 92%,' + generateRandomColor() + ' 8%)');
    }
}

function setColorElems(elems) {
    let colors = getColorsArray()
    let color = colors.shift()

    $.each(elems, function (key, elem) {
        elem.css("background-color", color);
    })
}

function generateRandomColor() {
    const red = Math.floor(Math.random() * 128) + 128;
    const green = Math.floor(Math.random() * 128) + 128;
    const blue = Math.floor(Math.random() * 128) + 128;

    return "rgb(" + red + ", " + green + ", " + blue + ")";
}

function coloredButtons(elem) {
    $('.colored-button').attr('class', 'btn btn-default colored-button')
    elem.attr('class', 'btn btn-secondary colored-button')
}

function showEquivalentElements() {
    let target = $('.await-color')

    target.unbind('mouseenter').mouseenter(function () {
        let background = $(this).css('background')
        $('.await-color').filter(function () {
            return validateColor(background, $(this))
        }).css("box-shadow", "inset 0 0 10px black");
    })

    target.unbind('mouseleave').mouseleave(function () {
        let background = $(this).css('background')
        $('.await-color').filter(function () {
            return validateColor(background, $(this))
        }).css("box-shadow", "none");
    })
}

function validateColor(background, target) {
    if (
        background === 'rgba(0, 0, 0, 0) none repeat scroll 0% 0% / auto padding-box border-box' ||
        background === 'rgb(255, 255, 255) none repeat scroll 0% 0% / auto padding-box border-box'
    ) {
        return false;
    }
    return target.css('background') === background &&
        target.attr('data-main-page') !== false &&
        typeof target.attr('data-main-page') !== 'undefined';
}

function getColorsArray() {
    let colorArray = [
        "rgba(255, 0, 0, 1)",
        "rgba(220, 51, 10, 0.6)",
        "rgb(203,60,25)",
        "rgba(121, 25, 6, 0.6)",
        "rgba(121, 25, 6, 1)",
        "rgba(214, 96, 110, 0.6)",
        "rgba(214, 96, 110, 1)",
        "rgba(252, 170, 153, 0.6)",
        "rgba(252, 170, 153, 1)",
        "rgba(214, 2, 86, 0.6)",
        "rgba(214, 2, 86, 1)",
        "rgba(147,50,88, 0.6)",
        "rgba(147,50,88, 1)",
        "rgba(247, 220, 163, 1)",
        "rgba(204, 118, 32, 0.6)",
        "rgba(204, 118, 32, 1)",
        "rgba(255,89,0,0.6)",
        "rgba(255, 89, 0, 1)",
        "rgba(164, 58 ,1, 0.6)",
        "rgba(164, 58 ,1, 1)",
        "rgba(73, 28, 1, 0.6)",
        "rgba(178, 135, 33, 0.6)",
        "rgba(178, 135, 33, 1)",
        "rgba(246, 223, 78, 0.6)",
        "rgba(246, 223, 78, 1)",
        "rgba(77, 77, 24, 0.6)",
        "rgba(1, 253, 215, 0.6)",
        "rgba(1, 253, 215, 1)",
        "rgba(1, 148, 130, 0.6)",
        "rgba(1, 79, 66, 0.6)",
        "rgba(139, 150, 24, 0.6)",
        "rgba(154, 205, 50, 0.6)",
        "rgba(154, 205, 50, 1)",
        "rgb(17, 255, 0)",
        "rgba(151, 186, 229, 1)",
        "rgba(0, 69, 255, 0.6)",
        "rgba(0, 69, 255, 1)",
        "rgba(1, 45, 152, 0.6)",
        "rgba(0, 24, 75, 0.3)",
        "rgba(157, 149, 226, 0.6)",
        "rgba(157, 149, 226, 1)",
        "rgba(6, 136, 165, 0.6)",
        "rgba(6, 136, 165, 1)",
        "rgba(64, 97, 206, 1)",
        "rgba(19,212,224, 0.6)",
        "rgba(19,212,224, 1)",
        "rgba(2, 97, 214, 0.6)",
        "rgba(159, 112, 216, 0.6)",
        "rgba(239, 50, 223, 0.6)",
        "rgba(239, 50, 223, 1)",
        "rgba(209, 46, 127, 0.6)",
        "rgba(209, 46, 127, 1)",
        "rgba(194, 85, 237, 1)",
        "rgba(252, 194, 243, 1)",
        "rgba(244, 139, 200, 0.6)",
        "rgba(244, 139, 200, 1)",
        "rgba(87, 64, 64, 0.6)",
        "rgba(26, 25, 25, 0.6)",
        "rgba(110, 95, 95, 0.6)",
        "rgba(239, 211, 211, 0.6)",
        "rgba(163, 209, 234, 0.6)",
        "rgba(234,163,163,0.6)",
        "rgba(232,194,90,0.6)",
    ]

    return colorArray.sort(() => Math.random() - 0.5);
}

function exportAnalysedSitesToExcel(obj, filename = 'report.xlsx') {
    let data = [];

    $.each(obj, function (cols, value) {
        let sites = Object.keys(value);
        $.each(sites, function (index, site) {
            let row = {[cols]: site}
            if (data[index] === undefined) {
                data.push(row)
            } else {
                $.extend( data[index], row );
            }
        })
    })

    exportToExcel(data, filename);
}
