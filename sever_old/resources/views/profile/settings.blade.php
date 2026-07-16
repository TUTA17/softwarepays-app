@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-white text-xl shadow-lg">
            <i class="fa-solid fa-user-gear"></i>
        </div>
        <div>
            <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white">Cài Đặt Tài Khoản</h1>
            <p class="text-slate-500 mt-1">Quản lý thông tin cá nhân và bảo mật của bạn</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-600 dark:text-rose-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-xmark"></i>
            {{ session('error') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-600 dark:text-rose-400 px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-card rounded-2xl overflow-hidden">
        <form action="{{ route('profile.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-slate-800 pb-2">Thông tin cơ bản</h2>
            
            <!-- Avatar -->
            <div class="mb-6 flex items-center gap-6">
                <div class="relative w-24 h-24 rounded-full overflow-hidden bg-slate-200 dark:bg-slate-700 border-4 border-white dark:border-slate-800 shadow-md">
                    @if($user->avatar)
                        <img src="{{ asset($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400 text-3xl">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Ảnh đại diện</label>
                    <input type="file" name="avatar" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-slate-300">
                    <p class="text-xs text-slate-400 mt-1">Hỗ trợ JPG, PNG, GIF. Kích thước tối đa 2MB.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Địa chỉ Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-slate-800 pb-2">Đổi mật khẩu</h2>
            
            <p class="text-sm text-slate-500 mb-4">Để trống nếu bạn không muốn đổi mật khẩu.</p>
            
            <div class="space-y-4 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nhập lại mật khẩu mới</label>
                        <input type="password" name="new_password_confirmation" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/30 flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
