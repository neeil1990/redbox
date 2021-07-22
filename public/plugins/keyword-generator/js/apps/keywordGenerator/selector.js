define([
    'underscore',
    'jquery',
    'jquery.select2'
], function (
    _,
    $,
    select2
) {
    'use strict';

    return function initSelect(el, options) {
        var defaultSettings = {
            minimumResultsForSearch: Infinity
        };

        return $(el).select2(_.extend({}, defaultSettings, options));
    };
});
