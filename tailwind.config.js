import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    safelist: [
        'from-red-500', 'to-red-700',
        'from-blue-500', 'to-blue-700',
        'from-cyan-500', 'to-cyan-700',
        'from-rose-500', 'to-rose-700',
        'from-orange-500', 'to-orange-700',
        'from-teal-500', 'to-teal-700',
        'from-green-500', 'to-green-700',
        'from-emerald-500', 'to-emerald-700',
        'from-lime-500', 'to-lime-700',
        'from-indigo-500', 'to-indigo-700',
        'from-purple-500', 'to-purple-700',
        'from-violet-500', 'to-violet-700',
        'from-slate-500', 'to-slate-700',
        'from-gray-500', 'to-gray-700',
        'from-yellow-500', 'to-yellow-700',
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
