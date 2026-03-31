<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة الإدارة') — WarmConcierge Admin</title>
    @include('layouts.partials.head')
</head>
<body class="bg-gray-100 text-[#2C2C2A] antialiased" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-20 bg-black/60 lg:hidden"
         @click="sidebarOpen = false"></div>

    <!-- SIDEBAR — Dark branded -->
    <aside class="fixed inset-y-0 right-0 z-30 flex flex-col w-64 bg-[#2C2C2A] border-l border-white/5
                  transform transition-transform duration-300 ease-in-out
                  lg:translate-x-0 lg:static"
           :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'">

        <!-- Logo + Admin Label -->
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-[#F5A623] flex items-center justify-center shadow-lg flex-shrink-0">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 text-white">
                    <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    <rect x="9" y="13" width="6" height="8" rx="1" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-white text-sm leading-tight">WarmConcierge</p>
                <span class="inline-flex items-center gap-1 mt-0.5 text-xs font-bold bg-[#F5A623]/20 text-[#F5A623] px-2 py-0.5 rounded-full">
                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/></svg>
                    Admin
                </span>
            </div>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-5 space-y-1">

            <a href="{{ url('/admin') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('admin') && !request()->is('admin/*') ? 'bg-[#F5A623] text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                لوحة التحكم
            </a>

            <a href="{{ url('/admin/requests') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('admin/requests*') ? 'bg-[#F5A623] text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                الطلبات
                <span class="mr-auto bg-white/20 text-white text-xs font-bold px-2 py-0.5 rounded-full">12</span>
            </a>

            <a href="{{ url('/admin/subscriptions') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('admin/subscriptions*') ? 'bg-[#F5A623] text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                الاشتراكات
            </a>

            <a href="{{ url('/admin/inventory') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('admin/inventory*') ? 'bg-[#F5A623] text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                المخزون
            </a>

            <a href="{{ url('/admin/users') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('admin/users*') ? 'bg-[#F5A623] text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                الفنيون
            </a>

            <a href="{{ url('/admin/users') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('admin/users*') ? 'bg-[#F5A623] text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                المستخدمون
            </a>

            <a href="{{ url('/admin/reports') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('admin/reports*') ? 'bg-[#F5A623] text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                التقارير
            </a>

            <div class="my-2 border-t border-white/10"></div>

            <a href="{{ url('/admin/settings') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ request()->is('admin/settings*') ? 'bg-[#F5A623] text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                الإعدادات
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-300 hover:bg-red-500/20 hover:text-red-400 transition-all duration-200 w-full text-right">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    تسجيل الخروج
                </button>
            </form>
        </nav>

        <!-- Admin Profile Bottom -->
        <div class="px-4 py-4 border-t border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#F5A623]/20 flex items-center justify-center flex-shrink-0 ring-2 ring-[#F5A623]/40">
                    <span class="text-[#F5A623] font-bold text-sm">{{ mb_substr(auth()->user()->name ?? 'أ', 0, 1) }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'المدير' }}</p>
                    <p class="text-xs text-gray-500 truncate">مدير النظام</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN AREA -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        <!-- TOP BAR -->
        <header class="flex-shrink-0 bg-white border-b border-gray-200 shadow-sm z-10">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6">

                <div class="flex items-center gap-3">
                    <button class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100" @click="sidebarOpen = true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-base font-bold text-[#2C2C2A]">@yield('page-title', 'لوحة التحكم')</h1>
                        <p class="text-xs text-gray-400 hidden sm:block">@yield('page-subtitle', 'إدارة المنصة')</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Quick Stats -->
                    <div class="hidden md:flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2">
                        <div class="flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span class="text-xs font-semibold text-gray-600">النظام يعمل</span>
                        </div>
                    </div>

                    <!-- Notification Bell -->
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
                             class="absolute left-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                <p class="font-bold text-sm text-[#2C2C2A]">التنبيهات</p>
                                <span class="text-xs bg-red-500 text-white font-bold px-2 py-0.5 rounded-full">5 جديدة</span>
                            </div>
                            <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
                                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                    <div class="w-7 h-7 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-[#2C2C2A]">طلب جديد بانتظار المراجعة</p>
                                        <p class="text-xs text-gray-300 mt-1">منذ 5 دقائق</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                    <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-[#2C2C2A]">مستخدم جديد مسجل</p>
                                        <p class="text-xs text-gray-300 mt-1">منذ 20 دقيقة</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-2.5 border-t border-gray-100">
                                <a href="{{ url('/admin/notifications') }}" class="text-xs text-[#F5A623] font-semibold hover:text-[#D4881A]">عرض كل التنبيهات</a>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Info -->
                    <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2">
                        <div class="w-7 h-7 rounded-full bg-[#F5A623]/20 flex items-center justify-center ring-1 ring-[#F5A623]/40">
                            <span class="text-[#F5A623] font-bold text-xs">{{ mb_substr(auth()->user()->name ?? 'أ', 0, 1) }}</span>
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-xs font-bold text-[#2C2C2A] leading-tight">{{ auth()->user()->name ?? 'المدير' }}</p>
                            <p class="text-xs text-gray-400 leading-tight">مدير النظام</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
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

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
