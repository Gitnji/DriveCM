import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        // Listen on all interfaces so tenant subdomains (testschool.lvh.me, etc.)
        // can reach the dev server.
        host: '0.0.0.0',

        // Allow cross-origin requests from any *.lvh.me host (dev only) plus the
        // bare lvh.me apex and admin subdomain. The browser blocks otherwise.
        cors: {
            origin: [
                /^https?:\/\/(.+\.)?lvh\.me(:\d+)?$/,
                /^https?:\/\/localhost(:\d+)?$/,
                /^https?:\/\/127\.0\.0\.1(:\d+)?$/,
            ],
        },

        // Tell the HMR client to connect via lvh.me (not [::1]) so subdomain pages
        // can establish the WebSocket from the right origin.
        hmr: {
            host: 'lvh.me',
        },

        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});