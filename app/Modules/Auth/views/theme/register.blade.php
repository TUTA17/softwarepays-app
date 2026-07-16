@extends('theme::layouts.app')

@section('title', __('register.page_title') . ' - SoftwarePays')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-600/20 mix-blend-screen filter blur-[100px] animate-pulse"></div>
            <div class="absolute top-[60%] -right-[10%] w-[40%] h-[60%] rounded-full bg-indigo-600/20 mix-blend-screen filter blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-[20%] right-[20%] w-[20%] h-[30%] rounded-full bg-violet-600/20 mix-blend-screen filter blur-[80px] animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="max-w-5xl w-full flex rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-700/50 shadow-2xl relative z-10 bg-white dark:bg-slate-900 sm:bg-white/40 sm:dark:bg-slate-900/40 sm:backdrop-blur-xl">
            
            <!-- Right Side: Visual Art (Hidden on Mobile) -->
            <div class="hidden lg:flex lg:w-1/2 relative bg-slate-50 dark:bg-slate-900 overflow-hidden items-center justify-center p-12">
                <!-- Abstract Cyberpunk Grid Background -->
                <div class="absolute inset-0 opacity-10 dark:opacity-20" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg, #94a3b8 1px, transparent 1px); background-size: 30px 30px;"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-50 dark:from-slate-900 via-transparent to-transparent z-0"></div>
                
                <div class="relative z-10 text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-lg shadow-blue-500/30 transform -rotate-6 group-hover:rotate-0 transition-transform duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h2 class="text-4xl font-display font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">{{ __('register.split_headline_line1') }} <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">{{ __('register.split_headline_line2') }}</span></h2>
                    <p class="text-slate-600 dark:text-slate-400 text-lg">{{ __('register.split_tagline_alt') }}</p>
                </div>
            </div>

            <!-- Left Side: Register Form -->
            <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-16">
                <div class="text-left mb-10">
                    <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">{{ __('register.title_alt') }}</h2>
                    <p class="text-slate-500 dark:text-slate-400">{{ __('register.subtitle_alt') }}</p>
                </div>
                
                @if ($errors->any())
                    <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-600 dark:text-rose-400 px-4 py-3 rounded-xl flex items-start gap-3 mb-8 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <ul class="list-disc pl-4 text-sm font-medium space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Social Register -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <a href="{{ route('social.redirect', 'google') }}" class="flex items-center justify-center gap-2 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md hover:bg-slate-50 dark:hover:bg-slate-700">
                        <i class="fa-brands fa-google text-[#DB4437]"></i> {{ __('register.google_button_short') }}
                    </a>
                    <a href="{{ route('social.redirect', 'github') }}" class="flex items-center justify-center gap-2 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md hover:bg-slate-50 dark:hover:bg-slate-700">
                        <i class="fa-brands fa-github text-slate-900 dark:text-white"></i> {{ __('register.github_button_short') }}
                    </a>
                </div>

                <div class="flex items-center mb-6">
                    <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    <span class="flex-shrink-0 mx-4 text-slate-400 dark:text-slate-500 text-sm font-medium">{{ __('register.register_divider_text') }}</span>
                    <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf
                    
                    @if(isset($ref) && $ref)
                        <input type="hidden" name="ref" value="{{ $ref }}">
                        <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 text-blue-600 dark:text-blue-400 px-4 py-3 rounded-xl mb-6 text-sm font-medium flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ __('register.referral_prefix') }} <b>{{ $ref }}</b>
                        </div>
                    @endif

                    <div class="group">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors group-focus-within:text-blue-500" for="name">{{ __('register.display_name_label') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="{{ __('register.name_placeholder') }}" class="w-full bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-4 py-3.5 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="group">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors group-focus-within:text-blue-500" for="email">{{ __('register.email_label_prefix') }} {{ __('register.email_label') }}</label>
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
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors group-focus-within:text-blue-500" for="password">{{ __('register.password_label') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" required placeholder="••••••••" class="w-full bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-12 py-3.5 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
                            <button type="button" onclick="togglePasswordVisibility('password', 'eye-icon-register')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-500 transition-colors focus:outline-none">
                                <i id="eye-icon-register" class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="group">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors group-focus-within:text-blue-500" for="password_confirmation">{{ __('register.confirm_password_label') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••" class="w-full bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-11 pr-12 py-3.5 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eye-icon-confirm')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-500 transition-colors focus:outline-none">
                                <i id="eye-icon-confirm" class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-start mb-6">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" value="1" required class="w-4 h-4 border border-slate-300 rounded bg-slate-50 focus:ring-3 focus:ring-blue-300 dark:bg-slate-700 dark:border-slate-600 dark:focus:ring-blue-600 dark:ring-offset-slate-800">
                        </div>
                        <label for="terms" class="ml-2 text-sm font-medium text-slate-600 dark:text-slate-300">
                            {{ __('register.terms_prefix') }} <a href="{{ route('pages.terms') }}" target="_blank" class="text-blue-600 hover:underline dark:text-blue-500">{{ __('register.terms_link') }}</a> {{ __('register.terms_and') }} <a href="{{ route('pages.privacy') }}" target="_blank" class="text-blue-600 hover:underline dark:text-blue-500">{{ __('register.privacy_link') }}</a> {{ __('register.terms_suffix') }}
                        </label>
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
                    
                    <button type="submit" class="w-full relative overflow-hidden group bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 mt-6">
                        <div class="absolute inset-0 w-full h-full bg-white/20 group-hover:translate-x-full transition-transform duration-500 ease-out -translate-x-full skew-x-12"></div>
                        <span class="flex items-center justify-center gap-2 relative z-10">
                            {{ __('register.button') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </button>
                    
                    <div class="mt-8 text-center text-sm font-medium text-slate-600 dark:text-slate-400">
                        {{ __('register.have_account') }}
                        <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-bold transition-colors ml-1">{{ __('register.login_now') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
