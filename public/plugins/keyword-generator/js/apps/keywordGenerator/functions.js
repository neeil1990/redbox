define([
    'jquery',
    'keywordGenerator/List',
    'clipboard',
    'tpl!templates/list',
    'jquery.qtip'
],
    function (
    $,
    List,
    Clipboard,
    list
    ) {
    'use strict';

    return {
        turnArrow: turnArrow,
        handleIndividualOptionsCheckboxes: handleIndividualOptionsCheckboxes,
        /**
         *
         * @param $container {jQuery}
         */
        render: function ($container) {
            var $list = $($.parseHTML(list()));
            var $lists = $();
            for (var i = 0; i < 2; i++) {
                $lists = $lists.add($list.clone(true, true));
            }
            $('.listContainer', $container).prepend($lists);
            $('.list-img-container', $container).last().hide();
            $('.list', $container).each(function () {
                List.List.number += 1;
                $(this).find('.listName').val((List.List.number));
            });

        },
        saveText: saveText,
        /**
         *
         * @param elements {Object}
         */
        addList: function (elements) {
            var maxCount = 5;
            var lists = $('.list', elements.$container).toArray();
            if (lists.length >= maxCount) {
                showMessage('Внимание!', 'Максимальное число списков: 5');

                return;
            }
            $(list()).insertAfter($('.list-w-img-container', elements.$container).last());
            $('.list-img-container', elements.$container).show();
            $('.list-img-container', elements.$container).last().hide();
            List.List.number++;
            $('.list-w-img-container', elements.$container).last().find('.listName').val('Список ' + List.List.number);
        },
        showPopup: showPopup,
        showMessage: showMessage,
        closePopups: closePopups,
        updateLists: updateLists,
        setCombinationsCount: setCombinationsCount,
        setCombinations: setCombinations,
        initialization: initialization,
        deleteList: deleteList,
        updateFilterResult: updateFilterResult,
        updateAllDynamicElements: updateAllDynamicElements,
        checkForOverload: checkForOverload
    };

    /**
     *
     * @param text {String}
     */
    function saveText(text) {
        text = 'Список получен с помощью инструмента Prime' +
            ' "Комбинатор ключевых фраз": //prime-ltd.su/nashi-servisyi\n\n' + text;
        text = text.replace(new RegExp('\n', 'g'), '\r\n');
        var textFile;
        var data = new Blob([text], {type: 'text/plain'});
        textFile = window.URL.createObjectURL(data);
        var a = document.createElement('a');
        document.body.appendChild(a);
        a.style = 'display: none';
        a.href = textFile;
        a.download = 'Prime Комбинатор ' + getFormattedDate();
        a.click();
    }

    function getFormattedDate() {
        var date = new Date();
        var month = date.getMonth() + 1;
        var str =
             date.getFullYear().toString() +
             pad(month) +
             pad(date.getDate()) +
             '_' + pad(date.getHours()) +
             '-' + pad(date.getMinutes());

        return str;
    }

    function turnArrow($element) {
        if ($element.hasClass('arrowDown')){
            $element.removeClass('arrowDown').addClass('arrowUp');
        }else {
            $element.removeClass('arrowUp').addClass('arrowDown');
        }
    }

    function pad(number) {
        return (number < 10 ? '0' : '') + number;
    }

    /**
     *
     * @param elements {Object}
     * @param content {String}
     */
    function showPopup(elements, content) {
        function onRenderCallback() {
            elements.$combinationCountPopUp = this.$el.find('.generatedCount');
            elements.$result = this.$el.find('.result_word_generator');
            setResultListeners(elements, this.$el);
            setCombinations(elements);
        }

        require(['popup', 'tpl!templates/base-popup'], function (popup, tpl) {
            popup({
                template: tpl,
                templateData: {
                    content: content,
                    customWidth: 800
                },
                onRenderCallback: onRenderCallback
            });
        });

    }

    function setResultListeners(elements, $el) {
        $el.find('.save-result-word-generator').click(function () {
                var text = $('.result_word_generator', $el).val();
                var words = text.split('\n');
                words = words[0] === '' ? [] : words;
                if (typeof(window.ga) === 'function'){
                    window.ga('owox.send', 'event', 'button', 'click', 'kg_save_resulting_words', words.length);
                    window.ga('owox.send', 'event', 'button', 'click', 'kg_save_number_used_lists', elements.lists.length);
                }
                saveText(text);
            }
        );
        var clipboard = new Clipboard('.copy-result-word-generator', {
            target: function (trigger) {
                return $(trigger).closest('.base-popup_content').find('.result_word_generator')[0];
            }
        });
        $el.find('.copy-result-word-generator').click(function () {
            var text = $('.result_word_generator', $el).val();
            var words = text.split('\n');
            words = words[0] === '' ? [] : words;
            if (typeof(window.ga) === 'function'){
                window.ga('owox.send', 'event', 'button', 'click', 'kg_copy_resulting_words', words.length);
                window.ga('owox.send', 'event', 'button', 'click', 'kg_copy_number_used_lists', elements.lists.length);
            }
        });

        $el.find('.filter_word').bind('input', function () {
            updateFilterResult(elements, $(this).val());
        });
    }

    function closePopups() {
        $('.base-popup-wrapper').find('.base-popup_close').click();
    }

    /**
     *
     * @param $container {jQuery}
     * @param stopWords {String}
     * @returns {Object}
     */
    function initialization($container, stopWords) {
        var elements = [];
        elements.$container = $container;
        elements.$areas = $('.wordList', $container);
        elements.$result = $('.result_word_generator', $container);
        elements.$popup = $container.find('.popup-result-content').closest('.popup');
        elements.$combinationCountPopUp = $container.find('.generatedCount');
        elements.$combinationCount = $container.find('.combinationsQuantity');
        elements.$selector = $container.find('.lists_filter');
        elements.$filterInput = $container.find('.word_filter');
        elements.$message = $('.popup-message-content', $container).closest('.popup');
        elements.lists = [];
        elements.globalOptionsList = $('.globalOptionsList', $container).find('.globalCheckboxOption');
        elements.globalOptions = [];
        elements.globalOptionsList.each(function () {
            var name = $(this).val();
            elements.globalOptions[name] = $(this).prop('checked');
        });
        List.List.stopWords = stopWords.split('\n');

        return elements;
    }

    /**
     *
     * @param elements {Object}
     */
    function updateAllDynamicElements(elements) {
        updateListsHandlers(elements);
        updateLists(elements);
        handleGlobalOptionsCheckboxes(elements);
        var quantity = List.List.getCombinationsCount(elements.lists, elements.globalOptions);
        setCombinationsCount(quantity, elements);
    }

    function handleIndividualOptionsCheckboxes($element) {
        var options = {};
        var $list = $element.closest('.list');
        $list.find(':checkbox').prop('disabled', false);
        options = getOptions($element);
        if (options.exactMatch) {
            $list.find(':checkbox[value=broadMatchModifier]').prop('disabled', true).prop('checked', false);
        }
        if (options.broadMatchModifier) {
            $list.find(':checkbox[value=exactMatch]').prop('disabled', true).prop('checked', false);
        }
    }

    /**
     *
     * @param elements {Object}
     */
    function handleGlobalOptionsCheckboxes(elements) {
        var $checkboxWhichCanBeDisabled;
        elements.globalOptionsList.each(function () {
            if ($(this).val() === 'addToResult') {
                $checkboxWhichCanBeDisabled = $(this);

                return false;
            }
        });
        if (!(elements.globalOptions.surroundWithQuotes || elements.globalOptions.surroundWithBrackets)) {
            $checkboxWhichCanBeDisabled.prop('disabled', true);
        } else {
            $checkboxWhichCanBeDisabled.prop('disabled', false);
        }
    }

    /**
     *
     * @param elements {Object}
     */
    function setDynamicElementsListeners(elements) {
        elements.$areas.bind('input', function () {
            updateAllDynamicElements(elements);
        });
        $('.deleteThis', elements.$container).click(function () {
            deleteList($(this), elements);

        });
        $('.additionalOptions', elements.$container).click(function (e) {
            e.preventDefault();
            turnArrow($(this));
            $(this).closest('.list').find('.additionalOptionsList').slideToggle();
        });
        $('.additionalCheckboxOption', elements.$container).click(function () {
            updateAllDynamicElements(elements);
            handleIndividualOptionsCheckboxes($(this));
        });
    }

    function unsetOldDynamicElementsListeners(elements) {
        elements.$areas.off('input');
        $('.deleteThis', elements.$container).off('click');
        $('.additionalOptions', elements.$container).off('click');
        $('.additionalCheckboxOption', elements.$container).off('click');
    }

    /**
     *
     * @param elements {Object}
     */
    function updateListsHandlers(elements) {
        elements.$areas = $('.wordList', elements.$container);
        unsetOldDynamicElementsListeners(elements);
        setDynamicElementsListeners(elements);
    }

    /**
     *
     * @param elements {Object}
     */
    function getGlobalOptions(elements) {
        elements.globalOptionsList.each(function () {
            var name = $(this).val();
            elements.globalOptions[name] = $(this).prop('checked');
        });
        if (elements.globalOptions.getAllPhrasesByLength) {
            elements.globalOptions.leftRight =
                $('.left-right', elements.$container).find('option:selected').val();

            elements.globalOptions.getAllPhrasesFrom =
                $('.from-words', elements.$container).find('option:selected').val();

            elements.globalOptions.getAllPhrasesTo =
                $('.to-words', elements.$container).find('option:selected').val();

        }
    }

    /**
     *
     * @param elements {Object}
     */
    function updateLists(elements) {
        elements.lists = [];
        elements.$areas.each(function () {
            var list = new List.List('');
            list.setWords($(this).val());
            list.options = getOptions($(this));
            $(this).closest('.list').find('.wordQuantity').text(list.getWordsCount());
            elements.lists.push(list);
        });
        getGlobalOptions(elements);
    }

    /**
     *
     * @param $area {jQuery}
     * @returns {Boolean[]}
     */
    function getOptions($area) {
        var $list = $area.closest('.list');
        var $options = $list.find('.additionalCheckboxOption');
        var options = {};
        $options.each(function () {
            var option = $(this).val();
            options[option] = $(this).prop('checked');
        });

        return options;
    }

    /**
     *
     * @param count {Number}
     * @param elements {Object}
     */
    function setCombinationsCount(count, elements) {
        elements.$combinationCount.text(count);
    }

    /**
     *
     * @param count {Number}
     * @param elements {Object}
     */
    function setResultCombinationsCount(count, elements) {
        elements.$combinationCountPopUp.text(count);
    }

    /**
     *
     * @param messageText {String}
     * @param messageHeader {String}
     */
    function showMessage(messageHeader, messageText) {
        require(['popup', 'tpl!templates/base-popup'], function (popup, tpl) {
            popup({
                template: tpl,
                templateData: {
                    header: messageHeader,
                    content: '<pre>' + messageText + '</pre>',
                    customWidth: 700
                }
            });
        });
    }

    function showConfirmMessage(messageHeader, messageText, onSubmitCallback, buttonText) {
        require(['popup-confirm'], function (popup) {
            popup(messageText, messageHeader, onSubmitCallback, buttonText);
        });
    }

    /**
     *
     * @param elements {Object}
     * @returns {Boolean}
     */
    function setCombinations(elements) {
        List.List.applyOptionsForAll(elements.lists);
        elements.combinations = List.List.getResult(elements, elements.lists);
        showResult(elements, elements.combinations);

    }

    /**
     *
     * @param elements {Object}
     * @param submitCallback {Function}
     * @returns {boolean}
     */
    function checkForOverload(elements, submitCallback) {
        var count = List.List.getCombinationsCount(elements.lists, elements.globalOptions);
        var MAX_COUNT = 200000;
        if (count > MAX_COUNT) {
            showConfirmMessage(
                'Внимание!',
                'Вы пытаетесь сгенерировать ' + count + ' фраз.\nЭто может занять очень много времени.',
                submitCallback,
                'Все равно продолжить'
            );

            return false;
        }

        return true;
    }

    /**
     *
     * @param elements {Object}
     * @returns {boolean}
     */
    function thereAreTooFewLists(elements) {
        var minCount = 2;
        return $('.list-w-img-container', elements.$container).length <= minCount;
    }

    /**
     *
     * @param $button {jQuery}
     * @param elements {Object }
     */
    function deleteList($button, elements) {
        var $list = $button.closest('.list-w-img-container');

        function remove() {
            $list.remove();
            $('.list-w-img-container', elements.$container).last().find('.list-img-container').hide();
            updateAllDynamicElements(elements);
        }

        if (thereAreTooFewLists(elements)) {
            showMessage(
                'Внимание!',
                'Минимальное число списков: 2'
            );
            return;
        }
        if ($list.find('.wordList').val().trim() !== '') {
            showConfirmMessage(
                'Внимание!',
                'Список, который вы хотите удалить, содержит слова. Это действие нельзя будет отменить.',
                remove,
                'Удалить'
            );
        } else {
            remove();
        }
    }

    /**
     *
     * @param elements {Object}
     * @param combinations {String[]}
     */
    function showResult(elements, combinations) {
        setResultCombinationsCount(combinations.length, elements);
        if (typeof(window.ga) === 'function'){
            window.ga('owox.send', 'event', 'button', 'click', 'kg_get_number_used_lists', elements.lists.length);
            window.ga('owox.send', 'event', 'button', 'click', 'kg_get_resulting_words', combinations.length);
        }
        $(elements.$result).val(combinations.join('\n'));
    }

    /**
     *
     * @param elements {Object}
     * @param substring {String}
     */
    function updateFilterResult(elements, substring) {
        var combinations = elements.combinations;
        combinations = combinations.filter(function (value) {
            return value.includes(substring);
        });

        showResult(elements, combinations);
    }
});
