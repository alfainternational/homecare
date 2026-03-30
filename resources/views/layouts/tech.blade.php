<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة الفني') — WarmConcierge</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#F5A623',
                        'primary-dark': '#D4881A',
                        'primary-light': '#FFF3DC',
                        accent: '#2C2C2A',
                    },
                    fontFamily: { cairo: ['Cairo', 'sans-serif'] }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Cairo', sans-serif; }</style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-[#2C2C2A] antialiased" x-data="{ sidebarOpen: false }">

<!-- TOP NAVBAR -->
<nav class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo (RTL: right side) -->
            <a href="{{ url('/tech/dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-[#F5A623] flex items-center justify-center shadow">
                    <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 text-white">
                        <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <rect x="9" y="13" width="6" height="8" rx="1" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-sm text-[#2C2C2A] leading-tight">WarmConcierge</p>
                    <p class="text-xs text-gray-400 leading-tight">بوابة الفنيين</p>
                </div>
            </a>

            <!-- Tech Name + Status Badge (RTL: left side) -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2">
                    <div x-data="{ online: true }" class="flex items-center gap-2">
                        <button @click="online = !online"
                                class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border-2 transition-all duration-200"
                                :class="online ? 'bg-green-50 text-green-700 border-green-300' : 'bg-gray-100 text-gray-500 border-gray-300'">
                            <span class="w-2 h-2 rounded-full" :class="online ? 'bg-green-500' : 'bg-gray-400'"></span>
                            <span x-text="online ? 'متاح' : 'غير متاح'"></span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2 p-1.5 pr-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 rounded-full bg-[#FFF3DC] flex items-center justify-center ring-2 ring-[#F5A623]/30">
                        <span class="text-[#F5A623] font-bold text-xs">{{ mb_substr(auth()->user()->name ?? 'ف', 0, 1) }}</span>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-xs font-bold text-[#2C2C2A]">{{ auth()->user()->name ?? 'الفني' }}</p>
                        <p class="text-xs text-gray-400">فني معتمد</p>
                    </div>
                </div>

                <!-- Mobile hamburger -->
                <button class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100" @click="sidebarOpen = true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

<div class="flex h-[calc(100vh-64px)] overflow-hidden">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden"
         @click="sidebarOpen = false"></div>

    <!-- SIDEBAR -->
    <aside class="fixed top-16 inset-y-auto right-0 z-30 flex flex-col w-60 h-[calc(100vh-64px)] bg-white border-l border-gray-100 shadow-lg
                  transform transition-transform duration-300 ease-in-out
                  lg:translate-x-0 lg:static lg:shadow-none"
           :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'">

        <nav class="flex-1 px-3 py-5 space-y-1">

            <a href="{{ url('/tech/dashboard') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('tech/dashboard') ? 'bg-[#F5A623] text-white shadow' : 'text-gray-600 hover:bg-[#FFF3DC] hover:text-[#F5A623]' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                لوحة التحكم
            </a>

            <a href="{{ url('/tech/requests') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('tech/requests*') ? 'bg-[#F5A623] text-white shadow' : 'text-gray-600 hover:bg-[#FFF3DC] hover:text-[#F5A623]' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                المهام المسندة
            </a>

            <a href="{{ url('/tech/schedule') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('tech/schedule') ? 'bg-[#F5A623] text-white shadow' : 'text-gray-600 hover:bg-[#FFF3DC] hover:text-[#F5A623]' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                الجدول الزمني
            </a>

            <a href="{{ url('/tech/profile') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('tech/profile') ? 'bg-[#F5A623] text-white shadow' : 'text-gray-600 hover:bg-[#FFF3DC] hover:text-[#F5A623]' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                ملفي الشخصي
            </a>

            <div class="my-2 border-t border-gray-100"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all duration-200 w-full text-right">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    تسجيل الخروج
                </button>
            </form>
        </nav>

        <!-- Stats Bottom -->
        <div class="px-4 pb-5 space-y-3">
            <div class="bg-[#FFF3DC] rounded-xl p-3">
                <p class="text-xs text-gray-500 font-medium">إجمالي المهام اليوم</p>
                <p class="text-2xl font-bold text-[#F5A623] mt-0.5">5</p>
                <p class="text-xs text-gray-400">3 مكتملة · 2 معلقة</p>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
        <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b border-gray-100 bg-white">
            <h1 class="text-base font-bold text-[#2C2C2A]">@yield('page-title', 'لوحة التحكم')</h1>
            <span class="text-xs text-gray-400">@yield('page-subtitle', '')</span>
        </div>

        @if(session('success') || session('error'))
        <div class="px-4 sm:px-6 pt-4" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition>
            @if(session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm font-semibold rounded-xl px-4 py-3 mb-2">
                <svg class="w-4 h-4 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-xl px-4 py-3 mb-2">
                <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
            @endif
        </div>
        @endif

        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
