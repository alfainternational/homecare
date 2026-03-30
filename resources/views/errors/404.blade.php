<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 — الصفحة غير موجودة</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{fontFamily:{arabic:['Cairo','sans-serif']}}}}</script>
  <style>body{font-family:'Cairo',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
  <div class="text-center px-6">
    <div class="text-9xl font-black text-[#F5A623] mb-4">404</div>
    <div class="text-2xl font-bold text-gray-800 mb-3">الصفحة غير موجودة</div>
    <p class="text-gray-500 mb-8 max-w-md mx-auto">عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها.</p>
    <div class="flex gap-4 justify-center">
      <a href="/" class="bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold px-8 py-3 rounded-2xl transition-all shadow-lg shadow-orange-100">عودة للرئيسية</a>
      <a href="javascript:history.back()" class="border-2 border-gray-200 text-gray-600 font-bold px-8 py-3 rounded-2xl hover:border-[#F5A623] transition-all">رجوع</a>
    </div>
  </div>
</body>
</html>
