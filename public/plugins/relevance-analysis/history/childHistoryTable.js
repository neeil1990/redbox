let object
let hash = 'Loremipsumdolorsit'
hash = hash.split('').sort(function () {
    return 0.5 - Math.random()
}).join('');

function getHistoryInfo() {
    $('.get-history-info').unbind("click").click(function () {
        let id = $(this).attr('data-order')
        $.ajax({
            type: "get",
            dataType: "json",
            url: "/get-history-info/" + id,
            success: function (response) {
                $('.history').show()
                let history = response.history
                if (history.type === 'list') {
                    $('#key-phrase').hide()
                    $('#site-list').show()
                    $('#siteList').val(history.siteList)
                } else {
                    $('#key-phrase').show()
                    $('#site-list').hide()
                }

                $('.form-control.link').val(history.link)
                $('.form-control.phrase').val(history.phrase)
                $('#type').val(history.type)
                $('#hiddenId').val(id)
                $(".custom-select#count").val(history.count).change();
                $(".custom-select.rounded-0.region").val(history.region).change();
                $(".form-control.ignoredDomains").val(history.ignoredDomains);
                $("#separator").val(history.separator);

                changeSwitchState($('#switchNoindex'), history.noIndex)

                changeSwitchState($('#switchAltAndTitle'), history.hiddenText)

                changeSwitchState($('#switchConjunctionsPrepositionsPronouns'), history.conjunctionsPrepositionsPronouns)

                changeSwitchState($('#switchMyListWords'), history.switchMyListWords, history.listWords, '.listWords')
            },
        });
    });
}

function changeSwitchState(object, state, value = '', target = '') {
    if (state === "true") {
        if (!object.is(':checked')) {
            object.trigger('click')
        }
    } else {
        if (object.is(':checked')) {
            object.trigger('click')
        }
    }

    if (value !== '') {
        $(target).val(value)
        $(target).show()
    } else {
        $(target).hide()
    }
}

function changeState(elem) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/change-state",
        data: {
            id: elem.attr('data-target'),
            calculate: elem.is(':checked')
        },
    });
}

function isValidate(min, max, target, settings, tableId) {
    if (settings.nTable.id === tableId) {
        return (isNaN(min) && isNaN(max)) ||
            (isNaN(min) && target <= max) ||
            (min <= target && isNaN(max)) ||
            (min <= target && target <= max);
    } else {
        return true;
    }
}

function isIncludes(target, search, settings, tableId) {
    if (settings.nTable.id === tableId) {
        if (search.length > 0) {
            return target.includes(search)
        } else {
            return true;
        }
    } else {
        return true;
    }
}

function isDateValid(target, settings, tableId, prefix) {
    if (settings.nTable.id === tableId) {
        let date = new Date(target)
        let dateMin = new Date($('#dateMin' + prefix).val() + ' 00:00:00')
        let dateMax = new Date($('#dateMax' + prefix).val() + ' 23:59:59')
        if (date >= dateMin && date <= dateMax) {
            return true;
        }
    } else {
        return true;
    }
}

function scrollTo(elemPath) {
    $(document).ready(function () {
        $('html, body').animate({
            scrollTop: $(elemPath).offset().top
        }, {
            duration: 370,
            easing: "linear"
        });
    })
}

function hideListHistory() {
    $('.list-children').dataTable().fnDestroy();
    $('#list-history').dataTable().fnDestroy();
    $('#history-list-subject').hide()
    $('#list-history').hide()
}

function hideTableHistory() {
    $("#history_table").dataTable().fnDestroy();
    $('.render').remove()
    $('.history').hide()
}

function format(data) {
    let array = object

    let child = ''
    $.each(array[data], function (key, value) {
        let state
        if (value['state'] === 1) {
            state =
                '<button type="button" class="btn btn-secondary get-history-info" data-order="' + value['id'] + '" data-toggle="modal" data-target="#staticBackdrop">' +
                '   Повторить анализ' +
                '</button>' +
                "<a href='/show-history/" + value['id'] + "' target='_blank' class='btn btn-secondary mt-3'> Подробная информация</a>"

        } else if (value['state'] === 0) {
            state =
                '<p>Обрабатывается..</p>' +
                '<div class="text-center" id="preloaderBlock">' +
                '        <div class="three col">' +
                '            <div class="loader" id="loader-1"></div>' +
                '        </div>' +
                '</div>'
            checkAnalyseProgress(value['id'])
        } else if (value['state'] === -1) {
            state =
                '<button type="button" class="btn btn-secondary get-history-info" data-order="' + value['id'] + '" data-toggle="modal" data-target="#staticBackdrop">' +
                '   Повторить анализ' +
                '</button>' +
                "<span class='text-muted'>Произошла ошибка, повторите попытку или обратитесь к администратору</span>"
        }

        let checked = value['calculate'] ? 'checked' : ''
        child +=
            '<tr>' +
            '   <td>' + value['created_at'] + '</td>' +
            '   <td> <textarea style="width: 150px; height: 160px;" data-target="' + value['id'] + '" class="history-comment form form-control">' + value['comment'] + '</textarea></td>' +
            '   <td style="width: 150px;">' + value['phrase'] + '</td>' +
            '   <td style="width: 150px;">' + getRegionName(value['region']) + '</td>' +
            '   <td style="width: 150px;">' + value['main_link'] + '</td>' +
            '   <td>' + value['position'] + '</td>' +
            '   <td>' + value['points'] + '</td>' +
            '   <td>' + value['coverage'] + '</td>' +
            '   <td>' + value['coverage_tf'] + '</td>' +
            '   <td>' + value['width'] + '</td>' +
            '   <td>' + value['density'] + '</td>' +
            '   <td>' +
            "   <div class='d-flex justify-content-center'> " +
            "       <div class='__helper-link ui_tooltip_w'> " +
            "           <div class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'>" +
            "               <input onclick='changeState($(this))' type='checkbox' class='custom-control-input switch' id='calculate-project-" + value['id'] + "' name='noIndex' data-target='" + value['id'] + "' " + checked + ">" +
            "               <label class='custom-control-label' for='calculate-project-" + value['id'] + "'></label>" +
            "           </div>" +
            "       </div>" +
            "   </div>" +
            '   </td>' +
            '   <td id="history-state-' + value['id'] + '" class="d-flex flex-column">' +
            state +
            '   </td>' +
            '</tr>'


    })

    let date = new Date()

    let month = date.getMonth() + 1;
    if (month < 10) {
        month = '0' + month
    }

    if (date.getDate() < 10) {
        var day = '0' + date.getDate()
    } else {
        var day = date.getDate();
    }

    date = date.getFullYear() + '-' + month + '-' + day
    let tableId = data.replace(' ', '-')

    return (
        '<table class="table table-bordered table-hover dataTable dtr-inline list-children" id="' + tableId + '">' +
        '<thead>' +
        '<tr>' +
        '     <th style="position: inherit" class="table-header">' +
        '         <input class="w-100 form form-control" type="date" name="dateMin' + tableId + '"' +
        '                id="dateMin' + tableId + '"' +
        '                value="2022-03-01">' +
        '         <input class="w-100 form form-control" type="date" name="dateMax' + tableId + '" id="dateMax' + tableId + '"' +
        '                value="' + date + '">' +
        '     </th>' +
        '    <th style="position: inherit" class="table-header">' +
        '        <input class="w-100 form form-control search-input" type="text"' +
        '               name="projectComment' + tableId + '" id="projectComment' + tableId + '" placeholder="comment">' +
        '    </th>' +
        '    <th style="position: inherit" class="table-header">' +
        '        <input class="w-100 form form-control search-input" type="text"' +
        '               name="phraseSearch' + tableId + '" id="phraseSearch' + tableId + '" placeholder="phrase">' +
        '    </th>' +
        '    <th style="position: inherit" class="table-header">' +
        '        <input class="w-100 form form-control search-input" type="text"' +
        '               name="regionSearch' + tableId + '" id="regionSearch' + tableId + '" placeholder="region">' +
        '    </th>' +
        '    <th style="position: inherit" class="table-header">' +
        '        <input class="w-100 form form-control search-input" type="text"' +
        '               name="mainPageSearch' + tableId + '" id="mainPageSearch' + tableId + '" placeholder="link">' +
        '    </th>' +
        '    <th style="position: inherit" class="table-header">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="minPosition' + tableId + '" id="minPosition' + tableId + '" placeholder="min">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="maxPosition' + tableId + '" id="maxPosition' + tableId + '" placeholder="max">' +
        '    </th>' +
        '    <th style="position: inherit" class="table-header">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="minPoints' + tableId + '" id="minPoints' + tableId + '" placeholder="min">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="maxPoints' + tableId + '" id="maxPoints' + tableId + '" placeholder="max">' +
        '    </th>' +
        '    <th style="position: inherit" class="table-header">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="minCoverage' + tableId + '" id="minCoverage' + tableId + '" placeholder="min">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="maxCoverage' + tableId + '" id="maxCoverage' + tableId + '" placeholder="max">' +
        '    </th>' +
        '    <th style="position: inherit" class="table-header">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="minCoverageTf' + tableId + '" id="minCoverageTf' + tableId + '" placeholder="min">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="maxCoverageTf' + tableId + '" id="maxCoverageTf' + tableId + '" placeholder="max">' +
        '    </th>' +
        '    <th style="position: inherit" class="table-header">' +
        '        <input class="w-100 form form-control search-input" type="number" name="minWidth"' + tableId +
        '               id="minWidth' + tableId + '" placeholder="min">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="maxWidth' + tableId + '" id="maxWidth' + tableId + '" placeholder="max">' +
        '    </th>' +
        '    <th style="position: inherit" class="table-header">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="minDensity' + tableId + '" id="minDensity' + tableId + '" placeholder="min">' +
        '        <input class="w-100 form form-control search-input" type="number"' +
        '               name="maxDensity' + tableId + '" id="maxDensity' + tableId + '" placeholder="max">' +
        '    </th>' +
        '   <th style="position: inherit" class="table-header">' +
        '       <div>' +
        '          Переключить всё' +
        '          <div class="d-flex w-100">' +
        '             <div class="__helper-link ui_tooltip_w">' +
        '                 <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success changeAllStateList">' +
        '                     <input type="checkbox" id="changeAllStateList" class="custom-control-input"> ' +
        '                     <label for="changeAllStateList" class="custom-control-label"></label>' +
        '                 </div>' +
        '             </div>' +
        '          </div>' +
        '       </div>' +
        '   </th>' +
        '   <th></th>' +
        '   </tr>' +
        '      <tr>' +
        '         <th class="table-header">Дата сканирования</th>' +
        '         <th class="table-header" style="max-width: 150px">Комментарий</th>' +
        '         <th class="table-header">Фраза</th>' +
        '         <th class="table-header">Регион</th>' +
        '         <th class="table-header" style="max-width: 150px">Посадочная страница</th>' +
        '         <th class="table-header">Позиция в топе</th>' +
        '         <th class="table-header">Баллы</th>' +
        '         <th class="table-header">Охват важных слов</th>' +
        '         <th class="table-header">Охват важных tf</th>' +
        '         <th class="table-header">Ширина</th>' +
        '         <th class="table-header">Плотность</th>' +
        '         <th class="table-header">Учитывать в расчёте общего балла</th>' +
        '         <th class="table-header"></th>' +
        '   </tr>' +
        '</thead>' +
        child +
        '</table>'
    );
}

function repeatScan() {
    $('#relevance-repeat-scan').unbind("click").click(function () {
        let id = $('#hiddenId').val()
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/repeat-scan",
            data: {
                id: id,
                type: $('#type').val(),
                siteList: $('#siteList').val(),
                link: $('.form-control.link').val(),
                phrase: $('.form-control.phrase').val(),
                count: $(".custom-select#count").val(),
                region: $(".custom-select.rounded-0.region").val(),
                ignoredDomains: $(".form-control.ignoredDomains").val(),
                separator: $("#separator").val(),
                noIndex: $('#switchNoindex').is(':checked'),
                hiddenText: $('#switchAltAndTitle').is(':checked'),
                conjunctionsPrepositionsPronouns: $('#switchConjunctionsPrepositionsPronouns').is(':checked'),
                switchMyListWords: $('#switchMyListWords').is(':checked'),
                listWords: $('.form-control.listWords').val(),
            },
            success: function (response) {
                if (response.code === 415) {
                    $('#message-error-info').html(response.message)
                    $('.toast-top-right.error-message').show(300)

                    setTimeout(() => {
                        $('.toast-top-right.error-message').show(300)
                    }, 5000)
                }

                if (response.code === 200) {
                    $('#history-state-' + id).html('<p>Обрабатывается..</p>' +
                        '<div class="text-center" id="preloaderBlock">' +
                        '        <div class="three col">' +
                        '            <div class="loader" id="loader-1"></div>' +
                        '        </div>' +
                        '</div>')
                    checkAnalyseProgress(id)
                }

            },
            error: function (response) {
                $('#toast-container').show(300)
                $('#message-info').html(response.responseJSON.message)
                setInterval(function () {
                    $('#toast-container').hide(300)
                }, 3500)
            }
        });
    });
}

function checkAnalyseProgress(id) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/check-state",
        data: {
            id: id,
        },
        success: function (response) {
            if (response.message === 'wait') {
                setTimeout(() => {
                    checkAnalyseProgress(id)
                }, 10000)
            } else if (response.message === 'error') {
                $('#history-state-' + id).html(
                    '<button type="button" class="btn btn-secondary get-history-info" data-order="' + id + '"' +
                    '        data-toggle="modal" data-target="#staticBackdrop"> Повторить анализ' +
                    '</button>' +
                    '<span class="text-muted">Произошла ошибка, повторите попытку или обратитесь к администратору</span>'
                );
            } else if (response.message === 'success') {
                $('#history-state-' + id).html(
                    '<button type="button" class="btn btn-secondary get-history-info" data-order="' + id + '"' +
                    '   data-toggle="modal" data-target="#staticBackdrop"> Повторить анализ' +
                    '</button>' +
                    '<a href="/show-history/' + id + '" target="_blank" class="btn btn-secondary mt-3"> Подробная информация</a>'
                );

                let table = $('#history_table').DataTable();
                let newObject = response.newObject

                table.row.add({
                    0: newObject['last_check'],
                    1: '<textarea style="height: 160px;" data-target="' + newObject['id'] + '" class="history-comment form form-control"></textarea>',
                    2: newObject['phrase'],
                    3: getRegionName(newObject['region']),
                    4: newObject['main_link'],
                    5: newObject['position'] === 0 ? 'Не попал в топ 100' : newObject['position'],
                    6: newObject['points'],
                    7: newObject['coverage'],
                    8: newObject['coverage_tf'],
                    9: newObject['width'],
                    10: newObject['density'],
                    11: '<div class="d-flex justify-content-center">' +
                        '    <div class="__helper-link ui_tooltip_w">' +
                        '        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">' +
                        '            <input checked onclick="changeState(' + $(this) + ')" type="checkbox"' +
                        '                   class="custom-control-input switch" id="calculate-project-' + newObject['id'] + '" name="noIndex"' +
                        '                   data-target="' + newObject['id'] + '"> ' +
                        '               <label class="custom-control-label" for="calculate-project-' + newObject['id'] + '"></label></div>' +
                        '    </div>' +
                        '</div>',
                    12: '<div id="history-state-' + newObject['id'] + '">' +
                        '       <button type="button" class="btn btn-secondary get-history-info" data-order="' + newObject['id'] + '" data-toggle="modal" data-target="#staticBackdrop"> Повторить анализ </button>' +
                        '       <a href="/show-history/' + newObject['id'] + '" target="_blank" class="btn btn-secondary mt-3"> Подробная информация</a>' +
                        '</div>'
                });

                table.draw()
            }

        },
    });
}

function customFilters(tableID, table, prefix = '', index = 0) {
    $.fn.dataTable.ext.search.push(function (settings, data) {
        let target = String(data[index]);
        return isDateValid(target, settings, tableID, prefix)
    });
    $('#dateMin' + prefix).change(function () {
        table.draw();
    });
    $('#dateMax' + prefix).change(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let phraseSearch = String($('#projectComment' + prefix).val()).toLowerCase();
        let target = String(data[index + 1]).toLowerCase();
        return isIncludes(target, phraseSearch, settings, tableID)
    });
    $('#projectComment' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let phraseSearch = String($('#phraseSearch' + prefix).val()).toLowerCase();
        let target = String(data[index + 2]).toLowerCase();
        return isIncludes(target, phraseSearch, settings, tableID)
    });
    $('#phraseSearch' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let regionSearch = String($('#regionSearch' + prefix).val()).toLowerCase();
        let target = String(data[index + 3]).toLowerCase();
        return isIncludes(target, regionSearch, settings, tableID)
    });
    $('#regionSearch' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let mainPageSearch = String($('#mainPageSearch' + prefix).val()).toLowerCase();
        let target = String(data[index + 4]).toLowerCase();
        return isIncludes(target, mainPageSearch, settings, tableID)
    });
    $('#mainPageSearch' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxPosition = parseFloat($('#maxPosition' + prefix).val());
        let minPosition = parseFloat($('#minPosition' + prefix).val());
        let target = parseFloat(data[index + 5]);
        return isValidate(minPosition, maxPosition, target, settings, tableID)
    });
    $('#minPosition' + prefix + ', #maxPosition' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxPoints = parseFloat($('#maxPoints' + prefix).val());
        let minPoints = parseFloat($('#minPoints' + prefix).val());
        let target = parseFloat(data[index + 6]);
        return isValidate(minPoints, maxPoints, target, settings, tableID)
    });
    $('#minPoints' + prefix + ', #maxPoints' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxAVGPoints = parseFloat($('#maxAVGPoints' + prefix).val());
        let minAvgPoints = parseFloat($('#minAVGPoints' + prefix).val());
        let target = parseFloat(data[index + 1 + 6]);
        return isValidate(minAvgPoints, maxAVGPoints, target, settings, tableID)
    });
    $('#minAVGPoints' + prefix + ', #maxAVGPoints' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxCoverage = parseFloat($('#maxCoverage' + prefix).val());
        let minCoverage = parseFloat($('#minCoverage' + prefix).val());
        let target = parseFloat(data[index + 7]);
        return isValidate(minCoverage, maxCoverage, target, settings, tableID)
    });
    $('#minCoverage' + prefix + ', #maxCoverage' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxCoverageTf = parseFloat($('#maxCoverageTf' + prefix).val());
        let minCoverageTf = parseFloat($('#minCoverageTf' + prefix).val());
        let target = parseFloat(data[index + 8]);
        return isValidate(minCoverageTf, maxCoverageTf, target, settings, tableID)
    });
    $('#minCoverageTf' + prefix + ', #maxCoverageTf' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxWidth = parseFloat($('#maxWidth' + prefix).val());
        let minWidth = parseFloat($('#minWidth' + prefix).val());
        let target = parseFloat(data[index + 9]);
        return isValidate(minWidth, maxWidth, target, settings, tableID)
    });
    $('#minWidth' + prefix + ', #maxWidth' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxDensity = parseFloat($('#maxDensity' + prefix).val());
        let minDensity = parseFloat($('#minDensity' + prefix).val());
        let target = parseFloat(data[index + 10]);
        return isValidate(minDensity, maxDensity, target, settings, tableID)
    });
    $('#minDensity' + prefix + ', #maxDensity' + prefix).keyup(function () {
        table.draw();
    });
}

function customHistoryFilters(tableID, table, prefix = '') {
    $.fn.dataTable.ext.search.push(function (settings, data) {
        let target = String(data[0]);
        return isDateValid(target, settings, tableID, prefix)
    });
    $('#dateMin' + prefix).change(function () {
        table.draw();
    });
    $('#dateMax' + prefix).change(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let phraseSearch = String($('#projectComment' + prefix).val()).toLowerCase();
        let target = String(data[1]).toLowerCase();
        return isIncludes(target, phraseSearch, settings, tableID)
    });
    $('#projectComment' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let phraseSearch = String($('#phraseSearch' + prefix).val()).toLowerCase();
        let target = String(data[2]).toLowerCase();
        return isIncludes(target, phraseSearch, settings, tableID)
    });
    $('#phraseSearch' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let regionSearch = String($('#regionSearch' + prefix).val()).toLowerCase();
        let target = String(data[3]).toLowerCase();
        return isIncludes(target, regionSearch, settings, tableID)
    });
    $('#regionSearch' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let mainPageSearch = String($('#mainPageSearch' + prefix).val()).toLowerCase();
        let target = String(data[4]).toLowerCase();
        return isIncludes(target, mainPageSearch, settings, tableID)
    });
    $('#mainPageSearch' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxPosition = parseFloat($('#maxPosition' + prefix).val());
        let minPosition = parseFloat($('#minPosition' + prefix).val());
        let target = parseFloat(data[5]);
        return isValidate(minPosition, maxPosition, target, settings, tableID)
    });
    $('#minPosition' + prefix + ', #maxPosition' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxPoints = parseFloat($('#maxPoints' + prefix).val());
        let minPoints = parseFloat($('#minPoints' + prefix).val());
        let target = parseFloat(data[6]);
        return isValidate(minPoints, maxPoints, target, settings, tableID)
    });
    $('#minPoints' + prefix + ', #maxPoints' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxCoverage = parseFloat($('#maxCoverage' + prefix).val());
        let minCoverage = parseFloat($('#minCoverage' + prefix).val());
        let target = parseFloat(data[7]);
        return isValidate(minCoverage, maxCoverage, target, settings, tableID)
    });
    $('#minCoverage' + prefix + ', #maxCoverage' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxCoverageTf = parseFloat($('#maxCoverageTf' + prefix).val());
        let minCoverageTf = parseFloat($('#minCoverageTf' + prefix).val());
        let target = parseFloat(data[8]);
        return isValidate(minCoverageTf, maxCoverageTf, target, settings, tableID)
    });
    $('#minCoverageTf' + prefix + ', #maxCoverageTf' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxWidth = parseFloat($('#maxWidth' + prefix).val());
        let minWidth = parseFloat($('#minWidth' + prefix).val());
        let target = parseFloat(data[9]);
        return isValidate(minWidth, maxWidth, target, settings, tableID)
    });
    $('#minWidth' + prefix + ', #maxWidth' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxDensity = parseFloat($('#maxDensity' + prefix).val());
        let minDensity = parseFloat($('#minDensity' + prefix).val());
        let target = parseFloat(data[10]);
        return isValidate(minDensity, maxDensity, target, settings, tableID)
    });
    $('#minDensity' + prefix + ', #maxDensity' + prefix).keyup(function () {
        table.draw();
    });
}

function getColor(result, ideal) {
    let percent = ideal / 100

    let difference = 100 - (result / percent)

    if (difference >= 0 && difference < 15 || difference < 0) {
        return 'rgba(78,183,103,0.5)';
    }

    if (difference >= 15 && difference <= 20) {
        return 'rgba(245,226,170,0.5)';
    }

    return 'rgba(220,53,69,0.5)';
}
