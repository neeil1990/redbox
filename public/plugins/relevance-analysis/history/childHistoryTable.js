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
                    $('.form-control.phrase').val(history.phrase)
                }
                $('#type').val(history.type)
                $('#hiddenId').val(id)
                $('.form-control.link').val(history.link)
                $(".custom-select#count").val(history.count).change();
                $(".custom-select.rounded-0.region").val(history.region).change();
                $(".form-control.ignoredDomains").html(history.ignoredDomains);
                $("#separator").val(history.separator);

                if (history.noIndex === "true") {
                    $('#switchNoindex').trigger('click')
                }

                if (history.hiddenText === "true") {
                    $('#switchAltAndTitle').trigger('click')
                }

                if (history.conjunctionsPrepositionsPronouns === "true") {
                    $('#switchConjunctionsPrepositionsPronouns').trigger('click')
                }

                if (history.switchMyListWords === "true") {
                    $('#switchMyListWords').trigger('click')
                }
            },
        });
    });
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

function isValidate(min, max, target, settings) {
    if (settings.nTable.id === 'history_table') {
        return (isNaN(min) && isNaN(max)) ||
            (isNaN(min) && target <= max) ||
            (min <= target && isNaN(max)) ||
            (min <= target && target <= max);
    } else {
        return true;
    }
}

function isIncludes(target, search, settings) {
    if (settings.nTable.id === 'history_table') {
        if (search.length > 0) {
            return target.includes(search)
        } else {
            return true;
        }
    } else {
        return true;
    }
}

function isDateValid(target, settings) {
    if (settings.nTable.id === 'history_table') {
        let date = new Date(target)
        let dateMin = new Date($('#dateMin').val() + ' 00:00:00')
        let dateMax = new Date($('#dateMax').val() + ' 23:59:59')
        if (date >= dateMin && date <= dateMax) {
            return true;
        }
    } else {
        return true;
    }
}

$(document).ready(function () {
    getHistoryInfo()
    $('#changeAllState').on('change', function () {
        let state = $(this).is(':checked')
        $.each($('.custom-control-input.switch'), function () {
            if (state !== $(this).is(':checked')) {
                $(this).trigger('click');
            }
        });
    });

    setInterval(() => {
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
                success: function (response) {
                    $('#changeAllState').prop('checked', false);
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
                            "<td>" + val.last_check + "</td>" +
                            "<td>" +
                            "   <textarea style='height: 160px;' data-target='" + val.id + "' class='history-comment form form-control' >" + val.comment + "</textarea>" +
                            "</td>" +
                            "<td>" + phrase + "</td>" +
                            "<td>" + getRegionName(val.region) + "</td>" +
                            "<td>" + val.main_link + "</td>" +
                            "<td>" + position + "</td>" +
                            "<td>" + val.points + "</td>" +
                            "<td>" + val.coverage + "</td>" +
                            "<td>" + val.coverage_tf + "</td>" +
                            "<td>" + val.width + "</td>" +
                            "<td>" + val.density + "</td>" +
                            "<td>" +
                            "   <div class='d-flex justify-content-center'> " +
                            "       <div class='__helper-link ui_tooltip_w'> " +
                            "           <div class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'>" +
                            "               <input onclick='changeState($(this))' type='checkbox' class='custom-control-input switch' id='calculate-project-" + val.id + "' name='noIndex' data-target='" + val.id + "' " + checked + ">" +
                            "               <label class='custom-control-label' for='calculate-project-" + val.id + "'></label>" +
                            "           </div>" +
                            "       </div>" +
                            "   </div>" +
                            "</td>" +
                            "<td id='history-state-" + val.id + "'>" +
                            state +
                            "</td>" +
                            "</tr>"
                        )
                    })

                    $(document).ready(function () {
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

                        getHistoryInfo()

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
                                    ignoredDomains: $(".form-control.ignoredDomains").html(),
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

                        //------------------------ CUSTOM FILTERS -----------------------
                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var projectComment = String($('#projectComment').val()).toLowerCase();
                            var target = String(data[1]).toLowerCase();
                            return isIncludes(target, projectComment, settings)
                        });
                        $('#projectComment').keyup(function () {
                            historyTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var phraseSearch = String($('#phraseSearch').val()).toLowerCase();
                            var target = String(data[2]).toLowerCase();
                            return isIncludes(target, phraseSearch, settings)
                        });
                        $('#phraseSearch').keyup(function () {
                            historyTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var regionSearch = String($('#regionSearch').val()).toLowerCase();
                            var target = String(data[3]).toLowerCase();
                            return isIncludes(target, regionSearch, settings)
                        });
                        $('#regionSearch').keyup(function () {
                            historyTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var mainPageSearch = String($('#mainPageSearch').val()).toLowerCase();
                            var target = String(data[4]).toLowerCase();
                            return isIncludes(target, mainPageSearch, settings)
                        });
                        $('#mainPageSearch').keyup(function () {
                            historyTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var maxPosition = parseFloat($('#maxPosition').val());
                            var minPosition = parseFloat($('#minPosition').val());
                            var target = parseFloat(data[5]);
                            return isValidate(minPosition, maxPosition, target, settings)
                        });
                        $('#minPosition, #maxPosition').keyup(function () {
                            historyTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var maxPoints = parseFloat($('#maxPoints').val());
                            var minPoints = parseFloat($('#minPoints').val());
                            var target = parseFloat(data[6]);
                            return isValidate(minPoints, maxPoints, target, settings)
                        });
                        $('#minPoints, #maxPoints').keyup(function () {
                            historyTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var maxCoverage = parseFloat($('#maxCoverage').val());
                            var minCoverage = parseFloat($('#minCoverage').val());
                            var target = parseFloat(data[7]);
                            return isValidate(minCoverage, maxCoverage, target, settings)
                        });
                        $('#minCoverage, #maxCoverage').keyup(function () {
                            historyTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var maxCoverageTf = parseFloat($('#maxCoverageTf').val());
                            var minCoverageTf = parseFloat($('#minCoverageTf').val());
                            var target = parseFloat(data[8]);
                            return isValidate(minCoverageTf, maxCoverageTf, target, settings)
                        });
                        $('#minCoverageTf, #maxCoverageTf').keyup(function () {
                            historyTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var maxWidth = parseFloat($('#maxWidth').val());
                            var minWidth = parseFloat($('#minWidth').val());
                            var target = parseFloat(data[9]);
                            return isValidate(minWidth, maxWidth, target, settings)
                        });
                        $('#minWidth, #maxWidth').keyup(function () {
                            historyTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var maxDensity = parseFloat($('#maxDensity').val());
                            var minDensity = parseFloat($('#minDensity').val());
                            var target = parseFloat(data[10]);
                            return isValidate(minDensity, maxDensity, target, settings)
                        });
                        $('#minDensity, #maxDensity').keyup(function () {
                            historyTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function (settings, data) {
                            var target = String(data[0]);
                            return isDateValid(target, settings)
                        });
                        $('#dateMin').change(function () {
                            historyTable.draw();
                        });
                        $('#dateMax').change(function () {
                            historyTable.draw();
                        });
                    });
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
                data: {
                    historyId: $(this).attr('data-order'),
                },
                success: function (response) {
                    $('#history-list-subject').show()
                    $('#list-history').show()
                    $.each(response.object, function (key, value) {
                        let children = ''
                        $.each(value, function (childKey, child) {
                            children +=
                                '<tr>' +
                                '    <td>' + child['phrase'] + '</td>' +
                                '    <td>' + getRegionName(child['region']) + '</td>' +
                                '    <td>' + child['main_link'] + '</td>' +
                                '    <td>' + child['position'] + '</td>' +
                                '    <td>' + child['points'] + '</td>' +
                                '    <td>' + child['coverage'] + '</td>' +
                                '    <td>' + child['coverage_tf'] + '</td>' +
                                '    <td>' + child['width'] + '</td>' +
                                '    <td>' + child['density'] + '</td>' +
                                '    <td>' + child['calculate'] + '</td>' +
                                '    <td id="history-state-' + child['id'] + '" class="d-flex flex-column">' +
                                '        <button type="button" class="btn btn-secondary get-history-info"' +
                                '                data-order="' + child['id'] + '" data-toggle="modal"' +
                                '                data-target="#staticBackdrop"> Повторить анализ' +
                                '        </button>' +
                                '        <a href="/show-history/' + child['id'] + '"' +
                                '           target="_blank"' +
                                '           class="btn btn-secondary mt-3">' +
                                '            Подробная информация' +
                                '        </a>' +
                                '    </td>' +
                                '</tr>'
                        })
                        $('#list-history-body').append(
                            '<tr class="render-list-history">' +
                            '<td>' + key +
                            '    <i class="fa fa-plus show-stories" data-target="' + key + '"' +
                            '       style="cursor:pointer;"></i>' +
                            '</td>' +
                            '<td>' + getRegionName(value[0]['region']) + '</td>' +
                            '<td>' + value[0]['main_link'] + '</td>' +
                            '<td>' + value[0]['position'] + '</td>' +
                            '<td>' + value[0]['points'] + '</td>' +
                            '<td>' + value[0]['coverage'] + '</td>' +
                            '<td>' + value[0]['coverage_tf'] + '</td>' +
                            '<td>' + value[0]['width'] + '</td>' +
                            '<td>' + value[0]['density'] + '</td>' +
                            '</tr>' +
                            '<tr class="render-list-history">' +
                            '   <td colspan="10" style="display: none" data-order="' + key + '">' +
                            '       <div class="row w-100">' +
                            '           <table' +
                            '               class="table table-bordered table-striped dataTable dtr-inline list-children"' +
                            '               id="list-history-' + value[0]['id'] + '">' +
                            '               <thead>' +
                            '               <tr>' +
                            '                   <th class="col-2">Фраза</th>' +
                            '                   <th class="col-1">Регион</th>' +
                            '                   <th class="col-2">Посадочная</th>' +
                            '                   <th class="col-1">Позиция в топе</th>' +
                            '                   <th class="col-1">Баллы</th>' +
                            '                   <th class="col-1">Охват важных слов</th>' +
                            '                   <th class="col-1">Охват TF</th>' +
                            '                   <th class="col-1">Ширина</th>' +
                            '                   <th class="col-1">Плотность</th>' +
                            '                   <th>Учитывать в расчёте общего балла</th>' +
                            '                   <th class="col-1" colspan="1" rowspan="1"></th>' +
                            '               </tr>' +
                            '               </thead>' +
                            '               <tbody>' +
                            children +
                            '               </tbody>' +
                            '           </table>' +
                            '       </div>' +
                            '   </td>' +
                            '</tr>'
                        )
                    })

                    $(document).ready(function () {
                        $('.list-children').DataTable()
                        $('.dataTables_wrapper.no-footer').css({
                            width: '100%'
                        })
                        scrollTo('#history-list-subject')
                    })
                }
            });
        })
    }, 500)
})


function scrollTo(elemPath) {
    $('html, body').animate({
        scrollTop: $(elemPath).offset().top
    }, {
        duration: 370,
        easing: "linear"
    });
}

function hideListHistory() {
    $('#history-list-subject').hide()
    $('#list-history').hide()
    $('.render-list-history').remove()
    $('.list-children').dataTable().fnDestroy();
}

function hideTableHistory() {
    $("#history_table").dataTable().fnDestroy();
    $('.render').remove()
    $('.history').hide()
}
