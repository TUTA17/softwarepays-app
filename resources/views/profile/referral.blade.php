@extends('theme::layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-20">
    <div class="mb-10">
        <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">Giới Thiệu Nhận Thưởng</h1>
        <p class="text-slate-500 dark:text-slate-400">Mời bạn bè tham gia và nhận phần thưởng hấp dẫn khi họ đăng ký & mua sắm.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            @include('theme::partials.user-sidebar')
        </div>

        <!-- Dashboard Content -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- Link giới thiệu -->
            <div class="glass-card rounded-2xl p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl -mr-10 -mt-10"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-link text-blue-500"></i> Link giới thiệu của bạn
                </h2>
                
                <div class="bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex flex-col sm:flex-row gap-4 items-center">
                    <div class="flex-1 w-full relative">
                        <input type="text" id="referral-link" readonly 
                            value="{{ url('/register?ref=' . $user->affiliate_code) }}"
                            class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-4 py-3 font-mono text-sm focus:outline-none focus:border-blue-500 transition-colors">
                    </div>
                    <button onclick="copyReferralLink()" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 whitespace-nowrap">
                        <i class="fa-regular fa-copy"></i> Copy Link
                    </button>
                </div>
                
                <div class="mt-4 flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                        <i class="fa-solid fa-check-circle text-emerald-500"></i> Thưởng đăng ký: <span class="font-bold text-emerald-500">{{ number_format($signupBonus) }}đ</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                        <i class="fa-solid fa-check-circle text-amber-500"></i> Hoa hồng mua hàng: <span class="font-bold text-amber-500">{{ $commissionPercent }}%</span> / đơn
                    </div>
                </div>
            </div>

            <!-- Thống kê -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-2xl">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-1">Số Người Đã Mời</p>
                        <div class="text-3xl font-bold text-slate-900 dark:text-white">{{ $referrals->total() }}</div>
                    </div>
                </div>
                
                <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-2xl">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-1">Tổng Hoa Hồng</p>
                        <div class="text-3xl font-bold text-emerald-500">{{ number_format($totalCommission) }}đ</div>
                    </div>
                </div>
            </div>

            <!-- Danh sách -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                        <i class="fa-solid fa-list-ul text-slate-400 mr-2"></i> Lịch Sử Giới Thiệu
                    </h2>
                </div>
                
                <div class="p-0">
                    @if($referrals->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-sm">
                                        <th class="p-4 font-medium">Người dùng</th>
                                        <th class="p-4 font-medium">Ngày tham gia</th>
                                        <th class="p-4 font-medium text-right">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @foreach($referrals as $refUser)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                                            <td class="p-4">
                                                <div class="flex items-center gap-3">
                                                    @if($refUser->avatar)
                                                        <img src="{{ asset($refUser->avatar) }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                                                    @else
                                                        <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 font-bold">
                                                            {{ substr($refUser->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-bold text-slate-900 dark:text-white">{{ $refUser->name }}</div>
                                                        <div class="text-xs text-slate-500">ID: #{{ $refUser->id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4 text-slate-500 text-sm">
                                                {{ $refUser->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="p-4 text-right">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-semibold">
                                                    Thành công
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="p-6 border-t border-slate-200 dark:border-slate-800">
                            {{ $referrals->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-3xl">
                                <i class="fa-solid fa-user-group"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Chưa có ai đăng ký</h3>
                            <p class="text-slate-500 max-w-md mx-auto">Hãy chia sẻ link giới thiệu của bạn cho bạn bè để nhận thưởng ngay nhé!</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function copyReferralLink() {
    var copyText = document.getElementById("referral-link");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // Cho mobile
    navigator.clipboard.writeText(copyText.value).then(function() {
        alert("Đã copy link giới thiệu!");
    }, function(err) {
        console.error('Không thể copy: ', err);
    });
}
</script>
@endsection
