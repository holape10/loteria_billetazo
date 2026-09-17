import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                dorado: {
                    50: '#fdf8ec',
                    100: '#faf0d3',
                    200: '#f3dda1',
                    300: '#ecc76a',
                    400: '#e6b03d',
                    500: '#d4a017',
                    600: '#b8860b',
                    700: '#8f6508',
                    800: '#6b4b06',
                    900: '#4a3404',
                },
            },
        },
    },

    plugins: [forms],
};