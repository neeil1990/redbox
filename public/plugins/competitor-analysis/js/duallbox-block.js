function duallboxBlockRender(metaTags) {
    getPhrasesDuallbox(metaTags)

    $('#getRecommendations').unbind().on('click', function () {
        $('.recommendations-render').remove()
        $('#recommendations-table').dataTable().fnDestroy()
        let selectedPhrases = $('#bootstrap-duallistbox-selected-list_duallistbox_phrases option').toArray().map(item => item.value);
        let selectedTags = $('#bootstrap-duallistbox-selected-list_duallistbox_tags option').toArray().map(item => item.value);

        let newHead = '<tr class="recommendations-render render"><th>Фраза</th>'
        $.each(selectedTags, function (key1, tag) {
            newHead += '<th>' + tag + '</th>'
        })
        newHead += '</tr>'

        $('#recommendations-head').append(newHead)

        $.each(selectedPhrases, function (key, phrase) {
            let newRow = '<tr class="recommendations-render render">' +
                '<td>' + phrase + '</td>'
            $.each(selectedTags, function (key1, tag) {
                newRow += '<td>'
                let words = []
                $.each(metaTags[phrase][tag], function (word, count) {
                    if (count >= 3 && word !== "") {
                        words.push(word)
                    }
                })

                newRow += words.join("\n") + '</td>'
            })
            $('#recommendations-tbody').append(newRow + "</tr>")
        })

        $('#recommendations-block').show()


        $('#recommendations-table').dataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "searching": true,
            dom: 'lBfrtip',
            buttons: [
                'copy', 'csv', 'excel'
            ]
        })

        $('#recommendations-table_length').attr('class', 'pl-2')
        $('.dt-button').attr('btn btn-secondary')
    })
}

function removePhrasesDuallbox() {
    $('#dualbox-phrases-block').html('')
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

