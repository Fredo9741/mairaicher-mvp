import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Filament/**/*.php',
    ],
    safelist: [
        // Classes pour les statuts de commande dans la page Logistique
        'bg-yellow-50', 'dark:bg-yellow-900/20', 'border-yellow-400',
        'bg-green-50', 'dark:bg-green-900/20', 'border-green-500',
        'bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-500',
        'border-l-4',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
