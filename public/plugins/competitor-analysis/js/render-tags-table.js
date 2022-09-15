function renderTagsTable(metaTags) {
    $('.tag-analysis').show()

    $.each(metaTags, function (phrase, tags) {
        let row = '<tr class="render">'
        row += '<td>' + phrase + '</td>'

        $.each(tags, function (meta, values) {
            row += '<td><div style="height: 260px; overflow-x: auto;">'
            let metas = ''
            $.each(values, function (word, count) {
                if (word != undefined && word != "" && word != 'undefined') {
                    metas += '<span>' + word + ': ' + count + '</span> <br>'
                }
            })
            row += metas + '</div></td>'

        })

        row += '</tr>'

        $('#tag-analysis-tbody').append(row)
    })
}
