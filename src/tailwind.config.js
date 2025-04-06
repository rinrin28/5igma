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
            colors: {
                customGray: '#E8EBF0',
                customPink: '#FF4A69',
                customLightPink2: '#FFABB9',
                customLightPink: '#FF768D',
                customBlue: '#99F7E9',
                customLightBlue: '#BCFFF5',
                customNavy: '#00214D',
                customYellow: '#FEEE95',
                customGreen: '#40867C',
                customGray2:'#DADEE5',
                customGray3:'#82868B',
                customNavy40: 'rgba(0, 33, 77, 0.4)', // 透明度40%のcustomNavy
                customWhite:'#FFFFFE'
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                zenkaku: ['"Zen Kaku Gothic Antique"', 'sans-serif'],
            },
            borderWidth: {
                '5': '5px', // feature/103で追加
            },
        },
    },

    plugins: [forms],
};
