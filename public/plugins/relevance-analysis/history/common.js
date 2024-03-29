$(document).ready(function () {
    $('.remove-empty-results').on('click', function () {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/remove-scan-results",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: $(this).attr('data-target'),
            },
            success: function (response) {
                if (response.code === 200) {
                    getSuccessMessage(response.message)
                    setValues(response)
                } else if (response.code === 415) {
                    getErrorMessage(response.message)
                }
            },
        });
    })

    $('.remove-with-filters').on('click', function () {
        let id = $(this).attr('data-target');

        if ($('#comment-filter-' + id).val() === '' &&
            $('#phrase-filter-' + id).val() === '' &&
            $('#region-filter-' + id).val() === 'none' &&
            $('#link-filter-' + id).val() === '' &&
            $('#date-filter-before-' + id).val() === '' &&
            $('#date-filter-after-' + id).val() === '' &&
            $('#position-filter-after-' + id).val() === '' &&
            $('#position-filter-before-' + id).val() === ''
        ) {
            if (!confirm('У вас будут удалены ВСЕ результаты проекта.')) {
                getSuccessMessage('Удаление было отменено')
                return;
            }
        }

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/remove-scan-results-with-filters",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id,
                comment: $('#comment-filter-' + id).val(),
                phrase: $('#phrase-filter-' + id).val(),
                region: $('#region-filter-' + id).val(),
                link: $('#link-filter-' + id).val(),
                before: $('#date-filter-before-' + id).val(),
                after: $('#date-filter-after-' + id).val(),
                positionAfter: $('#position-filter-after-' + id).val(),
                positionBefore: $('#position-filter-before-' + id).val()
            },
            success: function (response) {
                if (response.code === 200) {
                    getSuccessMessage(response.message)
                    window.location.reload();
                } else if (response.code === 415) {
                    getErrorMessage(response.message)
                }
            },
        });
    })

    function getSuccessMessage(message, time = 3000) {
        $('.toast-top-right.success-message').show(300)
        $('#message-info').html(message)
        setTimeout(() => {
            $('.toast-top-right.success-message').hide(300)
        }, time)
    }

    function getErrorMessage(message, time = 3000) {
        $('.toast-top-right.error-message').show(300)
        $('#message-error-info').html(message)
        setTimeout(() => {
            $('.toast-top-right.error-message').hide(300)
        }, time)
    }

    function setValues(response) {
        $('.count-sites-' + response.objectId).html(response.countSites)
        $('.total-points-' + response.objectId).html(response.points)
        $('.count-checks-' + response.objectId).html(response.countChecks)
        $('.total-positions-' + response.objectId).html(response.avgPosition)
        if (response.removed == 1) {
            $('#story-id-' + response.objectId).remove()
        } else {
            $('a[data-order="' + response.objectId + '"]').trigger('click')
        }
    }
});
