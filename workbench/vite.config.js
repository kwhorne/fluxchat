import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss({
            content: [
                './resources/**/*.blade.php',
                './resources/**/*.js',
                './resources/**/*.vue',
                '../vendor/livewire/flux/stubs/*.blade.php',
                '../vendor/livewire/flux/src/**/*.php',
            ],
        }),
        laravel({
            input: ['resources/css/app.css'],
            refresh: true,
        }),
    ],
});
