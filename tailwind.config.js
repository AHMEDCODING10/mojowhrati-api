/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontSize: {
                'main-lg': '32px',
                'main-md': '26px',
                'main-sm': '22px',
                'sub-lg': '18px',
                'sub-md': '16px',
                'sub-sm': '14px',
            },
            colors: {
                'onyx': '#121212',
                'charcoal': '#1e1e1e',
                'ebony': '#0a0a0a',
                'ivory': '#FFFFF0',
                'soft-cream': '#FFF8E7',
                'charcoal-gray': '#333333',
                'warm-gray': '#7D7D7D',
                'gold': '#D4AF37',
                'rose-gold': '#B76E79',
                'light-taupe': '#D8CFC4',
                'pale-yellow': '#FFF5B7',
                'luxury-gold': {
                    DEFAULT: '#c5a059',
                    dark: '#8e6e3d',
                    light: '#d4b783',
                }
            },
            fontFamily: {
                sans: ['Almarai', 'Inter', 'Tajawal', 'sans-serif'],
                serif: ['Playfair Display', 'serif'],
                display: ['Montserrat', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
