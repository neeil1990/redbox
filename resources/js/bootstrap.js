window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Password generator
 * @type {{GenerateOptions: GenerateOptions; generate(options?: GenerateOptions): string; generateMultiple(count: number, options?: GenerateOptions): string[]}}
 */
window.generator = require('generate-password');

window.cookies = require('js-cookie');

window.copy = require('copy-to-clipboard');

window.hash = require('hash.subscribe');

window.currencyFormatter = require('currency-formatter');

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: "pusher",
    key: "local",
    wsHost: window.location.hostname,
    wsPort: 443,
    wssPort: 443,
    forceTLS: true,
    disableStats: true,
    enabledTransports: ["ws", "wss"],
});

window.XLSX = require('xlsx');

window.exportToExcel = function(data, filename = 'export.xlsx') {
    const worksheet = window.XLSX.utils.json_to_sheet(data);

    const cols = [];
    const columnHeaders = Object.keys(data[0] || {});

    columnHeaders.forEach(header => {
        const maxLength = data.reduce((max, row) => {
            const cellValue = row[header] ? String(row[header]) : '';
            return Math.max(max, cellValue.length, header.length);
        }, 0);

        cols.push({ wch: maxLength + 2 }); // +2 для отступов
    });

    worksheet['!cols'] = cols;

    const workbook = window.XLSX.utils.book_new();
    window.XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');
    window.XLSX.writeFile(workbook, filename);
};
