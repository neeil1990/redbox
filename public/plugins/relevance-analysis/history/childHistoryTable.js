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
                tbody.append(
                    "<tr class='render'>" +
                    "<td class='col-1'>" + val.last_check + "</td>" +
                    "<td class='col-1'>" + val.phrase + "</td>" +
                    "<td class='col-1'>" + val.region + "</td>" +
                    "<td class='col-1'>" + val.main_link + "</td>" +
                    "<td class='col-1'>" + val.position + "</td>" +
                    "<td class='col-1'>" + val.points + "</td>" +
                    "<td class='col-1'>" + val.coverage + "</td>" +
                    "<td class='col-1'>" + val.coverage_tf + "</td>" +
                    "<td class='col-1'>" + val.width + "</td>" +
                    "<td class='col-1'>" + val.density + "</td>" +
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
                    "<td>" +
                    "<a href='/show-details-history/" + val.id + "' target='_blank' class='btn btn-secondary'> Подробная информация</a>" +
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

                function isValidate(min, max, target, settings) {
                    if (settings.nTable.id !== 'history_table') {
                        return true;
                    }
                    return (isNaN(min) && isNaN(max)) ||
                        (isNaN(min) && target <= max) ||
                        (min <= target && isNaN(max)) ||
                        (min <= target && target <= max);
                }

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxPosition = parseFloat($('#maxPosition').val());
                    var minPosition = parseFloat($('#minPosition').val());
                    var target = parseFloat(data[4]);
                    return isValidate(minPosition, maxPosition, target, settings)
                });
                $('#minPosition, #maxPosition').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxPoints = parseFloat($('#maxPoints').val());
                    var minPoints = parseFloat($('#minPoints').val());
                    var target = parseFloat(data[5]);
                    return isValidate(minPoints, maxPoints, target, settings)
                });
                $('#minPoints, #maxPoints').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxCoverage = parseFloat($('#maxCoverage').val());
                    var minCoverage = parseFloat($('#minCoverage').val());
                    var target = parseFloat(data[6]);
                    return isValidate(minCoverage, maxCoverage, target, settings)
                });
                $('#minCoverage, #maxCoverage').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxCoverageTf = parseFloat($('#maxCoverageTf').val());
                    var minCoverageTf = parseFloat($('#minCoverageTf').val());
                    var target = parseFloat(data[7]);
                    return isValidate(minCoverageTf, maxCoverageTf, target, settings)
                });
                $('#minCoverageTf, #maxCoverageTf').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxWidth = parseFloat($('#maxWidth').val());
                    var minWidth = parseFloat($('#minWidth').val());
                    var target = parseFloat(data[8]);
                    return isValidate(minWidth, maxWidth, target, settings)
                });
                $('#minWidth, #maxWidth').keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var maxDensity = parseFloat($('#maxDensity').val());
                    var minDensity = parseFloat($('#minDensity').val());
                    var target = parseFloat(data[9]);
                    return isValidate(minDensity, maxDensity, target, settings)
                });
                $('#minDensity, #maxDensity').keyup(function () {
                    table.draw();
                });
            });
        },
    });
});


