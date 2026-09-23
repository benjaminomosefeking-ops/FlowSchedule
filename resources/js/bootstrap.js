import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.Pusher = Pusher;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

window.Echo = pusherKey
    ? new Echo({
        broadcaster: 'pusher',
        client: new Pusher(pusherKey, {
            wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
            wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
            wssPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
            forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        }),
        key: pusherKey,
    })
    : null;