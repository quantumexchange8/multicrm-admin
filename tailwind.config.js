const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{vue,js,jsx}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Comfortaa', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                dark: {
                    'eval-0': '#000000',
                    'eval-1': '#0F0F0F',
                    'eval-2': '#2A2F42',
                    'eval-3': '#2C3142',
                    'eval-4': '#989898',
                },
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
}
