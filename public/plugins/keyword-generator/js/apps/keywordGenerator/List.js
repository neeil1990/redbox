define([], function () {
    'use strict';
    /**
     *
     * @param name {String}
     * @constructor
     */
    function List(name) {
        this.name = name;
        this.words = [];
        this.options = {};
    }

    List.prototype.getName = function () {
        return this.name;
    };

    List.prototype.setWordsArray = function (array) {
        this.words = List.deleteEmptyRowsInArray(array);
        this.words = List.removeMultipleSpacesInArray(this.words);
    };

    List.prototype.setWords = function (text) {
        this.words = List.deleteEmptyRowsInArray(text.trim().split('\n'));
        this.words = List.removeMultipleSpacesInArray(this.words);
    };

    List.prototype.getWordsArray = function () {
        return this.words;
    };

    List.prototype.getWordsCount = function () {
        var count = this.words.length;
        if (this.options.addBlankWord) {
            count++;
        }

        return count;
    };

    List.prototype.isEmpty = function () {
        return this.words.length === 0;
    };

    List.prototype.addBlankWord = function () {
        this.words.push('');
    };

    List.prototype.addExclamationMark = function () {
        for (var i = 0; i < this.words.length; i++){
            var wordsInLine =  this.words[i].split(' ');
            wordsInLine = wordsInLine.map(function (word) {
                if (word !== '' && !isStopWord(word)) {
                    return '!' + word;
                }
                if (isStopWord(word)) {
                    return '+' + word;
                }

                return word;
            });
            this.words[i] = wordsInLine.join(' ');
        }
    };

    List.prototype.addPluses = function () {
        for (var i = 0; i < this.words.length; i++){
            var wordsInLine = this.words[i].split(' ');
            wordsInLine = wordsInLine.map(function (word) {
                return '+' + word;
            });
            this.words[i] = wordsInLine.join(' ');
        }
    };

    function isStopWord(word) {
        return List.stopWords.indexOf(word) !== -1;
    }
    List.getUniqueElements = function (array) {
        var n = {};
        var r = [];
        for (var i = 0; i < array.length; i++) {
            if (n[array[i]] === undefined) {
                n[array[i]] = true;
                r.push(array[i]);
            }
        }

        return r;
    };

    /**
     *
     * @param array {String[]}
     * @returns {String[]}
     */
    List.removeMultipleSpacesInArray = function (array) {
        var newArray = [];
        for (var i = 0; i < array.length; i++) {
            newArray[i] = List.removeMultipleSpacesInString(array[i]);
        }

        return newArray;
    };

    /**
     *
     * @param string {String}
     * @returns {String}
     */
    List.removeMultipleSpacesInString = function (string) {
        return string.split(/\s+/).join(' ');
    };

    List.prototype.applyOptions = function () {
        if (this.options.exactMatch) {
            this.addExclamationMark();
        }
        if (this.options.broadMatchModifier) {
            this.addPluses();
        }
        if (this.options.addBlankWord) {
            this.addBlankWord();
        }
    };

    List.number = 0;
    List.stopWords = [];
    /**
     *
     * @param words {String[]}
     * @returns {Array}
     */
    List.deleteEmptyRowsInArray = function (words) {
        var arr = words;
        arr = arr.map(function (row) {
            return row.trim();
        });
        arr = arr.filter(function (row) {
            return row !== '';
        });

        return arr;
    };

    /**
     *
     * @param lists {List[]}
     * @returns {String}
     */
    List.getNamesOfEmptyFields = function (lists) {
        return lists.filter(function (list) {
            return list.isEmpty();
        }).map(function (list) {
            return list.getName();
        }).join('\n');
    };

    /**
     *
     * @param lists {List[]}
     * @returns {boolean}
     */
    List.namesAreUnique = function (lists) {
        var bool = true;
        var names = [];
        lists.map(function (list) {
            if (names.indexOf(list.getName()) !== -1) {
                bool = false;
            }
            names.push(list.getName());
        });

        return bool;
    };

    /**
     *
     * @param lists {List[]}
     * @returns {*}
     */
    List.getNames = function (lists) {
        return lists.map(function (list) {
            return list.getName();
        });
    };

    /**
     *
     * @param lists {List[]}
     * @returns {*}
     */
    List.applyOptionsForAll = function (lists) {
        for (var i = 0; i < lists.length; i++) {
            lists[i].applyOptions();
        }

        return lists;
    };

    /**
     *
     * @param n {Number}
     * @param lists {List[]}
     * @returns {String[]}
     */
    List.getCombinations = function (n, lists) {
        var first = lists[n];
        first = first.getWordsArray();
        if (lists[n + 1] === undefined) {

            return first;
        }
        var second = this.getCombinations(n + 1, lists);

        return this.combineTwoLists(first, second);
    };

    /**
     *
     * @param lists {List[]}
     * @param result {List}
     */
    List.addSelectedListsToResult = function (lists, result) {
        for (var i = 0; i < lists.length; i++) {
            if (lists[i].options.addToResult) {
                var list = new List('');
                list.setWords(lists[i].getWordsArray().join('\n'));
                result.words = result.words.concat(list.getWordsArray());
            }
        }
    };

    /**
     *
     * @param lists {List[]}
     */
    List.addPlusesToStopWordsInLists = function (lists) {
        for (var i = 0; i < lists.length; i++) {
            var words = lists[i].getWordsArray();
            for (var j = 0; j < words.length; j++) {
                words[j] = List.addPlusesToStopWordsInString(words[j]);
            }
            lists[i].words = words;
        }
    };

    /**
     *
     * @param string {String}
     * @returns {String}
     */
    List.addPlusesToStopWordsInString = function (string) {
        var wordsArray = string.split(' ');
        for (var i = 0; i < wordsArray.length; i++) {
            if (List.stopWords.indexOf(wordsArray[i]) !== -1) {
                wordsArray[i] = '+' + wordsArray[i];
            }
        }

        return wordsArray.join(' ');
    };

    /**
     *
     * @param elements {Object}
     * @param lists {List[]}
     * @returns {String[]}
     */
    List.getResult = function (elements, lists) {
        var newLists = lists;
        var tempArray = [];
        var result = new List('');

        if (elements.globalOptions.addPlus) {
            List.addPlusesToStopWordsInLists(newLists);
        }

        result.setWordsArray(List.getCombinations(0, newLists));
        if (elements.globalOptions.getAllPhrasesByLength) {
            result.setWordsArray(
                getCombinationsFromTo(
                    result.words,
                    elements.globalOptions.getAllPhrasesFrom,
                    elements.globalOptions.getAllPhrasesTo,
                    elements.globalOptions.leftRight
                )
            );
        }
        result.setWordsArray(List.getUniqueElements(result.getWordsArray()));
        List.addSelectedListsToResult(newLists, result);

        if (elements.globalOptions.surroundWithQuotes) {
            tempArray = tempArray.concat(List.surround(result.words, '"', '"'));
        }

        if (elements.globalOptions.surroundWithBrackets) {
            tempArray = tempArray.concat(List.surround(result.words, '[', ']'));
        }

        if (elements.globalOptions.addToResult && (elements.globalOptions.surroundWithBrackets || elements.globalOptions.surroundWithQuotes)) {
            result.words = tempArray.concat(result.words);
        } else {
            if (!elements.addToResult && (elements.globalOptions.surroundWithBrackets || elements.globalOptions.surroundWithQuotes)) {
                result.words = tempArray;
            }
        }

        return result.words;
    };
    /**
     *
     * @param wordArray {String[]}
     * @param from {Number}
     * @param to {Number}
     * @param direction {String}
     */
    function getCombinationsFromTo(wordArray, from, to, direction) {
        var newWords = [];
        var innerWords = [];
        if (direction === 'both'){
            newWords = getCombinationsFromTo(wordArray, from, to, 'right');
            newWords = newWords.concat(getCombinationsFromTo(wordArray, from, to, 'left'));

            return newWords;
        }
        for (var i = 0; i < wordArray.length; i++) {
            innerWords = wordArray[i].split(' ');
            if (innerWords.length < from) {
                continue;
            }

            if (direction === 'left') {
                innerWords = innerWords.reverse();
            }

            for (var j = from; j <= to; j++) {
                var newInnerWords = innerWords;
                newInnerWords = innerWords.slice(0, j);
                if (direction === 'left') {
                    newInnerWords = newInnerWords.reverse();
                }
                newWords.push(newInnerWords.join(' '));
            }
        }

        return newWords;
    }

    /**
     *
     * @param array {String[]}
     * @param firstChar {String}
     * @param secondChar {String}
     * @returns {String[]}
     */
    List.surround = function (array, firstChar, secondChar) {
        var tempArray = [];
        for (var i = 0; i < array.length; i++) {
            if (array[i].trim() !== '') {
                tempArray.push(firstChar + array[i] + secondChar);
            }
        }

        return tempArray;
    };

    /**
     *
     * @param globalOptionsThatAffectCount {Boolean[]}
     * @returns {Number}
     */
    List.getMultiplierByGlobalOptions = function (globalOptionsThatAffectCount) {
        var multiplier = 0;
        for (var i = 0; i < globalOptionsThatAffectCount.length; i++) {
            multiplier += globalOptionsThatAffectCount[i] ? 1 : 0;
        }
        multiplier = multiplier === 0 ? 1 : multiplier;

        return multiplier;
    };

    /**
     *
     * @param lists {List[]}
     * @returns {Number}
     */
    List.getCountConsideringIndividualOptions = function (lists) {
        var quantity = 0;
        var additionalWords = 0;
        for (var i = 0; i < lists.length; i++) {
            var wordsCount = lists[i].getWordsCount();
            if (lists[i].options.addToResult) {
                additionalWords += lists[i].getWordsCount();
                if (lists[i].options.addBlankWord) {
                    additionalWords--;
                }
            }
            if (quantity !== 0 || wordsCount !== 0){
                quantity = quantity === 0 ? 1 : quantity;
                wordsCount = wordsCount === 0 ? 1 : wordsCount;
                quantity *= wordsCount;
            }
        }

        return quantity + additionalWords;
    };

    /**
     *.
     * @param lists {String[][]}
     * @param globalOptions {Object}
     * @returns {Number}
     */
    List.getCombinationsCount = function (lists, globalOptions) {
        var globalOptionsThatAffectCount = [globalOptions.surroundWithQuotes,
            globalOptions.surroundWithBrackets,
            globalOptions.addToResult];
        var quantity = List.getCountConsideringIndividualOptions(lists);
        var multiplier = List.getMultiplierByGlobalOptions(globalOptionsThatAffectCount);

        return quantity * multiplier;
    };

    /**
     *
     * @param array {String[]}
     * @returns String
     */
    function concat(array) {
        return array.join(' ').trim();
    }

    /**
     *
     * @param first {String[]}
     * @param second {String[]}
     * @returns {String[]}
     */
    List.combineTwoLists = function (first, second) {
        var list = [];
        if (second.length === 0){
            return first;
        }
        if (first.length === 0){
            return second;
        }
        for (var i = 0; i < first.length; i++) {
            for (var j = 0; j < second.length; j++) {
                list.push(concat([first[i], second[j]]));
            }
        }

        return list;
    };

    return {
        List: List
    };

});
