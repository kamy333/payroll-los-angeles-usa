/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.php',
        './resources/js/**/*.js',
        './app/View/**/*.php',
    ],
    darkMode: ['class', "[data-theme='dark']"],
    theme: {
        extend: {
            colors: {
                surface: 'rgb(var(--color-surface) / <alpha-value>)',
                elevated: 'rgb(var(--color-elevated) / <alpha-value>)',
                foreground: 'rgb(var(--color-foreground) / <alpha-value>)',
                muted: 'rgb(var(--color-muted) / <alpha-value>)',
                border: 'rgb(var(--color-border) / <alpha-value>)',
                primary: 'rgb(var(--color-primary) / <alpha-value>)',
                'primary-foreground': 'rgb(var(--color-primary-foreground) / <alpha-value>)',
            },
            fontFamily: {
                sans: ['"Inter"', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'sans-serif'],
            },
            boxShadow: {
                shell: '0 20px 45px -25px rgb(var(--color-primary) / 0.35)',
            },
        },
    },
    plugins: [],
};
