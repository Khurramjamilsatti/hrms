<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Digital</title>
    <link rel="icon" href="/images/payroll-digital-logo.png" type="image/png">
    <link rel="apple-touch-icon" href="/images/payroll-digital-logo.png">
    <meta property="og:image" content="{{ url('/images/payroll-digital-logo.png') }}">
    <script>
      (function () {
        try {
          var saved = localStorage.getItem('hrms-theme');
          var theme = saved === 'light' || saved === 'dark'
            ? saved
            : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
          document.documentElement.classList.toggle('dark', theme === 'dark');
          document.documentElement.setAttribute('data-theme', theme);
          document.documentElement.style.colorScheme = theme;
        } catch (e) {}
      })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-ink">
    <div id="app"></div>
</body>
</html>
