module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './packages/analytics/resources/**/*.blade.php',
        './packages/analytics/resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', 'ui-sans-serif', 'system-ui'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
