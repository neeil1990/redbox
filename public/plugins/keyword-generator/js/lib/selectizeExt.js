define([
    'jquery',
    'selectizePatched'
], function (
    $,
    Selectize
) {
    'use strict';

    Selectize.define('no-delete', function () {
        this.deleteSelection = function () {};
    });

    return Selectize;
});

