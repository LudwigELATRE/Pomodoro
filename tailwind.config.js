/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./templates/**/*.html.twig",
    "./assets/vue/**/*.{vue,js}",
    "./assets/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        'pomodoro-red': '#DC2626',
        'pomodoro-green': '#16A34A',
        'pomodoro-blue': '#2563EB',
      }
    },
  },
  plugins: [],
}
