/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
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
