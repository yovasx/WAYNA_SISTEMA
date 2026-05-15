import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#f4f1ff',
                    100: '#ece8ff',
                    200: '#d8d0ff',
                    300: '#b8abf7',
                    400: '#8d7ae4',
                    500: '#5b4bc4',
                    600: '#4a3aa8',
                    700: '#3d2f88',
                    800: '#32286e',
                    900: '#291f58',
                },
                accent: {
                    50: '#fcf3ee',
                    100: '#f6e4dc',
                    200: '#edc5b6',
                    300: '#dfa084',
                    400: '#cc7f60',
                    500: '#b86a4f',
                    600: '#94503a',
                    700: '#783f30',
                    800: '#5f3227',
                    900: '#4d2821',
                },
                surface: {
                    DEFAULT: '#f6f1ea',
                    soft: '#f3eee7',
                    raised: '#fffdf9',
                    muted: '#ede6dd',
                },
                stroke: {
                    DEFAULT: '#e3d9ce',
                    soft: '#eee6de',
                    strong: '#d1c3b4',
                },
                success: {
                    50: '#f2f8f4',
                    100: '#e7f3ec',
                    500: '#3f7a5c',
                    600: '#2f6148',
                    700: '#254d39',
                },
                danger: {
                    50: '#fcf1f0',
                    100: '#f8e6e4',
                    500: '#a14e4b',
                    600: '#853e3b',
                    700: '#6d3230',
                },
                ink: {
                    DEFAULT: '#201b18',
                    soft: '#6e6259',
                    muted: '#8b7f75',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                wayna: '0 18px 40px -24px rgba(50, 40, 110, 0.28)',
                'wayna-soft': '0 10px 30px -22px rgba(32, 27, 24, 0.28)',
                'wayna-inner': 'inset 0 1px 0 rgba(255, 255, 255, 0.7)',
            },
            borderRadius: {
                wayna: '1.5rem',
                'wayna-xl': '1.75rem',
            },
        },
    },

    plugins: [forms],
};
