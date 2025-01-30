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
    wsPort: process.env.PUSHER_APP_PORT,
    forceTLS: window.location.protocol == "http:" ? false : true,
    disableStats: true,
    enabledTransports: ["ws", "wss"],
});
