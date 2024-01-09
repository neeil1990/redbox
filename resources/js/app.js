/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

var VueCookie = require('vue-cookie');
window.Vue.use(VueCookie);

var requireComponent = require.context('./', true, /Base[A-Z]\w+\.(vue|js)$/)
requireComponent.keys().forEach(function (fileName) {
    var baseComponentConfig = requireComponent(fileName)
    baseComponentConfig = baseComponentConfig.default || baseComponentConfig
    var baseComponentName = baseComponentConfig.name || (
        fileName
            .replace(/^.+\//, '')
            .replace(/\.\w+$/, '')
    )
    Vue.component(baseComponentName, baseComponentConfig)
});

import RemoveDuplicates from './components/pages/RemoveDuplicates';
import ResponseHttpCode from './components/pages/ResponseHttpCode';
import MetaTags from './components/meta-tags/Index';
import MetaTagsHistory from './components/meta-tags/History';

Vue.component('remove-duplicates', RemoveDuplicates);
Vue.component('response-http-code', ResponseHttpCode);
Vue.component('meta-tags', MetaTags);
Vue.component('meta-tags-history', MetaTagsHistory);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const vm = new Vue({
    el: '#app',
});


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

window.loading = function () {
    let loading = require('please-wait');

    let string = 'A good day to you fine user!';

    let msg = $('<p />').css({
        "font-size": '20px',
        "margin-bottom": '80px',
        color: '#FFF',
    }).addClass('loading-message').text(string);

    let sc = 'fold';

    let spinner = $('<div />', {
        class: 'sk-spinner sk-' + sc
    }).css('margin', '0 auto').append($('<div />').addClass(`sk-${sc}-cube`)[0].outerHTML.repeat(4));

    let loadingParams = {
        logo: "/img/logo.svg",
        backgroundColor: 'rgb(0 0 0 / 60%)',
        loadingHtml: msg[0].outerHTML + spinner[0].outerHTML
    };

    window.pleaseWait = loading.pleaseWait(loadingParams);

    return window.pleaseWait;
};

window.currencyFormatRu = function(num) {
    return currencyFormatter.format(num, {locale: 'ru-RU', symbol: '', decimal: '.'});
};
