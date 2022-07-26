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
    $('html, body').animate({
        scrollTop: $(elemPath).offset().top
    }, {
        duration: 370,
        easing: "linear"
    });
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
                '</button>'
                +
                "<a href='/show-history/" + value['id'] + "' target='_blank' class='btn btn-secondary mt-3'> Подробная информация</a>"

        } else if (value['state'] === 0) {
            state =
                '<p>Обрабатывается..</p>' +
                '<div class="text-center" id="preloaderBlock">' +
                '        <div class="three col">' +
                '            <div class="loader" id="loader-1"></div>' +
                '        </div>' +
                '</div>'
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
            success: function () {
                $('#history-state-' + id).html('<p>Обрабатывается..</p>' +
                    '<div class="text-center" id="preloaderBlock">' +
                    '        <div class="three col">' +
                    '            <div class="loader" id="loader-1"></div>' +
                    '        </div>' +
                    '</div>')
            },
            error: function () {
                $('#toast-container').show(300)
                $('#message-info').html('Что-то пошло не так, повторите попытку позже.')
                setInterval(function () {
                    $('#toast-container').hide(300)
                }, 3500)
            }
        });
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
    let pos = '#minPosition' + prefix + ', #maxPosition' + prefix
    $(pos).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxPoints = parseFloat($('#maxPoints' + prefix).val());
        let minPoints = parseFloat($('#minPoints' + prefix).val());
        let target = parseFloat(data[index + 6]);
        return isValidate(minPoints, maxPoints, target, settings, tableID)
    });
    let points = '#minPoints' + prefix + ', #maxPoints' + prefix
    $(points).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxCoverage = parseFloat($('#maxCoverage' + prefix).val());
        let minCoverage = parseFloat($('#minCoverage' + prefix).val());
        let target = parseFloat(data[index + 7]);
        return isValidate(minCoverage, maxCoverage, target, settings, tableID)
    });
    let coverage = '#minCoverage' + prefix + ', #maxCoverage' + prefix
    $(coverage).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxCoverageTf = parseFloat($('#maxCoverageTf' + prefix).val());
        let minCoverageTf = parseFloat($('#minCoverageTf' + prefix).val());
        let target = parseFloat(data[index + 8]);
        return isValidate(minCoverageTf, maxCoverageTf, target, settings, tableID)
    });
    let covTf = '#minCoverageTf' + prefix + ', #maxCoverageTf' + prefix
    $(covTf).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxWidth = parseFloat($('#maxWidth' + prefix).val());
        let minWidth = parseFloat($('#minWidth' + prefix).val());
        let target = parseFloat(data[index + 9]);
        return isValidate(minWidth, maxWidth, target, settings, tableID)
    });
    let width = '#minWidth' + prefix + ', #maxWidth' + prefix
    $(width).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxDensity = parseFloat($('#maxDensity' + prefix).val());
        let minDensity = parseFloat($('#minDensity' + prefix).val());
        let target = parseFloat(data[index + 10]);
        return isValidate(minDensity, maxDensity, target, settings, tableID)
    });
    let density = '#minDensity' + prefix + ', #maxDensity' + prefix
    $(density).keyup(function () {
        table.draw();
    });
}

$(document).ready(function () {

    setInterval(() => {
        $('#changeAllState, #changeAllStateList').unbind().on('change', function () {
            let state = $(this).is(':checked')
            $.each($('.custom-control-input.switch'), function () {
                if (state !== $(this).is(':checked')) {
                    $(this).trigger('click');
                }
            });
        });

        $('.history-comment').unbind().change(function () {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/edit-history-comment",
                data: {
                    id: $(this).attr('data-target'),
                    comment: $(this).val()
                },
                success: function () {
                    $('#toast-container').show(300)
                    $('#message-info').html('Коментарий успешно сохранён')
                    setInterval(function () {
                        $('#toast-container').hide(300)
                    }, 3000)
                },
            });
        });

        $('.project_name').unbind().click(function () {
            hideListHistory()
            hideTableHistory()

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/get-stories",
                data: {
                    history_id: $(this).attr('data-order'),
                },
                async: true,
                success: function (response) {
                    if (response.code === 415) {
                        getErrorMessage(response.message)
                    } else {
                        $('#changeAllState').prop('checked', false);
                        $('#changeAllStateList').prop('checked', false);
                        $('.search-input').val('')
                        $('.history').show()
                        let tbody = $('#historyTbody')

                        $.each(response.stories, function (key, val) {
                            let checked = val.calculate ? 'checked' : ''
                            let state

                            if (val.state === 1) {
                                state =
                                    '<button type="button" class="btn btn-secondary get-history-info" data-order="' + val.id + '" data-toggle="modal" data-target="#staticBackdrop">' +
                                    '   Повторить анализ' +
                                    '</button>'
                                    +
                                    "<a href='/show-history/" + val.id + "' target='_blank' class='btn btn-secondary mt-3'> Подробная информация</a>"

                            } else if (val.state === 0) {
                                state =
                                    '<p>Обрабатывается..</p>' +
                                    '<div class="text-center" id="preloaderBlock">' +
                                    '        <div class="three col">' +
                                    '            <div class="loader" id="loader-1"></div>' +
                                    '        </div>' +
                                    '</div>'
                            } else if (val.state === -1) {
                                state =
                                    '<button type="button" class="btn btn-secondary get-history-info" data-order="' + val.id + '" data-toggle="modal" data-target="#staticBackdrop">' +
                                    '   Повторить анализ' +
                                    '</button>' +
                                    "<span class='text-muted'>Произошла ошибка, повторите попытку или обратитесь к администратору</span>"
                            }

                            let position = val.position

                            if (val.position == 0) {
                                position = 'Не попал в топ 100'
                            }

                            let phrase = val.phrase

                            if (phrase == null) {
                                phrase = 'Был использван анализ без ключевой фразы'
                            }

                            tbody.append(
                                "<tr class='render'>" +
                                "   <td>" + val.last_check + "</td>" +
                                "   <td>" +
                                "      <textarea style='height: 160px;' data-target='" + val.id + "' class='history-comment form form-control' >" + val.comment + "</textarea>" +
                                "   </td>" +
                                "   <td>" + phrase + "</td>" +
                                "   <td>" + getRegionName(val.region) + "</td>" +
                                "   <td>" + val.main_link + "</td>" +
                                "   <td>" + position + "</td>" +
                                "   <td>" + val.points + "</td>" +
                                "   <td>" + val.coverage + "</td>" +
                                "   <td>" + val.coverage_tf + "</td>" +
                                "   <td>" + val.width + "</td>" +
                                "   <td>" + val.density + "</td>" +
                                "   <td>" +
                                "      <div class='d-flex justify-content-center'> " +
                                "          <div class='__helper-link ui_tooltip_w'> " +
                                "              <div class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'>" +
                                "                  <input onclick='changeState($(this))' type='checkbox' class='custom-control-input switch' id='calculate-project-" + val.id + "' name='noIndex' data-target='" + val.id + "' " + checked + ">" +
                                "                  <label class='custom-control-label' for='calculate-project-" + val.id + "'></label>" +
                                "              </div>" +
                                "          </div>" +
                                "      </div>" +
                                "   </td>" +
                                "   <td id='history-state-" + val.id + "'>" +
                                state +
                                "   </td>" +
                                "</tr>"
                            )
                        })

                        $(document).ready(function () {
                            if($.fn.DataTable.fnIsDataTable($('#history_table'))) {
                                $('#history_table').dataTable().fnDestroy();
                            }

                            let historyTable = $('#history_table').DataTable({
                                "order": [[0, "desc"]],
                                "pageLength": 25,
                                "searching": true,
                                dom: 'lBfrtip',
                                buttons: [
                                    'copy', 'csv', 'excel'
                                ]
                            });

                            $('#history_table').wrap("<div style='width: 100%; overflow-x: scroll; max-height:90vh;'></div>")

                            $(".dt-button").addClass('btn btn-secondary')

                            $('#history_table_filter').hide()

                            scrollTo('#tab_1 > div.history > h3')

                            repeatScan()

                            customFilters('history_table', historyTable)
                        });
                    }
                },
            });
        });

        $('.project_name_v2').unbind().click(function () {
            hideListHistory()
            hideTableHistory()

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/get-stories-v2",
                async: true,
                data: {
                    historyId: $(this).attr('data-order'),
                },
                success: function (response) {
                    if (response.code === 415) {
                        getErrorMessage(response.message)
                    } else {
                        $('#history-list-subject').show()
                        $('#list-history').show()
                        object = response.object
                        $.each(response.object, function (key, value) {
                            let position = value[0]['position']
                            if (position == 0) {
                                position = 'Не попал в топ 100'
                            }
                            $('#list-history-body').append(
                                '<tr class="render">' +
                                '   <td data-target="' + key + '" class="col-1" style="text-align: center; vertical-align: inherit; width: 50px"></td>' +
                                '   <td>' + value[0]['created_at'] + '</td>' +
                                '   <td>' + key + '</td>' +
                                '   <td>' + getRegionName(value[0]['region']) + '</td>' +
                                '   <td>' + value[0]['main_link'] + '</td>' +
                                '   <td>' + position + '</td>' +
                                '   <td>' + value[0]['points'] + '</td>' +
                                '   <td>' + value[0]['coverage'] + '</td>' +
                                '   <td>' + value[0]['coverage_tf'] + '</td>' +
                                '   <td>' + value[0]['width'] + '</td>' +
                                '   <td>' + value[0]['density'] + '</td>' +
                                '</tr>'
                            )
                        })
                        $(document).ready(function () {
                            $('.dataTables_wrapper.no-footer').css({
                                width: '100%'
                            })

                            $('#list-history-body > tr.render > td.col-1').append('<i class="fa fa-eye"></i>')

                            if($.fn.DataTable.fnIsDataTable($('#list-history'))) {
                                $('#list-history').dataTable().fnDestroy();
                            }

                            let listTable = $('#list-history').DataTable({
                                columns: [
                                    {
                                        className: 'dt-control',
                                        orderable: false,
                                    },
                                    {data: 'date'},
                                    {data: 'phrase'},
                                    {data: 'region'},
                                    {data: 'link'},
                                    {data: 'position'},
                                    {data: 'points'},
                                    {data: 'coverage'},
                                    {data: 'coverage_tf'},
                                    {data: 'width'},
                                    {data: 'density'},
                                ],
                                order: [[1, 'desc']],
                                destroy: true
                            });

                            scrollTo('#history-list-subject')

                            customFiltersWithoutComment('list-history', listTable, 'List', 1)
                            $('#list-history').wrap("<div style='width: 100%; overflow-x: scroll; max-height:90vh;'></div>")

                            $('#list-history').unbind().on('click', 'td.dt-control', function () {
                                let tr = $(this).closest('tr');
                                let row = listTable.row(tr);

                                if (row.child.isShown()) {
                                    row.child.hide();
                                    tr.removeClass('shown');
                                    $('#' + $(this).attr('data-target').replace(' ', '-')).dataTable().fnDestroy()
                                } else {
                                    row.child(format($(this).attr('data-target'))).show();
                                    tr.addClass('shown');
                                    let target = $(this).attr('data-target').replace(' ', '-')
                                    let table = $('#' + target).DataTable({
                                        order: [[0, 'desc']],
                                        destroy: true
                                    })
                                    customFilters(target, table, target)
                                }
                            });
                        })
                        repeatScan()
                    }
                }
            });
        })

        getHistoryInfo()
    }, 500)

})

function customFiltersWithoutComment(tableID, table, prefix = '', index = 0) {
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
        let phraseSearch = String($('#phraseSearch' + prefix).val()).toLowerCase();
        let target = String(data[index + 1]).toLowerCase();
        return isIncludes(target, phraseSearch, settings, tableID)
    });
    $('#phraseSearch' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let regionSearch = String($('#regionSearch' + prefix).val()).toLowerCase();
        let target = String(data[index + 2]).toLowerCase();
        return isIncludes(target, regionSearch, settings, tableID)
    });
    $('#regionSearch' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let mainPageSearch = String($('#mainPageSearch' + prefix).val()).toLowerCase();
        let target = String(data[index + 3]).toLowerCase();
        return isIncludes(target, mainPageSearch, settings, tableID)
    });
    $('#mainPageSearch' + prefix).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxPosition = parseFloat($('#maxPosition' + prefix).val());
        let minPosition = parseFloat($('#minPosition' + prefix).val());
        let target = parseFloat(data[index + 4]);
        return isValidate(minPosition, maxPosition, target, settings, tableID)
    });
    let pos = '#minPosition' + prefix + ', #maxPosition' + prefix
    $(pos).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxPoints = parseFloat($('#maxPoints' + prefix).val());
        let minPoints = parseFloat($('#minPoints' + prefix).val());
        let target = parseFloat(data[index + 5]);
        return isValidate(minPoints, maxPoints, target, settings, tableID)
    });
    let points = '#minPoints' + prefix + ', #maxPoints' + prefix
    $(points).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxCoverage = parseFloat($('#maxCoverage' + prefix).val());
        let minCoverage = parseFloat($('#minCoverage' + prefix).val());
        let target = parseFloat(data[index + 6]);
        return isValidate(minCoverage, maxCoverage, target, settings, tableID)
    });
    let coverage = '#minCoverage' + prefix + ', #maxCoverage' + prefix
    $(coverage).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxCoverageTf = parseFloat($('#maxCoverageTf' + prefix).val());
        let minCoverageTf = parseFloat($('#minCoverageTf' + prefix).val());
        let target = parseFloat(data[index + 7]);
        return isValidate(minCoverageTf, maxCoverageTf, target, settings, tableID)
    });
    let covTf = '#minCoverageTf' + prefix + ', #maxCoverageTf' + prefix
    $(covTf).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxWidth = parseFloat($('#maxWidth' + prefix).val());
        let minWidth = parseFloat($('#minWidth' + prefix).val());
        let target = parseFloat(data[index + 8]);
        return isValidate(minWidth, maxWidth, target, settings, tableID)
    });
    let width = '#minWidth' + prefix + ', #maxWidth' + prefix
    $(width).keyup(function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        let maxDensity = parseFloat($('#maxDensity' + prefix).val());
        let minDensity = parseFloat($('#minDensity' + prefix).val());
        let target = parseFloat(data[index + 9]);
        return isValidate(minDensity, maxDensity, target, settings, tableID)
    });
    let density = '#minDensity' + prefix + ', #maxDensity' + prefix
    $(density).keyup(function () {
        table.draw();
    });
}
