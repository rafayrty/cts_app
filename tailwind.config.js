const colors = require('tailwindcss/colors')

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                danger: colors.rose,
                primary:  {
                  DEFAULT: '#0CDFBF',
                  50: '#A8FAEE',
                  100: '#95F9EA',
                  200: '#6EF7E2',
                  300: '#47F5DB',
                  400: '#21F3D3',
                  500: '#0CDFBF',
                  600: '#09AA91',
                  700: '#067564',
                  800: '#033F36',
                  900: '#010A09'
                },
              //colors.blue,
                success: colors.green,
                warning: colors.yellow,
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
