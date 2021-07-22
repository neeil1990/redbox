/**
 * twigExt modules - модуль для расширенная twig.js
 * все расширения (функции, фильтры, тэги и т.д) реализовываем тут.
 * how it work https://github.com/justjohn/twig.js/wiki/Extending-twig.js
 *
 * @module lib/twig/twigExt
 */
define(['native-twig'], function (Twig, routing) {
    'use strict';

    function pluralize(number, titles)
    {
        var cases = [2, 0, 1, 1, 1, 2];
        return titles[ (number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5] ];
    }

    return (function () {
        Twig.extendFunction('url', function (urlAlias) {
            return (!routing.getRoute(urlAlias) ? routing.getRoute('baseUrl') : routing.getRoute(urlAlias));
        });

        Twig.extendFilter('pluralize', pluralize);

        return Twig;
    })();
});
