define([
    'jquery',
    'keywordGenerator/functions',
    'keywordGenerator/selector'
], function (
    $,
    Functions,
    select2Init
) {
    return {
        keywordGeneratorStart: function ($container, basePath) {
            var stopWords;
            $.ajax({
                url: basePath + '/data/stop_words.txt',
                success: function (data) {
                    stopWords = data;
                    Functions.render($container);
                    main($container, stopWords);
                }
            });
        }
    };
    function main($container, stopWords) {
        initializePage($container, stopWords);
    }

    /**
     *
     * @param $container {jQuery}
     * @param stopWords {String}
     */
    function initializePage($container, stopWords) {
        var elements = Functions.initialization($container, stopWords);
        Functions.updateAllDynamicElements(elements);
        $('.addList', $container).click(function () {
                Functions.addList(elements);
                Functions.updateAllDynamicElements(elements);
            }
        );

        $('.get', $container).click(function () {
            Functions.updateLists(elements);
            function resultOutput() {
                Functions.closePopups();
                Functions.showMessage(
                    $('#Phrases-are-being-generated').val(),
                    $('#This-may-take-some-time').val()
                );
                setTimeout(function () {
                    Functions.closePopups();
                    Functions.showPopup(elements, elements.$popup.html());
                }, 500);
            }

            if (!Functions.checkForOverload(elements, resultOutput)) {
                return;
            }

            resultOutput();
        });

        $('.make-changes', $container).click(function () {
            Functions.closePopups();
        });

        var $fromWords = $('.from-words', $container);
        var $toWords = $('.to-words', $container);

        select2Init($fromWords);
        select2Init($toWords);
        select2Init($('.left-right', $container));

        $fromWords.on('change', function () {
            var fromVal = $fromWords.val();
            var toVal = $toWords.val();
            $toWords.html('');
            for (var i = fromVal; i <= 7; i++) {
                $toWords.append('<option value=\'' + i + '\'>' + i + '</option>');
            }
            $toWords.val(toVal).trigger('change.select2');
        });

        $toWords.on('change', function () {
            var fromVal = $fromWords.val();
            var toVal = $toWords.val();
            $fromWords.html('');
            for (var i = 1; i <= toVal; i++) {
                $fromWords.append('<option value=\'' + i + '\'>' + i + '</option>');
            }
            $fromWords.val(fromVal).trigger('change.select2');
        });

        $('.globalCheckboxOption', $container).click(function () {
            Functions.updateAllDynamicElements(elements);
        });

        $('.additionalGlobalOptions', $container).click(function (e) {
            e.preventDefault();
            $('.globalOptions', $container).slideToggle();
        });

        $('.help-arrow', $container).click(function () {
            $('.help-content', $container).slideToggle();
        });

        $('.arrow', $container).click(function (e) {
            e.preventDefault();
            Functions.turnArrow($(this));
        });

    }
});
