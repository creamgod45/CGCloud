import plugin from "tailwindcss/plugin";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        './resources/**/*.vue',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    darkMode: 'selector',
    theme: {
        extend: {
            colors:{
                'color1': 'rgb(248, 202, 68)',
                'color1-50': '#e6bc40',
                'color2': '#5866a6',
                'color2-50': '#697ac6',
                'color3': '#405ee6',
                'color3-50': '#4768fa',
                'color4': '#918259',
                'color4-50': '#b2a06e',
                'color5': '#505466',
                'color5-50': '#747a94',
                'color6': '#3c3933',
                'color6-50': '#666056',
                'color7': 'var(--color7)',
                'color7-50': 'var(--color7-50)',
                'menu-bar': 'var(--menu-bar)',
                'line-50': '#4CC764',
                'line': '#06C755',
            },
            gridColumnGap: {
                us: '0px',
                sm: '0px',
                footer: '10px',
                md: '130px',
                lg: '15px',
                menu: '85px',
                xl: '240px',
                xxl: '65px',
                normal: '45px',
            },
            screens: {
                us: '0px',
                xs: '320px',
                sm: '480px',
                footer: '648px',
                md: '768px',
                lg: '976px',
                menu: '989px',
                xl: '1120px',
                xxl: '1440px',
            },
            borderRadius: {
                '4xl': '2rem',
            },
            opacity: {
                '0': '0',
                '20': '0.2',
                '40': '0.4',
                '60': '0.6',
                '80': '0.8',
                '100': '1',
            },
            flex: {
                'auto1': '0 0 auto'
            },
            scale: {
                "20": '0.20',
                "25": '0.25',
                "30": '0.30',
                "35": '0.35',
            },
            textShadow: {
                sm: '1px 1px 2px var(--tw-shadow-color)',
                DEFAULT: '2px 2px 4px var(--tw-shadow-color)',
                lg: '4px 4px 8px var(--tw-shadow-color)',
                xl: '4px 4px 16px var(--tw-shadow-color)',
            },
        },
    },
    plugins: [
        require('tailwind-scrollbar'),
        plugin(function ({ matchUtilities, theme }) {
            matchUtilities(
                {
                    'text-shadow': (value) => ({
                        textShadow: value,
                    }),
                },
                { values: theme('textShadow') }
            )
        }),

        plugin(function ({ matchUtilities, theme }) {
            matchUtilities(
                {
                    'grid-column-gap': (value) => ({
                        gridColumnGap: value,
                    }),
                },
                { values: theme('gridColumnGap') }
            )
        }),
    ],
}
