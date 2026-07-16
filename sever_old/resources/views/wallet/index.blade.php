@extends('layouts.app')

@section('title', 'Nạp Tiền Vào Ví - KeyGame')

@push('styles')
<style>
    .amount-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .amount-btn::before {
        content: '';
        position: absolute;
        top: 0; left: -100%; w: 100%; h: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s;
    }
    .amount-btn:hover::before {
        left: 100%;
    }
    .amount-btn.active {
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 15px -3px rgba(59, 130, 246, 0.5);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-20">
        
        <div class="mb-10">
            <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">Nạp Tiền Vào Ví</h1>
            <p class="text-slate-500 dark:text-slate-400">Số dư trong ví được dùng để mua game tự động mọi lúc mọi nơi mà không cần chờ đợi xác nhận thanh toán qua ngân hàng.</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                @include('partials.user-sidebar')
            </div>

            <!-- Deposit Form -->
            <div class="lg:col-span-3">
                <div class="glass-card p-6 md:p-8 rounded-2xl">
                    @if ($errors->any())
                        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-6 py-4 rounded-xl flex items-center gap-3 shadow-lg mb-8">
                            <i class="fa-solid fa-triangle-exclamation text-xl shrink-0"></i> 
                            <span class="font-medium">{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <div id="depositForm">
                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-hand-pointer text-blue-400"></i> Chọn nhanh số tiền nạp
                            </label>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                                <button type="button" class="amount-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl py-3 text-center cursor-pointer font-bold hover:bg-slate-50 dark:bg-slate-800" onclick="setAmount(50000, this)">50.000đ</button>
                                <button type="button" class="amount-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl py-3 text-center cursor-pointer font-bold hover:bg-slate-50 dark:bg-slate-800" onclick="setAmount(100000, this)">100.000đ</button>
                                <button type="button" class="amount-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl py-3 text-center cursor-pointer font-bold hover:bg-slate-50 dark:bg-slate-800" onclick="setAmount(200000, this)">200.000đ</button>
                                <button type="button" class="amount-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl py-3 text-center cursor-pointer font-bold hover:bg-slate-50 dark:bg-slate-800" onclick="setAmount(500000, this)">500.000đ</button>
                                <button type="button" class="amount-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl py-3 text-center cursor-pointer font-bold hover:bg-slate-50 dark:bg-slate-800" onclick="setAmount(1000000, this)">1.000.000đ</button>
                                <button type="button" class="amount-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl py-3 text-center cursor-pointer font-bold hover:bg-slate-50 dark:bg-slate-800" onclick="setAmount(2000000, this)">2.000.000đ</button>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-keyboard text-blue-400"></i> Hoặc nhập số tiền khác (VNĐ)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-bold">₫</span>
                                </div>
                                <input type="number" id="customAmount" min="10000" max="10000000" placeholder="Tối thiểu 10,000đ" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-10 pr-4 py-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-xl font-display font-bold shadow-inner">
                            </div>
                        </div>

                        <button type="button" onclick="generateQR()" class="w-full btn-primary-glow text-slate-900 dark:text-white font-bold py-4 rounded-xl text-lg flex items-center justify-center gap-3 group">
                            TẠO MÃ QR NẠP TIỀN <i class="fa-solid fa-qrcode"></i>
                        </button>
                    </div>

                    <!-- QR Display Area -->
                    <div id="qrArea" class="hidden mt-8 text-center bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Quét Mã QR Bằng Ứng Dụng Ngân Hàng/MoMo</h3>
                        <p class="text-slate-500 dark:text-slate-400 mb-6">Số dư sẽ tự động được cộng vào tài khoản của bạn trong 1-3 phút.</p>
                        
                        <div class="inline-block p-4 bg-white rounded-xl shadow-lg border border-slate-100 mb-6">
                            <img id="qrImage" src="" alt="QR Code" class="w-64 h-64 object-contain">
                        </div>

                        <div class="bg-blue-50 dark:bg-slate-800/50 p-4 rounded-xl text-left border border-blue-100 dark:border-slate-700 max-w-md mx-auto">
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-500">Số tiền:</span>
                                <span class="font-bold text-slate-900 dark:text-white" id="qrAmount">0đ</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-500">Ngân hàng:</span>
                                <span class="font-bold text-slate-900 dark:text-white">TPBank</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-slate-500">Số TK:</span>
                                <span class="font-bold text-slate-900 dark:text-white">0123456789</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-slate-200 dark:border-slate-700">
                                <span class="text-slate-500">Nội dung chuyển:</span>
                                <span class="font-bold text-rose-500 text-lg tracking-wider">NAPTIEN {{ Auth::id() }}</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button onclick="resetForm()" class="text-sm text-blue-500 hover:underline"><i class="fa-solid fa-arrow-left"></i> Nhập lại số tiền</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function setAmount(amount, btn) {
        document.getElementById('customAmount').value = amount;
        
        // Xóa class active của tất cả nút
        let btns = document.querySelectorAll('.amount-btn');
        btns.forEach(b => {
            b.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-600', 'active');
        });
        
        // Thêm class active cho nút được bấm
        if (btn) {
            btn.classList.add('active');
        }
    }
    
    function generateQR() {
        const amount = document.getElementById('customAmount').value;
        if(!amount || amount < 10000) {
            alert('Vui lòng nhập số tiền hợp lệ (Tối thiểu 10,000đ)');
            return;
        }

        // Thông tin ngân hàng của Admin (Thay đổi tùy ý)
        const bankId = 'TPB'; // TP Bank
        const accountNo = '0123456789'; // Thay bằng STK TPBank của bạn
        const template = 'compact';
        const memo = 'NAPTIEN {{ Auth::id() }}';
        
        const qrUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-${template}.png?amount=${amount}&addInfo=${memo}`;
        
        document.getElementById('qrImage').src = qrUrl;
        document.getElementById('qrAmount').innerText = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        
        document.getElementById('depositForm').classList.add('hidden');
        document.getElementById('qrArea').classList.remove('hidden');
    }

    function resetForm() {
        document.getElementById('depositForm').classList.remove('hidden');
        document.getElementById('qrArea').classList.add('hidden');
    }
    
    // Auto-active button if manual input matches
    document.getElementById('customAmount').addEventListener('input', function(e) {
        let val = parseInt(e.target.value);
        let btns = document.querySelectorAll('.amount-btn');
        btns.forEach(b => b.classList.remove('active'));
        
        if(val) {
            btns.forEach(b => {
                if(parseInt(b.getAttribute('onclick').match(/\d+/)[0]) === val) {
                    b.classList.add('active');
                }
            });
        }
    });
</script>
@endpush
