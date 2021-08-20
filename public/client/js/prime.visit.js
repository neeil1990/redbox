class Visit {
    pagesRequired = 2;
    clicksRequired = 5;
    keyCookie = 'paramsVisit';

    pagesCount = {};
    constructor() {
        let paramVisit = this.getCookie(this.keyCookie);

        if(!paramVisit){
            return;
        }

        this.paramVisit = paramVisit;
    }

    update(params) {
        this.setCookie(this.keyCookie, params, {expires: .5});
        this.paramVisit = this.getCookie(this.keyCookie);
    }

    handle() {
        if(!this.paramVisit || this.paramVisit === 'null')
            return;

        this.clicks();
        this.pages();

        let self = this;
        this.check();
        setTimeout(function () {
            self.check();
        }, 15000);
    }

    check() {
        let params = this.getAsObject();

        if (this.newTime(params[2]) <= this.time() && params[4] >= this.pagesRequired && params[5] >= this.clicksRequired) {
            this.show(params[1]);
        }
    }

    newTime(minutes) {
        let time = null;
        if(!this.getCookie('minutesCount')){
            time = this.time() + (60 * minutes);
            this.setCookie('minutesCount', time);
        }else{
            time = this.getCookie('minutesCount');
        }

        return time;
    }

    show(code) {
        $('.showVisCode').remove();

        let codeHtml = $('<span/>').text(code);
        let copyHtml = $('<div/>', {
            "class" : 'showVisCodeReady',
            "onClick" : '(new Visit()).copy()'
        }).text('Скопировать');

        let showCode = $('<div/>', { "class" : 'showVisCode'}).html(
            `Промокод: ${codeHtml[0].outerHTML} ${copyHtml[0].outerHTML}`
        );

        $('body').append(showCode);
    }

    copy() {
        let $temp = $("<input>");
        $("body").append($temp);
        $temp.val($('.showVisCode span').text()).select();
        document.execCommand("copy");
        $temp.remove();

        this.setCookie(this.keyCookie, 'null');
        this.setCookie('pagesCount', 'null');
        this.setCookie('minutesCount', 'null');

        alert('Промокод скопирован!')
    }

    clicks() {
        let self = this;
        $('html').click(function () {
            if(self.paramVisit){
                let params = self.getAsObject();
                params[5]++;
                self.update(self.getAsString(params));
            }
        });
    }

    pages() {
        let pageUrls = $.parseJSON(this.getCookie('pagesCount'));

        let url = window.location.pathname;
        this.pagesCount = {
            [url]: url,
            ...pageUrls
        };

        this.setCookie('pagesCount', JSON.stringify(this.pagesCount));

        console.log(this.pagesCount);

        let params = this.getAsObject();
        params[4] = this.pagesCount ? Object.keys(this.pagesCount).length : 0;
        this.update(this.getAsString(params));
    }

    time() {
        return parseInt(new Date().getTime() / 1000)
    }

    getAsObject(){
        return this.b64DecodeUnicode(this.paramVisit).split('||');
    }

    getAsString(params) {
        return this.b64EncodeUnicode(params.join('||'));
    }

    b64EncodeUnicode(str) {
        return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function toSolidBytes(match, p1) {
            return String.fromCharCode('0x' + p1);
        }));
    }

    b64DecodeUnicode(str) {
        return decodeURIComponent(atob(str).split('').map(function (c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
    }

    getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : null;
    }

    setCookie(name, value, options = {}) {

        options = {
            path: '/',
            ...options
        };

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString();
        }

        let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

        for (let optionKey in options) {
            updatedCookie += "; " + optionKey;
            let optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += "=" + optionValue;
            }
        }
        document.cookie = updatedCookie;
    }
}
