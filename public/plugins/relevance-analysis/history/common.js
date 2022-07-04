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
                    triggerClick(response)
                } else if (response.code === 415) {
                    getErrorMessage(response.message)
                }
            },
        });
    })

    $('.remove-with-filters').on('click', function () {
        let id = $(this).attr('data-target');
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
            },
            success: function (response) {
                if (response.code === 200) {
                    getSuccessMessage(response.message)
                    triggerClick(response)
                } else if (response.code === 415) {
                    getErrorMessage(response.message)
                }
            },
        });
    })

    function getSuccessMessage(message) {
        $('.toast-top-right.success-message').show(300)
        $('#message-info').html(message)
        setTimeout(() => {
            $('.toast-top-right.success-message').hide(300)
        }, 3000)
    }

    function getErrorMessage(message) {
        $('.toast-top-right.error-message').show(300)
        $('#message-error-info').html(message)
        setTimeout(() => {
            $('.toast-top-right.error-message').hide(300)
        }, 3000)
    }

    function triggerClick(response) {
        $('.count-sites-' + response.objectId).html(response.countSites)
        $('.total-points-' + response.objectId).html(response.points)
        $('.count-checks-' + response.objectId).html(response.countChecks)
        $('.total-positions-' + response.objectId).html(response.avgPosition)
        $('a[data-order="' + response.objectId + '"]').trigger('click')
    }
});
