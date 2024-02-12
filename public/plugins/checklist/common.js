var notificationBlocks = 0

function errorMessage(errors, timeout = 5000) {
    let messages = ''
    $.each(errors, function (k, v) {
        messages += v + "<br>"
    })

    let margin = notificationBlocks * 150
    notificationBlocks++

    let $block =
        $(
            '<div id="toast-container" class="toast-top-right error-message" style="display:none; top: ' + margin + 'px">' +
            '    <div class="toast toast-error" aria-live="polite">' +
            '        <div class="toast-message" id="toast-error-message">' + messages + '</div>' +
            '    </div>' +
            '</div>'
        )

    $('#block-from-notifications').append($block)

    $block.show(300)

    setTimeout(() => {
        $block.remove()
        notificationBlocks--
    }, timeout)
}

function successMessage(message) {
    let margin = notificationBlocks * 70
    notificationBlocks++

    let $block =
        $('<div id="toast-container" class="toast-top-right success-message" style="display: none; top: ' + margin + 'px">' +
            '    <div class="toast toast-success" aria-live="polite">' +
            '        <div class="toast-message" id="toast-success-message">' + message + '</div>' +
            '    </div>' +
            '</div>')

    $('#block-from-notifications').append($block)

    $block.show(300)

    setTimeout(() => {
        $block.remove()
        notificationBlocks--
    }, 5000)
}

function getRandomInt(max) {
    return Math.floor(Math.random() * max);
}

function parseTree($object) {
    let $dataId = $object.attr('data-id')
    let objects = []
    let $subtasks = []

    let object = {
        name: $('input[data-target="' + $dataId + '"][data-type="name"]').val(),
        status: $('select[data-target="' + $dataId + '"][data-type="status"]').val(),
        description: $('.pre-description[data-id="' + $dataId + '"]').val(),
        deadline: $('input[data-type="deadline"][data-target="' + $dataId + '"]').val(),
        start: $('input[data-type="start"][data-target="' + $dataId + '"]').val(),
        count_days: $('.datetime-counter[data-target="' + $dataId + '"]').val(),
        active_after: $('input[data-type="active_after"][data-target="' + $dataId + '"]').val(),
        repeat_after: $('input[data-type="repeat_after"][data-target="' + $dataId + '"]').val(),
        weekends: $('select[data-type="weekends"][data-target="' + $dataId + '"]').val(),
    }

    if ($('#subtasks-' + $dataId).children('li').length > 0) {
        $.each($('#subtasks-' + $dataId).children('li'), function () {
            $subtasks.push(parseTree($(this)))
        })
    }

    object.subtasks = $subtasks
    objects.push(object)

    return objects
}

$(document).on('click', '.remove-stub', function () {
    let ID = $(this).attr('data-id')
    let $parent = $(this).parent().parent().parent().parent()

    if (confirm('Вы действительно хотите удалить шаблон?')) {
        $.ajax({
            type: 'get',
            url: '/remove-checklist-stub/' + ID,
            success: function (message) {
                successMessage(message)
                $parent.remove()
            },
            error: function (response) {
                errorMessage(response.responseJSON.errors)
            }
        })
    }
})

$(document).on('click', '.remove-stub-card', function () {
    let ID = $(this).attr('data-id')
    let $parent = $(this).parent().parent().parent()

    if (confirm('Вы действительно хотите удалить шаблон?')) {
        $.ajax({
            type: 'get',
            url: '/remove-checklist-stub/' + ID,
            success: function (message) {
                successMessage(message)
                $parent.remove()
            },
            error: function (response) {
                errorMessage(response.responseJSON.errors)
            }
        })
    }
})
