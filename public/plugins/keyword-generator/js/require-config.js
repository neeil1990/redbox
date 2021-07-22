(function () {
    'use strict';

    window.requirejs.config({
        baseUrl: '/plugins/keyword-generator/js',
        paths: {

            'jquery.qtip': 'lib/jquery.qtip.min', //++
            'selectizeExt': 'lib/selectizeExt',
            'jquery.select2': 'lib/select2.full.min',
            'native-twig': 'lib/twig.min', //++
            'twig': 'lib/twigExt', // local

            'underscore': 'lib/lodash',//+
            'backbone': 'lib/backbone.min',//+
            'backbone.modal': 'lib/backbone.modal.min', //+
            'backbone.marionette': 'lib/backbone.marionette.min', //+
            'backbone.marionette.modals': 'lib/backbone.marionette.modals', //+
            'clipboard':'lib/clipboard.min',
            'tpl': 'lib/requirejs-twig',//+
            'popup': 'popups/popup', // local
            'popup-layout': 'popups/popup-layout', // local

            'popup-confirm': 'popups/popup-confirm', // local

            'keywordGenerator': 'apps/keywordGenerator',
        },
        shim: {},
        waitSeconds: 200
    });
}());
