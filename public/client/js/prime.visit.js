class Visit {
    style = {
        'showVisCode' : {
            width: '100%',
            padding: '5px 0',
            background: 'rgb(35 56 71)',
            color: '#b9b9b9',
            position: 'relative',
            "z-index": '500',
            clear: 'both',
            "text-align": 'center'
        },
        'showVisCodeReady' : {
            display: 'inline-block',
            "vertical-align": 'baseline',
            "line-height": '1',
            background: '#001d02',
            "font-size": '14px',
            "margin-left": '5px',
            padding: '3px 5px',
            border: '1px solid #bebebe',
            "border-radius": '4px',
            cursor: 'pointer'
        },
    };
    pagesCount = {};
    constructor(params) {
        if(!params){
            return;
        }

        this.params = JSON.parse(this.getAsObject(params));
    }

    handle() {

        if(!this.params)
            return;

        this.show();

        this.clicks();
        this.pages();

        let self = this;
        this.check();

        setTimeout(function () {
            self.check();
        }, 15000);
    }

    check() {
        if (this.newTime(this.params.minutes) <= this.time() && this.getPages() >= this.params.pages && this.getClicks() >= this.params.clicks) {
            this.show();
        }
    }

    getPages() {
        return (this.pagesCount) ? Object.keys(this.pagesCount).length : 0;
    }

    getClicks() {
        let clicks = self.getCookie('clicksPrime');
        return (clicks) ? clicks : 0;
    }

    newTime(minutes) {

        let time = null;
        if(!this.getCookie('minutesPrime')){
            time = this.time() + (60 * minutes);
            this.setCookie('minutesPrime', time);
        }else{
            time = this.getCookie('minutesPrime');
        }

        return time;
    }

    show() {
        $('.showVisCode').remove();
        let self = this;
        $.get( `/public/behavior/${this.params.domain}/code`).done(function(data) {
            let codeHtml = $('<span/>').text(data.code);
            let copyHtml = $('<div/>', {
                "class" : 'showVisCodeReady',
                "onClick" : '(new Visit()).copy()'
            }).css(self.style.showVisCodeReady).text('Скопировать');

            let showCode = $('<div/>', { "class" : 'showVisCode'}).css(self.style.showVisCode).html(
                `Промокод: ${codeHtml[0].outerHTML} ${copyHtml[0].outerHTML}`
            );
            $('body').append(showCode);
        }).fail(function() {
            console.log( "error" );
        });
    }

    copy() {
        let $temp = $("<input>");
        $("body").append($temp);
        $temp.val($('.showVisCode span').text()).select();
        document.execCommand("copy");
        $temp.remove();

        this.setCookie('pagesCount', 'null');
        this.setCookie('minutesCount', 'null');

        alert('Промокод скопирован!')
    }

    clicks() {
        let self = this;
        $('html').click(function () {
            let clicks = self.getCookie('clicksPrime');

            if(!clicks)
                clicks = 1;
            else
                clicks++;

            self.setCookie('clicksPrime', clicks);
        });
    }

    pages() {
        let pageUrls = $.parseJSON(this.getCookie('pagesPrime'));

        let url = window.location.pathname;
        this.pagesCount = {
            [url]: url,
            ...pageUrls
        };

        this.setCookie('pagesPrime', JSON.stringify(this.pagesCount));
    }

    time() {
        return parseInt(new Date().getTime() / 1000)
    }

    getAsObject(params){
        return this.b64DecodeUnicode(params);
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
