function duallboxBlockRender(metaTags, count) {
    getPhrasesDuallbox(metaTags)

    $('#getRecommendations').unbind().on('click', function () {
        if ($.fn.DataTable.fnIsDataTable($('#recommendations-table'))) {
            $('#recommendations-table').dataTable().fnDestroy()
        }
        $('.recommendations-render').remove()

        let selectedPhrases = $('#bootstrap-duallistbox-selected-list_duallistbox_phrases option').toArray().map(item => item.value);
        let selectedTags = $('#bootstrap-duallistbox-selected-list_duallistbox_tags option').toArray().map(item => item.value);

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/get-recommendations",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                selectedPhrases: selectedPhrases,
                selectedTags: selectedTags,
                count: count,
                metaTags: metaTags
            },
            success: function (response) {
                // let newHead = '<tr class="recommendations-render render">'
                // $.each(selectedTags, function (key1, tag) {
                //     newHead += '<th>' + tag + '</th>'
                // })
                // newHead += '</tr>'

                let newRow = "<tr class='recommendations-render render'>"
                let newHead = '<tr class="recommendations-render render">'
                $.each(response.result, function (tag, values) {
                    newHead += '<th>' + tag + '</th>'
                    newRow += '<td>'
                    $.each(values, function (word, count) {
                        newRow += word + ": " + count + "\n"
                    })
                    newRow += "</td>"
                })
                newHead += '</tr>'
                $('#recommendations-head').append(newHead)
                $('#recommendations-body').append(newRow + "</tr>")

                $('#recommendations-block').show()

                $(document).ready(function () {
                    $('#recommendations-table').dataTable({
                        "order": [[0, "desc"]],
                        "pageLength": 10,
                        "searching": true,
                        dom: 'lBfrtip',
                        buttons: [
                            'copy', 'csv', 'excel'
                        ]
                    })

                    $('#recommendations-table_length').css('margin-right', '5px')
                    $('#recommendations-table_wrapper > div.dt-buttons').css('display', 'inline')
                    $('#recommendations-table_wrapper > div.dt-buttons').css('margin-left', '20px')
                    $('.dt-button').attr('class', 'btn btn-secondary')
                })
            },
            error: function () {
                $('#recommendations-block').hide()
            }
        });
    })
}

function getPhrasesDuallbox(metaTags) {
    let select =
        '<h3>Выберите фразы</h3>' +
        '    <select multiple="multiple" size="10" name="duallistbox_phrases" id="duallistbox_phrases">'

    $.each(metaTags, function (phrase, tags) {
        select += '<option value="' + phrase + '">' + phrase + '</option>'
    })

    select += '</select>'

    $('#dualbox-phrases-block').append(select)

    $('#duallistbox_phrases').bootstrapDualListbox();
}

