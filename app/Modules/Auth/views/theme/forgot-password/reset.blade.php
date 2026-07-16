@extends('theme::layouts.app')

@section('title', __('fpreset.page_title'))

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-indigo-500/20 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 glass-card p-8 rounded-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
        
        <div>
            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/50 rounded-2xl flex items-center justify-center mx-auto mb-6 transform rotate-3 hover:rotate-0 transition-transform">
                <i class="fa-solid fa-key text-3xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h2 class="text-center text-3xl font-display font-bold text-slate-900 dark:text-white">
                {{ __('fpreset.heading') }}
            </h2>
            <p class="mt-3 text-center text-sm text-slate-500 dark:text-slate-400">
                {{ __('fpreset.description_prefix') }} <span class="font-bold text-slate-700 dark:text-slate-300">{{ $email }}</span>
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="space-y-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('fpreset.new_password_label') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required
                            class="appearance-none relative block w-full pl-10 px-3 py-3 border border-slate-300 dark:border-slate-600 placeholder-slate-500 text-slate-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-slate-800 transition-colors"
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-rose-500 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('fpreset.confirm_password_label') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock-open text-slate-400"></i>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="appearance-none relative block w-full pl-10 px-3 py-3 border border-slate-300 dark:border-slate-600 placeholder-slate-500 text-slate-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-slate-800 transition-colors"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-lg shadow-indigo-500/30 overflow-hidden">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fa-solid fa-check text-indigo-500 group-hover:text-indigo-400 transition-colors"></i>
                    </span>
                    {{ __('fpreset.update_button') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
