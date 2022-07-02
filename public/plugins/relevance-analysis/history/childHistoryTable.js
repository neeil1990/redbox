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
                    $("#history_table").dataTable().fnDestroy();
                    $('.render').remove()
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

                        $('html, body').animate({
                            scrollTop: $('#tab_1 > div.history > h3').offset().top
                        }, {
                            duration: 370,
                            easing: "linear"
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
    }, 500)
})
