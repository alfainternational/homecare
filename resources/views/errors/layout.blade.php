<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') — WarmConcierge</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
  <div class="text-center max-w-lg">
    <div class="text-8xl font-black text-brand mb-4">@yield('code')</div>
    <h1 class="text-2xl font-bold text-gray-800 mb-3">@yield('title')</h1>
    <p class="text-gray-500 mb-8">@yield('message')</p>
    <div class="flex items-center justify-center gap-4">
      <a href="{{ url('/') }}"
         class="px-6 py-3 bg-brand text-white font-bold rounded-xl hover:bg-brand-dark transition-all">
        العودة للرئيسية
      </a>
      <button onclick="history.back()"
              class="px-6 py-3 border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-100 transition-all">
        الصفحة السابقة
      </button>
    </div>
  </div>
</body>
</html>
