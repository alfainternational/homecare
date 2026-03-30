<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') — WarmConcierge</title>
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
    <style>
        * { font-family: 'Cairo', sans-serif; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-[#2C2C2A] antialiased" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden"
         @click="sidebarOpen = false"></div>

    <!-- SIDEBAR -->
    <aside class="fixed inset-y-0 right-0 z-30 flex flex-col w-64 bg-white border-l border-gray-100 shadow-xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:shadow-none"
           :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'">

        <!-- Logo -->
        <div class="flex items-center gap-2.5 px-5 py-5 border-b border-gray-100">
            <div class="w-9 h-9 rounded-xl bg-[#F5A623] flex items-center justify-center shadow flex-shrink-0">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 text-white">
                    <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    <rect x="9" y="13" width="6" height="8" rx="1" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm text-[#2C2C2A] leading-tight">WarmConcierge</p>
                <p class="text-xs text-gray-400 leading-tight">لوحة التحكم</p>
            </div>
        </div>

        <!-- User Info -->
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#FFF3DC] flex items-center justify-center flex-shrink-0 ring-2 ring-[#F5A623]/30">
                    <span class="text-[#F5A623] font-bold text-sm">{{ mb_substr(auth()->user()->name ?? 'م', 0, 1) }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-[#2C2C2A] truncate">{{ auth()->user()->name ?? 'المستخدم' }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
            </div>
            <div class="mt-2.5">
                <span class="inline-flex items-center gap-1 bg-[#FFF3DC] text-[#D4881A] text-xs font-bold px-2.5 py-1 rounded-lg">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    {{ auth()->user()->subscription->plan->name ?? 'الباقة الأساسية' }}
                </span>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4 space-y-1">

            <a href="{{ url('/dashboard') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('dashboard') && !request()->is('dashboard/*') ? 'bg-[#F5A623] text-white shadow' : 'text-gray-600 hover:bg-[#FFF3DC] hover:text-[#F5A623]' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                لوحتي
            </a>

            <a href="{{ url('/dashboard/requests/new') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('dashboard/requests/new') ? 'bg-[#F5A623] text-white shadow' : 'text-gray-600 hover:bg-[#FFF3DC] hover:text-[#F5A623]' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                طلب جديد
            </a>

            <a href="{{ url('/dashboard/requests') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('dashboard/requests') ? 'bg-[#F5A623] text-white shadow' : 'text-gray-600 hover:bg-[#FFF3DC] hover:text-[#F5A623]' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                طلباتي
            </a>

            <a href="{{ url('/dashboard/subscription') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('dashboard/subscription') ? 'bg-[#F5A623] text-white shadow' : 'text-gray-600 hover:bg-[#FFF3DC] hover:text-[#F5A623]' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                اشتراكي
            </a>

            <a href="{{ url('/store') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('store*') ? 'bg-[#F5A623] text-white shadow' : 'text-gray-600 hover:bg-[#FFF3DC] hover:text-[#F5A623]' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                المتجر
            </a>

            <div class="my-2 border-t border-gray-100"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-gray-600 hover:bg-red-50 hover:text-red-600 w-full text-right">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    تسجيل الخروج
                </button>
            </form>
        </nav>

        <!-- Bottom CTA -->
        <div class="px-4 py-4 border-t border-gray-100">
            <a href="{{ url('/dashboard/requests/new') }}"
               class="flex items-center justify-center gap-2 w-full bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold text-sm py-3 px-4 rounded-xl shadow hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                طلب صيانة جديد
            </a>
        </div>
    </aside>

    <!-- MAIN AREA -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        <!-- TOP HEADER -->
        <header class="flex-shrink-0 bg-white border-b border-gray-100 shadow-sm z-10">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6">

                <div class="flex items-center gap-3">
                    <button class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100" @click="sidebarOpen = true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-base font-bold text-[#2C2C2A]">@yield('page-title', 'لوحة التحكم')</h1>
                        <p class="text-xs text-gray-400 hidden sm:block">@yield('page-subtitle', 'مرحباً بك في WarmConcierge')</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">

                    <!-- Bell -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 rounded-xl text-gray-500 hover:bg-gray-100 hover:text-[#F5A623] transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                <p class="font-bold text-sm text-[#2C2C2A]">الإشعارات</p>
                                <span class="text-xs bg-[#F5A623] text-white font-bold px-2 py-0.5 rounded-full">3 جديدة</span>
                            </div>
                            <div class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-[#2C2C2A]">تم قبول طلبك</p>
                                        <p class="text-xs text-gray-400 mt-0.5">سيصلك الفني خلال ساعتين</p>
                                        <p class="text-xs text-gray-300 mt-1">منذ 10 دقائق</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                    <div class="w-8 h-8 bg-[#FFF3DC] rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-[#F5A623]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-[#2C2C2A]">تذكير موعد</p>
                                        <p class="text-xs text-gray-400 mt-0.5">لديك زيارة صيانة غداً الساعة 10 صباحاً</p>
                                        <p class="text-xs text-gray-300 mt-1">منذ ساعة</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-2.5 border-t border-gray-100">
                                <a href="#" class="text-xs text-[#F5A623] font-semibold hover:text-[#D4881A]">عرض كل الإشعارات</a>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 pr-2 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-[#FFF3DC] flex items-center justify-center ring-2 ring-[#F5A623]/30">
                                <span class="text-[#F5A623] font-bold text-xs">{{ mb_substr(auth()->user()->name ?? 'م', 0, 1) }}</span>
                            </div>
                            <span class="hidden sm:block text-sm font-semibold text-[#2C2C2A] max-w-[96px] truncate">{{ auth()->user()->name ?? 'المستخدم' }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-bold text-[#2C2C2A] truncate">{{ auth()->user()->name ?? 'المستخدم' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                            </div>
                            <div class="py-1">
                                <a href="#" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 hover:text-[#F5A623]">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    الملف الشخصي
                                </a>
                            </div>
                            <div class="border-t border-gray-100 py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 w-full text-right">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        تسجيل الخروج
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success') || session('error'))
        <div class="px-4 sm:px-6 pt-4" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition>
            @if(session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm font-semibold rounded-xl px-4 py-3">
                <svg class="w-4 h-4 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-xl px-4 py-3">
                <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
            @endif
        </div>
        @endif

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
