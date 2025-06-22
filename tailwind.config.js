/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.{blade.php,js,vue}",
    ],
    theme: {
        extend: {
            colors: {
                primary: '#FF9800',
                darkPrimary: '#374151',
                lightText: '#4B5563',
                darkText: '#E5E7EB',
                secondary: '#4A90E2',
            },
            spacing: {
                '128': '32rem',
                '144': '36rem',
            },
            animation: {
                fadeIn: 'fadeIn 1s ease-in-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
        fontFamily: {
            vasir: ["vasir"],
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
