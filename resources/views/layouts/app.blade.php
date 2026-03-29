<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WarmConcierge — خدمات الصيانة المنزلية')</title>

    <!-- Google Fonts: Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:  '#F5A623',
                        'primary-dark': '#D4881A',
                        'primary-light': '#FFF3DC',
                        accent:   '#2C2C2A',
                    },
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { font-family: 'Cairo', sans-serif; }
        html { scroll-behavior: smooth; }
        .navbar-blur { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .toast-enter { animation: slideInRight 0.35s ease; }
        @keyframes slideInRight {
            from { transform: translateX(-60px); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-white text-accent antialiased" x-data="{ mobileMenu: false }">

    <!-- ============================================================
         STICKY NAVBAR
    ============================================================ -->
    <header
        class="sticky top-0 z-50 bg-white/95 navbar-blur border-b border-gray-100 shadow-sm"
        x-data="{ scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 10"
        :class="scrolled ? 'shadow-md' : ''"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo — Right side (RTL: appears on the right) -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 flex-shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center shadow">
                        <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <rect x="9" y="13" width="6" height="8" rx="1" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    <span class="font-bold text-lg text-accent tracking-tight">WarmConcierge</span>
                </a>

                <!-- Center Nav Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="#services"
                       class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary hover:bg-primary-light rounded-lg transition-colors duration-200">
                        الخدمات
                    </a>
                    <a href="#pricing"
                       class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary hover:bg-primary-light rounded-lg transition-colors duration-200">
                        الباقات
                    </a>
                    <a href="#how-it-works"
                       class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary hover:bg-primary-light rounded-lg transition-colors duration-200">
                        كيف يعمل
                    </a>
                </nav>

                <!-- Left side — Auth Buttons -->
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm font-semibold text-primary border-2 border-primary rounded-xl hover:bg-primary-light transition-colors duration-200">
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-5 py-2 text-sm font-bold text-white bg-primary hover:bg-primary-dark rounded-xl shadow hover:shadow-md transition-all duration-200">
                        اشترك الآن
                    </a>
                </div>

                <!-- Mobile Hamburger -->
                <button
                    class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100"
                    @click="mobileMenu = !mobileMenu"
                    aria-label="القائمة"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenu"  stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>

        <!-- Mobile Menu -->
        <div
            x-show="mobileMenu"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden bg-white border-t border-gray-100 px-4 pb-4 pt-2 space-y-1"
        >
            <a href="#services"  @click="mobileMenu=false" class="block px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-primary-light rounded-lg">الخدمات</a>
            <a href="#pricing"   @click="mobileMenu=false" class="block px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-primary-light rounded-lg">الباقات</a>
            <a href="#how-it-works" @click="mobileMenu=false" class="block px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-primary-light rounded-lg">كيف يعمل</a>
            <div class="pt-2 border-t border-gray-100 flex gap-2">
                <a href="{{ route('login') }}"    class="flex-1 text-center px-4 py-2.5 text-sm font-semibold text-primary border-2 border-primary rounded-xl hover:bg-primary-light">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary-dark rounded-xl">اشترك الآن</a>
            </div>
        </div>
    </header>

    <!-- ============================================================
         FLASH MESSAGES (Toast Notifications)
    ============================================================ -->
    @if(session('success') || session('error') || session('warning') || session('info'))
    <div
        class="fixed top-20 left-4 z-50 space-y-2 max-w-sm w-full"
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 5000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
    >
        @if(session('success'))
        <div class="toast-enter flex items-start gap-3 bg-white border-r-4 border-green-500 rounded-xl px-4 py-3 shadow-lg">
            <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-green-800">تمت العملية بنجاح</p>
                <p class="text-xs text-green-600 mt-0.5">{{ session('success') }}</p>
            </div>
            <button @click="show=false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="toast-enter flex items-start gap-3 bg-white border-r-4 border-red-500 rounded-xl px-4 py-3 shadow-lg">
            <div class="flex-shrink-0 w-6 h-6 bg-red-100 rounded-full flex items-center justify-center mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-red-800">حدث خطأ</p>
                <p class="text-xs text-red-600 mt-0.5">{{ session('error') }}</p>
            </div>
            <button @click="show=false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif

        @if(session('warning'))
        <div class="toast-enter flex items-start gap-3 bg-white border-r-4 border-yellow-500 rounded-xl px-4 py-3 shadow-lg">
            <div class="flex-shrink-0 w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mt-0.5">
                <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-yellow-800">تنبيه</p>
                <p class="text-xs text-yellow-700 mt-0.5">{{ session('warning') }}</p>
            </div>
            <button @click="show=false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif

        @if(session('info'))
        <div class="toast-enter flex items-start gap-3 bg-white border-r-4 border-blue-500 rounded-xl px-4 py-3 shadow-lg">
            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-0.5">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-blue-800">معلومة</p>
                <p class="text-xs text-blue-600 mt-0.5">{{ session('info') }}</p>
            </div>
            <button @click="show=false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif
    </div>
    @endif

    <!-- ============================================================
         MAIN CONTENT
    ============================================================ -->
    <main>
        @yield('content')
    </main>

    <!-- ============================================================
         FOOTER
    ============================================================ -->
    <footer class="bg-accent text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">

                <!-- Brand Column -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center shadow">
                            <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <rect x="9" y="13" width="6" height="8" rx="1" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <span class="font-bold text-lg tracking-tight">WarmConcierge</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        منصة متكاملة لخدمات الصيانة المنزلية الاحترافية. نضمن لك راحة البال مع فنيين معتمدين وخدمة على مدار الساعة.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="#" class="w-9 h-9 bg-white/10 hover:bg-primary rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557a9.83 9.83 0 01-2.828.775 4.932 4.932 0 002.165-2.724 9.864 9.864 0 01-3.127 1.195 4.916 4.916 0 00-8.384 4.482C7.691 8.094 4.066 6.13 1.64 3.161a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.061a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.937 4.937 0 004.604 3.417 9.868 9.868 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.054 0 13.999-7.496 13.999-13.986 0-.209 0-.42-.015-.63a9.936 9.936 0 002.46-2.548l-.047-.02z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-9 h-9 bg-white/10 hover:bg-primary rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="space-y-4">
                    <h4 class="font-bold text-base text-white">روابط سريعة</h4>
                    <ul class="space-y-2.5">
                        <li><a href="#services"    class="text-sm text-gray-400 hover:text-primary transition-colors">الخدمات</a></li>
                        <li><a href="#pricing"     class="text-sm text-gray-400 hover:text-primary transition-colors">الباقات والأسعار</a></li>
                        <li><a href="#how-it-works" class="text-sm text-gray-400 hover:text-primary transition-colors">كيف يعمل</a></li>
                        <li><a href="{{ route('login') }}"    class="text-sm text-gray-400 hover:text-primary transition-colors">تسجيل الدخول</a></li>
                        <li><a href="{{ route('register') }}" class="text-sm text-gray-400 hover:text-primary transition-colors">إنشاء حساب</a></li>
                    </ul>
                </div>

                <!-- Legal & Contact -->
                <div class="space-y-4">
                    <h4 class="font-bold text-base text-white">الدعم والقانوني</h4>
                    <ul class="space-y-2.5">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-primary transition-colors">تواصل معنا</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-primary transition-colors">سياسة الخصوصية</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-primary transition-colors">شروط الخدمة</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-primary transition-colors">إعدادات الكوكيز</a></li>
                    </ul>
                    <div class="pt-2">
                        <p class="text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                +966 50 000 0000
                            </span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                support@warmconcierge.com
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500 text-center">
                    &copy; 2024 WarmConcierge Home Services. جميع الحقوق محفوظة.
                </p>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-gray-600 bg-white/5 px-2.5 py-1 rounded-lg">🇸🇦 المملكة العربية السعودية</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
