/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        'ff-orange': '#FF9900',
        'ff-black': '#111111',
        'ff-gray': '#1F1F1F',
      },
      fontFamily: {
        'sans': ['Inter', 'sans-serif'],
        'display': ['Teko', 'sans-serif'], // Uma fonte mais "gamer" se possível, ou usar padrão
      }
    },
  },
  plugins: [],
}
