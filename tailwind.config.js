/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: '#F5A623',
          dark: '#D4881A',
          light: '#FFF3DC',
        },
        accent: '#2C2C2A',
      },
      fontFamily: {
        arabic: ['Cairo', 'Noto Kufi Arabic', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
