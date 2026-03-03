/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "wp-content/themes/mythem/**/*.php",

  ],
  theme: {
    extend: {
      colors: {
        blue: '#099FF7',
        orange: '#FF7E00',
        black: '#444444'
      }
    },

    fontSize: {
      'xs': ['0.75rem', { lineHeight: '1.4' }],      // 12px
      'sm': ['0.875rem', { lineHeight: '1.4' }],     // 14px
      'base': ['1rem', { lineHeight: '1.4' }],        // 16px
      'lg': ['1.125rem', { lineHeight: '1.4' }],     // 18px
      'xl': ['1.25rem', { lineHeight: '1.4' }],      // 20px
      '2xl': ['1.5rem', { lineHeight: '1.4' }],      // 24px
      '3xl': ['1.875rem', { lineHeight: '1.4' }],    // 30px
      '4xl': ['2.25rem', { lineHeight: '1.4' }],     // 36px
      '5xl': ['3rem', { lineHeight: '1.4' }],        // 48px
    }
  },
  plugins: [],
}
