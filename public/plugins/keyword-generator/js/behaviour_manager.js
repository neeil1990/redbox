define(['jquery'], function ($) {
    'use strict';

    return function (element) {
        if (!element) {
            element = document;
        }
        var $elHasBehavior = $('.__has-behaviour', element);
        $.each($elHasBehavior, function (index, el) {
            var behaviourName = $(el).data('behaviour');
            require(['behaviour/' + behaviourName], function(behaviourApply) {
                behaviourApply(el);
            });
        });
    };
});
