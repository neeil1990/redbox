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

$('#changeAllState').on('change', function () {
    let state = $(this).is(':checked')
    $.each($('.custom-control-input.switch'), function () {
        if (state !== $(this).is(':checked')) {
            $(this).trigger('click');
        }
    });
});

$('.project_name').click(function () {
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
                        // "<button data-order='" + val.id + "' class='btn btn-secondary mt-3 relevance-repeat-scan'>Повторить анализ</button>"
                        +
                        "<a href='/show-details-history/" + val.id + "' target='_blank' class='btn btn-link'> Подробная информация</a>"

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

                tbody.append(
                    "<tr class='render'>" +
                    "<td>" + val.last_check + "</td>" +
                    "<td>" +
                    "   <textarea style='height: 160px;' data-target='" + val.id + "' class='history-comment form form-control' >" + val.comment + "</textarea>" +
                    "</td>" +
                    "<td>" + val.phrase + "</td>" +
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
                let table = $('#history_table').DataTable({
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

                let href = '#history_table';
                $('html, body').animate({
                    scrollTop: $(href).offset().top
                }, {
                    duration: 370,
                    easing: "linear"
                });

                $('.history-comment').change(function () {
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

                $('.get-history-info').click(function () {
                    let id = $(this).attr('data-order')
                    $.ajax({
                        type: "get",
                        dataType: "json",
                        url: "/get-history-info/" + id,
                        success: function (response) {
                            let history = response.history
                            $('#hiddenId').val(id)
                            $('.form-control.link').val(history.link)
                            $('.form-control.phrase').val(history.phrase)
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

                $('#relevance-repeat-scan').click(function () {
                    let id = $('#hiddenId').val()
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "/repeat-scan",
                        data: {
                            id: id,
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

                function isValidate(min, max, target, settings) {
                    // if (settings.nTable.id !== 'history_table') {
                    //     return true;
                    // }
                    return (isNaN(min) && isNaN(max)) ||
                        (isNaN(min) && target <= max) ||
                        (min <= target && isNaN(max)) ||
                        (min <= target && target <= max);
                }

                function isIncludes(target, search) {
                    if (search.length > 0) {
                        return target.includes(search)
                    } else {
                        return true;
                    }
                }

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var projectComment = String($('#projectComment').val()).toLowerCase();
                    var target = String(data[1]).toLowerCase();
                    return isIncludes(target, projectComment)
                });
                $('#projectComment').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var phraseSearch = String($('#phraseSearch').val()).toLowerCase();
                    var target = String(data[2]).toLowerCase();
                    return isIncludes(target, phraseSearch)
                });
                $('#phraseSearch').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var regionSearch = String($('#regionSearch').val()).toLowerCase();
                    var target = String(data[3]).toLowerCase();
                    return isIncludes(target, regionSearch)
                });
                $('#regionSearch').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var mainPageSearch = String($('#mainPageSearch').val()).toLowerCase();
                    var target = String(data[4]).toLowerCase();
                    return isIncludes(target, mainPageSearch)
                });
                $('#mainPageSearch').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxPosition = parseFloat($('#maxPosition').val());
                    var minPosition = parseFloat($('#minPosition').val());
                    var target = parseFloat(data[5]);
                    return isValidate(minPosition, maxPosition, target, settings)
                });
                $('#minPosition, #maxPosition').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxPoints = parseFloat($('#maxPoints').val());
                    var minPoints = parseFloat($('#minPoints').val());
                    var target = parseFloat(data[6]);
                    return isValidate(minPoints, maxPoints, target, settings)
                });
                $('#minPoints, #maxPoints').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxCoverage = parseFloat($('#maxCoverage').val());
                    var minCoverage = parseFloat($('#minCoverage').val());
                    var target = parseFloat(data[7]);
                    return isValidate(minCoverage, maxCoverage, target, settings)
                });
                $('#minCoverage, #maxCoverage').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxCoverageTf = parseFloat($('#maxCoverageTf').val());
                    var minCoverageTf = parseFloat($('#minCoverageTf').val());
                    var target = parseFloat(data[8]);
                    return isValidate(minCoverageTf, maxCoverageTf, target, settings)
                });
                $('#minCoverageTf, #maxCoverageTf').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxWidth = parseFloat($('#maxWidth').val());
                    var minWidth = parseFloat($('#minWidth').val());
                    var target = parseFloat(data[9]);
                    return isValidate(minWidth, maxWidth, target, settings)
                });
                $('#minWidth, #maxWidth').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxDensity = parseFloat($('#maxDensity').val());
                    var minDensity = parseFloat($('#minDensity').val());
                    var target = parseFloat(data[10]);
                    return isValidate(minDensity, maxDensity, target, settings)
                });
                $('#minDensity, #maxDensity').keyup(function () {
                    table.draw();
                });

                function isDateValid(target) {
                    let date = new Date(target)
                    let dateMin = new Date($('#dateMin').val() + ' 00:00:00')
                    let dateMax = new Date($('#dateMax').val() + ' 23:59:59')
                    if (date >= dateMin && date <= dateMax) {
                        return true;
                    }
                }

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var target = String(data[0]);
                    return isDateValid(target)
                });
                $('#dateMin').change(function () {
                    table.draw();
                });
                $('#dateMax').change(function () {
                    table.draw();
                });
            });
        },
    });
});
