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

function renderStubs(tasks, target) {
    let html = ''

    $.each(tasks, function (index, task) {
        let button = '<button class="btn btn-sm btn-default" data-toggle="collapse" href="#collapse-example-' + index + '" aria-expanded="false" aria-controls="collapse-example-' + index + '" id="heading-example' + index + '"><i class="fa fa-eye"></i></button>'
        let stubType = ''
        if (task.type === 'personal') {
            stubType = '(личный шаблон)'
            button += '<button class="btn btn-sm btn-default remove-stub" data-id="' + task.id + '"><i class="fa fa-trash"></i></button>'
        } else {
            stubType = '(базовый шаблон)'
        }

        html += '<ol class="card pl-0" data-id="' + index + '">' +
            '    <p class="card-header">' +
            '        <span class="d-flex justify-content-between">' +
            '            <span>' + task.name + '</span>' +
            '            <span>' + stubType + '</span>' +
            '            <span>' + button + '</span>' +
            '        </span>' +
            '    </p>' +
            '    <div id="collapse-example-' + index + '" aria-labelledby="heading-example" class="collapse" style="">' +
            '    <div class="accordion stubs card-body" data-id="' + index + '">'
        html += generateNestedStubs(JSON.parse(task.tree), true)
        html += '</div>' + '</div>' + '</ol>'
    });

    $(target).html(html)
}

$(document).on('click', '#stubs-place > ol', function (e) {
    if (!$(e.target).hasClass('btn-default')) {
        $('.ribbon-wrapper.ribbon-lg').remove();

        $(this).append(
            '<div class="ribbon-wrapper ribbon-lg">' +
            '    <div class="ribbon bg-primary">' +
            '        Выбрано' +
            '    </div>' +
            '</div>'
        );
    }
});
