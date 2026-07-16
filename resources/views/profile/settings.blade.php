@extends('theme::layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            @include('theme::partials.user-sidebar')
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-3">

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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />

    <div class="glass-card rounded-2xl overflow-hidden">
        <!-- Đưa Header vào trong Card để cân bằng với Sidebar -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-white text-xl shadow-lg">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-slate-900 dark:text-white">{{ __('account.page_title') }}</h1>
                <p class="text-slate-500 text-sm mt-1">Quản lý thông tin cá nhân và bảo mật của bạn</p>
            </div>
        </div>

        <form action="{{ route('profile.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 pb-2">{{ __('account.info_heading') }}</h2>
            
            <!-- Avatar -->
            <div class="mb-6 flex items-center gap-6">
                <div id="avatar_preview_wrapper" class="relative w-24 h-24 rounded-full overflow-hidden bg-slate-200 dark:bg-slate-700 border-4 border-white dark:border-slate-800 shadow-md">
                    @if($user->avatar)
                        <img id="avatar_preview" src="{{ asset($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400 text-3xl">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Ảnh đại diện</label>
                    <input type="file" id="avatar_input" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-slate-300">
                    <input type="hidden" name="avatar_base64" id="avatar_base64">
                    <p class="text-xs text-slate-400 mt-1">Hỗ trợ JPG, PNG, GIF. Kích thước tối đa 2MB.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('account.name_label') }}</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('account.email_label') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-slate-800 pb-2">Bảo mật nâng cao</h2>
            
            <div class="mb-4 p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2"><i class="fa-solid fa-shield-alt text-blue-500"></i> {{ __('account.two_factor_heading') }}</h3>
                    <p class="text-sm text-slate-500 mt-1">{{ __('account.two_factor_description') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="two_factor_enabled" value="1" class="sr-only peer" {{ $user->two_factor_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="mb-8 p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2"><i class="fa-solid fa-lock text-rose-500"></i> {{ __('account.checkout_otp_heading') }}</h3>
                    <p class="text-sm text-slate-500 mt-1">{{ __('account.checkout_otp_description') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="checkout_otp_enabled" value="1" class="sr-only peer" {{ $user->checkout_otp_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                </label>
            </div>

            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-slate-800 pb-2">{{ __('account.change_password_heading') }}</h2>
            
            <p class="text-sm text-slate-500 mb-4">{{ __('account.password_leave_blank_note') }}</p>
            
            <div class="space-y-4 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('account.current_password_label') }}</label>
                    <input type="password" name="current_password" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('account.new_password_label') }}</label>
                        <input type="password" name="new_password" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('account.confirm_new_password_label') }}</label>
                        <input type="password" name="new_password_confirmation" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
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
</div>

<!-- Avatar Crop Modal -->
<div id="crop_modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-[90%] max-w-md overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Cắt ảnh đại diện</h3>
            <button type="button" id="close_crop_modal" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="p-4 bg-slate-100 dark:bg-slate-800 flex justify-center items-center h-[300px]">
            <img id="crop_image" src="" class="max-w-full max-h-full">
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
            <button type="button" id="cancel_crop" class="px-4 py-2 rounded-lg font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700">Hủy</button>
            <button type="button" id="save_crop" class="px-4 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700">Xác Nhận</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let cropper;
    const avatarInput = document.getElementById('avatar_input');
    const cropModal = document.getElementById('crop_modal');
    const cropImage = document.getElementById('crop_image');
    const closeCropModal = document.getElementById('close_crop_modal');
    const cancelCrop = document.getElementById('cancel_crop');
    const saveCrop = document.getElementById('save_crop');
    const avatarPreviewWrapper = document.getElementById('avatar_preview_wrapper');
    const avatarBase64 = document.getElementById('avatar_base64');

    avatarInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                cropImage.src = e.target.result;
                cropModal.classList.remove('hidden');
                cropModal.classList.add('flex');
                
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            };
            reader.readAsDataURL(file);
        }
    });

    const closeModal = () => {
        cropModal.classList.add('hidden');
        cropModal.classList.remove('flex');
        avatarInput.value = ''; // Reset file input so user can select same file again if they cancel
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    };

    closeCropModal.addEventListener('click', closeModal);
    cancelCrop.addEventListener('click', closeModal);

    saveCrop.addEventListener('click', function () {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300,
        });
        const base64Data = canvas.toDataURL('image/jpeg', 0.9);
        
        // Cập nhật giao diện
        avatarPreviewWrapper.innerHTML = `<img id="avatar_preview" src="${base64Data}" class="w-full h-full object-cover">`;
        
        // Lưu chuỗi base64
        avatarBase64.value = base64Data;
        
        closeModal();
    });
});
</script>
@endsection
