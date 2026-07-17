/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // POS Omni–inspired brand palette
        brand: {
          DEFAULT: '#1e1433',
          soft: '#2a1f45',
          muted: '#3d2f5c',
          light: '#5a4a7a',
        },
        accent: {
          DEFAULT: '#ff5b60',
          soft: '#ffe4e5',
          muted: '#ff8a8e',
          dark: '#e8454a',
        },
        gold: {
          DEFAULT: '#ffc107',
          soft: '#fff3cd',
          dark: '#e6ac00',
        },
        // Semantic surfaces (flip via CSS vars in dark mode)
        surface: {
          DEFAULT: 'rgb(var(--color-surface) / <alpha-value>)',
          card: 'rgb(var(--color-card) / <alpha-value>)',
          border: 'rgb(var(--color-border) / <alpha-value>)',
          muted: 'rgb(var(--color-muted) / <alpha-value>)',
        },
        ink: {
          DEFAULT: 'rgb(var(--color-ink) / <alpha-value>)',
          soft: 'rgb(var(--color-ink-soft) / <alpha-value>)',
          muted: 'rgb(var(--color-ink-muted) / <alpha-value>)',
        },
        // Keep primary aliased to accent for legacy .btn-primary
        primary: {
          50: '#fff5f5',
          100: '#ffe4e5',
          200: '#ffc9cb',
          300: '#ff8a8e',
          400: '#ff6b70',
          500: '#ff5b60',
          600: '#e8454a',
          700: '#c9383d',
          800: '#a82e32',
          900: '#1e1433',
        },
      },
      boxShadow: {
        card: '0 2px 12px rgb(var(--color-shadow) / 0.06)',
        soft: '0 4px 24px rgb(var(--color-shadow) / 0.08)',
      },
    },
  },
  plugins: [],
}
