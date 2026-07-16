@extends('theme::layouts.app')

@section('title', __('auth.login_title') . ' - SoftwarePays')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-600/20 mix-blend-screen filter blur-[100px] animate-pulse"></div>
            <div class="absolute top-[60%] -right-[10%] w-[40%] h-[60%] rounded-full bg-indigo-600/20 mix-blend-screen filter blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-[20%] right-[20%] w-[20%] h-[30%] rounded-full bg-violet-600/20 mix-blend-screen filter blur-[80px] animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="max-w-5xl w-full flex rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-700/50 shadow-2xl relative z-10 bg-white dark:bg-slate-900 sm:bg-white/40 sm:dark:bg-slate-900/40 sm:backdrop-blur-xl">

            <!-- Left Side: Visual Art (Hidden on Mobile) -->
            <div class="hidden lg:flex lg:w-1/2 relative bg-slate-50 dark:bg-slate-900 overflow-hidden items-center justify-center p-12">
                <!-- Abstract Cyberpunk Grid Background -->
                <div class="absolute inset-0 opacity-10 dark:opacity-20" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 30px 30px;"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-50 dark:from-slate-900 via-transparent to-transparent z-0"></div>

                <div class="relative z-10 text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-lg shadow-blue-500/30 transform -rotate-6 group-hover:rotate-0 transition-transform duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                        </svg>
                    </div>
                    <h2 class="text-4xl font-display font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">{{ __('auth.split_headline_line1') }} <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">{{ __('auth.split_headline_line2') }}</span></h2>
                    <p class="text-slate-600 dark:text-slate-400 text-lg">{{ __('auth.split_tagline_alt') }}</p>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-16">
                <div class="text-left mb-10">
                    <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">{{ __('auth.login_title') }}</h2>
                    <p class="text-slate-500 dark:text-slate-400">{{ __('auth.subtitle_alt') }}</p>
                </div>

                @if(session('error'))
                    <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-600 dark:text-rose-400 px-4 py-3 rounded-xl flex items-start gap-3 mb-6 shadow-sm">
                        <i class="fa-solid fa-circle-exclamation mt-1"></i>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-start gap-3 mb-6 shadow-sm">
                        <i class="fa-solid fa-circle-check mt-1"></i>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-600 dark:text-amber-400 px-4 py-3 rounded-xl flex items-start gap-3 mb-6 shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                        <span class="text-sm font-medium">{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Social Login -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <a href="{{ route('social.redirect', 'google') }}" class="flex items-center justify-center gap-2 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md hover:bg-slate-50 dark:hover:bg-slate-700">
                        <i class="fa-brands fa-google text-[#DB4437]"></i> {{ __('auth.google_button_short') }}
                    </a>
                    <a href="{{ route('social.redirect', 'github') }}" class="flex items-center justify-center gap-2 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md hover:bg-slate-50 dark:hover:bg-slate-700">
                        <i class="fa-brands fa-github text-slate-900 dark:text-white"></i> {{ __('auth.github_button_short') }}
                    </a>
                </div>

                <div class="flex items-center mb-6">
                    <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    <span class="flex-shrink-0 mx-4 text-slate-400 dark:text-slate-500 text-sm font-medium">{{ __('auth.login_divider_text') }}</span>
                    <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div class="group">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors group-focus-within:text-blue-500" for="email">{{ __('auth.email_label_prefix') }} {{ __('auth.email_label') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com" class="w-full bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-4 py-3.5 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="group">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 transition-colors group-focus-within:text-blue-500" for="password">{{ __('auth.password_label') }}</label>
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors">{{ __('auth.forgot_password') }}</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" required placeholder="••••••••" class="w-full bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-12 py-3.5 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
                            <button type="button" onclick="togglePasswordVisibility('password', 'eye-icon-login')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-500 transition-colors focus:outline-none">
                                <i id="eye-icon-login" class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <script>
                        function togglePasswordVisibility(inputId, iconId) {
                            const input = document.getElementById(inputId);
                            const icon = document.getElementById(iconId);
                            if (input.type === 'password') {
                                input.type = 'text';
                                icon.classList.remove('fa-eye');
                                icon.classList.add('fa-eye-slash');
                            } else {
                                input.type = 'password';
                                icon.classList.remove('fa-eye-slash');
                                icon.classList.add('fa-eye');
                            }
                        }
                    </script>

                    <div class="flex items-center pt-2">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900">
                        <label for="remember-me" class="ml-2 block text-sm font-medium text-slate-600 dark:text-slate-400 cursor-pointer select-none">
                            {{ __('auth.remember_me') }}
                        </label>
                    </div>

                    <button type="submit" class="w-full relative overflow-hidden group bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 mt-4">
                        <div class="absolute inset-0 w-full h-full bg-white/20 group-hover:translate-x-full transition-transform duration-500 ease-out -translate-x-full skew-x-12"></div>
                        <span class="flex items-center justify-center gap-2 relative z-10">
                            {{ __('auth.login_button_cta') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </button>

                    <div class="mt-8 text-center text-sm font-medium text-slate-600 dark:text-slate-400">
                        {{ __('auth.no_account') }}
                        <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-bold transition-colors ml-1">{{ __('auth.register_link_cta') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
