@extends('theme::layouts.app')

@php
    $sysSettings = \App\Modules\Core\Models\Setting::getAllGrouped();
    $bankConfig = $sysSettings['payment_tab'] ?? [];
    $bankId = $bankConfig['bank_id'] ?? 'TPB';
    $accountNo = $bankConfig['account_no'] ?? '0123456789';
    $accountName = $bankConfig['account_name'] ?? 'TÊN CHỦ TK';
    $paymentNotice = $bankConfig['payment_notice'] ?? '';
    $isVndCurrency = session('currency', 'VND') === 'VND';
    $paypalClientId = config('services.paypal.mode') === 'live' ? config('services.paypal.live_client_id') : config('services.paypal.sandbox_client_id');
@endphp

@section('title', 'Nạp Tiền Vào Ví - SoftwarePays')

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
            <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">{{ __('wallet.deposit_page_title') }}</h1>
            <p class="text-slate-500 dark:text-slate-400">{{ __('wallet.deposit_page_subtitle') }}</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                @include('theme::partials.user-sidebar')
            </div>

            <!-- Content Area -->
            <div class="lg:col-span-3 space-y-8">
                
                <!-- Deposit Form -->
                <div class="glass-card p-6 md:p-8 rounded-2xl">
                    <div id="depositForm">
                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-credit-card text-blue-400"></i> {{ __('wallet.deposit_choose_method') }}
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @if($isVndCurrency)
                                <button type="button" id="method-bank" class="deposit-method-btn active bg-blue-50 dark:bg-blue-500/10 border-2 border-blue-500 text-blue-700 dark:text-blue-400 rounded-xl py-3 text-center font-bold text-sm" onclick="setDepositMethod('bank', this)">
                                    <i class="fa-solid fa-building-columns block text-lg mb-1"></i> {{ __('wallet.deposit_bank_qr_label') }}
                                </button>
                                @else
                                <button type="button" id="method-bank" disabled title="{{ __('checkout.vnd_only_note') }}" class="deposit-method-btn opacity-40 grayscale cursor-not-allowed bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-600 rounded-xl py-3 text-center font-bold text-sm">
                                    <i class="fa-solid fa-building-columns block text-lg mb-1"></i> {{ __('wallet.deposit_bank_qr_label') }}
                                </button>
                                @endif
                                <button type="button" id="method-paypal" class="deposit-method-btn {{ $isVndCurrency ? 'bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300' : 'active bg-blue-50 dark:bg-blue-500/10 border-2 border-blue-500 text-blue-700 dark:text-blue-400' }} rounded-xl py-3 text-center font-bold text-sm" onclick="setDepositMethod('paypal', this)">
                                    <i class="fa-brands fa-paypal block text-lg mb-1"></i> PayPal <span class="text-[10px] opacity-70">({{ $paypalCurrency }})</span>
                                </button>
                                <button type="button" id="method-card" class="deposit-method-btn bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl py-3 text-center font-bold text-sm" onclick="setDepositMethod('card', this)">
                                    <i class="fa-solid fa-credit-card block text-lg mb-1"></i> {{ __('checkout.card_short_label') }} <span class="text-[10px] opacity-70">({{ $paypalCurrency }})</span>
                                </button>
                                <button type="button" id="method-crypto" class="deposit-method-btn bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl py-3 text-center font-bold text-sm" onclick="setDepositMethod('crypto', this)">
                                    <i class="fa-brands fa-bitcoin block text-lg mb-1"></i> Crypto
                                </button>
                            </div>
                        </div>

                        <div id="cryptoCurrencyPicker" class="hidden mb-8">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-coins text-blue-400"></i> {{ __('wallet.choose_crypto_label') }}
                            </label>
                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                                <button type="button" class="crypto-method-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl py-3 text-center font-bold text-xs" data-crypto="bitcoin" onclick="setCryptoMethod('bitcoin', this)">Bitcoin</button>
                                <button type="button" class="crypto-method-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl py-3 text-center font-bold text-xs" data-crypto="ethereum" onclick="setCryptoMethod('ethereum', this)">Ethereum</button>
                                <button type="button" class="crypto-method-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl py-3 text-center font-bold text-xs" data-crypto="litecoin" onclick="setCryptoMethod('litecoin', this)">Litecoin</button>
                                <button type="button" class="crypto-method-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl py-3 text-center font-bold text-xs" data-crypto="usdt" onclick="setCryptoMethod('usdt', this)">USDT</button>
                                <button type="button" class="crypto-method-btn bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl py-3 text-center font-bold text-xs" data-crypto="solana" onclick="setCryptoMethod('solana', this)">Solana</button>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-hand-pointer text-blue-400"></i> {{ __('wallet.quick_amount_label') }}
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
                                <i class="fa-solid fa-keyboard text-blue-400"></i> {{ __('wallet.custom_amount_label') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-bold">₫</span>
                                </div>
                                <input type="number" id="customAmount" min="10000" max="10000000" placeholder="Tối thiểu 10,000đ" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl pl-10 pr-4 py-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-xl font-display font-bold shadow-inner">
                            </div>
                        </div>

                        <button type="button" id="btnGenerate" onclick="handleDepositSubmit()" class="w-full btn-primary-glow text-slate-900 dark:text-white font-bold py-4 rounded-xl text-lg flex items-center justify-center gap-3 group">
                            <span id="btnGenerateLabel">{{ $isVndCurrency ? __('wallet.generate_qr_button') : 'PayPal' }}</span> <i class="fa-solid fa-qrcode"></i>
                        </button>
                        <div id="wallet-paypal-button-container" class="hidden"></div>
                    </div>

                    <!-- Crypto Payment Waiting Area -->
                    <div id="cryptoWaitArea" class="hidden text-center bg-white dark:bg-slate-900 py-8">
                        <div id="cryptoWaitLoading">
                            <i class="fa-solid fa-spinner fa-spin text-4xl text-blue-500"></i>
                            <p class="mt-4 text-slate-500">{{ __('wallet.initializing_transaction') }}</p>
                        </div>
                        <div id="cryptoWaitContent" class="hidden max-w-sm mx-auto">
                            <p class="text-sm text-slate-500 mb-2">{{ __('wallet.pay_instruction_crypto') }}</p>
                            <p id="cryptoWaitNetwork" class="mb-3 text-sm font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg py-2 px-3"></p>
                            <div class="flex items-center justify-center gap-2 mb-3">
                                <p id="cryptoWaitAmount" class="text-2xl font-bold text-slate-900 dark:text-white"></p>
                                <button type="button" onclick="copyToClipboard('cryptoWaitAmount', this)" class="text-slate-400 hover:text-blue-500 transition-colors" title="{{ __('wallet.pay_copy') }}">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                            <img id="cryptoWaitQr" class="mx-auto mb-3 rounded-lg border border-slate-200 dark:border-slate-700" width="180" height="180" alt="QR code">
                            <div class="flex items-center gap-2">
                                <p id="cryptoWaitAddress" class="flex-1 text-xs break-all bg-slate-100 dark:bg-slate-800 rounded-lg p-3 font-mono text-slate-700 dark:text-slate-300"></p>
                                <button type="button" onclick="copyToClipboard('cryptoWaitAddress', this)" class="shrink-0 text-slate-400 hover:text-blue-500 transition-colors" title="{{ __('wallet.pay_copy') }}">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                            <div id="cryptoWaitMinNote" class="hidden mt-3 rounded-lg border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 p-3">
                                <p class="text-xs text-red-600 dark:text-red-400"><i class="fa-solid fa-circle-info mr-1"></i><span id="cryptoWaitMinRequiredText"></span></p>
                                <p class="mt-1 text-2xl font-black text-red-600 dark:text-red-400">+<span id="cryptoWaitMinExtra"></span></p>
                                <p class="text-xs text-red-600 dark:text-red-400">{{ __('checkout.crypto_extra_credited_label') }}</p>
                            </div>
                            <p class="mt-4 text-sm text-amber-600 dark:text-amber-400"><i class="fa-solid fa-hourglass-half mr-1"></i> {{ __('wallet.pay_waiting') }}</p>
                            <button onclick="resetForm()" class="mt-6 px-6 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold transition-colors"><i class="fa-solid fa-arrow-left mr-2"></i> {{ __('wallet.pay_back') }}</button>
                        </div>
                        <div id="cryptoWaitError" class="hidden">
                            <p class="text-red-500"></p>
                        </div>
                    </div>

                    <!-- QR Display Area -->
                    <div id="qrArea" class="hidden text-center bg-white dark:bg-slate-900">
                        <div class="mb-6 flex flex-col items-center justify-center p-4 bg-rose-50 dark:bg-rose-900/20 border-b-4 border-rose-500 rounded-t-xl">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('wallet.qr_heading') }}</h3>
                            <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400 font-bold text-lg bg-white dark:bg-slate-800 px-4 py-2 rounded-full shadow-sm">
                                <i class="fa-regular fa-clock"></i> {{ __('wallet.expires_in_label') }}: <span id="countdown">59:59</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start text-left p-4 md:p-0">
                            <!-- Left: QR and Bank Details -->
                            <div class="flex flex-col items-center bg-slate-50 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-700">
                                <div class="p-3 bg-white rounded-xl shadow-lg border border-slate-100 mb-6">
                                    <img id="qrImage" src="" alt="QR Code" class="w-56 h-56 object-contain">
                                </div>
                                <div class="w-full space-y-3">
                                    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-2">
                                        <span class="text-slate-500 text-sm">{{ __('wallet.col_amount') }}:</span>
                                        <span class="font-bold text-blue-600 text-lg" id="qrAmount">0đ</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-2">
                                        <span class="text-slate-500 text-sm">{{ __('wallet.pay_bank_name_label') }}:</span>
                                        <span class="font-bold text-slate-900 dark:text-white">{{ $bankId }}</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-2">
                                        <span class="text-slate-500 text-sm">{{ __('wallet.pay_bank_account_label') }}:</span>
                                        <span class="font-bold text-slate-900 dark:text-white">{{ $accountNo }}</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-2">
                                        <span class="text-slate-500 text-sm">{{ __('wallet.account_holder_label') }}:</span>
                                        <span class="font-bold text-slate-900 dark:text-white">{{ strtoupper($accountName) }}</span>
                                    </div>
                                    <div class="flex flex-col pt-2 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800 mt-2">
                                        <span class="text-blue-600 dark:text-blue-400 text-xs font-bold mb-1 uppercase">{{ __('wallet.pay_content_label') }}:</span>
                                        <span class="font-bold text-rose-500 text-xl tracking-wider text-center" id="qrMemo">NAPTIEN {{ Auth::id() }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right: Instructions -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-lg mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-list-check text-blue-500"></i> {{ __('wallet.instructions_heading') }}
                                    </h4>
                                    <ul class="space-y-4">
                                        <li class="flex gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 flex items-center justify-center font-bold shrink-0">1</div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ __('wallet.step1_title') }}</p>
                                                <p class="text-sm text-slate-500">{{ __('wallet.step1_desc') }}</p>
                                            </div>
                                        </li>
                                        <li class="flex gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 flex items-center justify-center font-bold shrink-0">2</div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ __('wallet.step2_title') }}</p>
                                                <p class="text-sm text-slate-500">{{ __('wallet.step2_desc') }}</p>
                                            </div>
                                        </li>
                                        <li class="flex gap-3">
                                            <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-600 flex items-center justify-center font-bold shrink-0"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                            <div>
                                                <p class="font-bold text-rose-600">{{ __('wallet.warning_title') }}</p>
                                                <p class="text-sm text-slate-500">{{ __('wallet.warning_desc') }}</p>
                                            </div>
                                        </li>
                                        <li class="flex gap-3">
                                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 flex items-center justify-center font-bold shrink-0">3</div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ __('wallet.step3_title') }}</p>
                                                <p class="text-sm text-slate-500">{{ __('wallet.step3_desc') }}</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 p-4 rounded-r-lg">
                                    <h5 class="font-bold text-amber-800 dark:text-amber-500 text-sm mb-1"><i class="fa-solid fa-circle-exclamation"></i> {{ __('wallet.caution_label') }}</h5>
                                    <p class="text-xs text-amber-700 dark:text-amber-400">{!! __('wallet.caution_desc') !!}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800">
                            <button onclick="resetForm()" class="px-6 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold transition-colors"><i class="fa-solid fa-arrow-left mr-2"></i> {{ __('wallet.back_to_amount') }}</button>
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="glass-card rounded-2xl overflow-hidden mt-8">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg"><i class="fa-solid fa-clock-rotate-left text-blue-500 mr-2"></i> {{ __('wallet.history_heading') }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-bold">{{ __('wallet.detail_txid_label') }}</th>
                                    <th class="p-4 font-bold">{{ __('wallet.col_method') }}</th>
                                    <th class="p-4 font-bold">{{ __('wallet.col_amount') }}</th>
                                    <th class="p-4 font-bold">{{ __('wallet.col_status') }}</th>
                                    <th class="p-4 font-bold">{{ __('wallet.col_time') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800/50">
                                @forelse($transactions as $tx)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="p-4 font-mono text-xs text-slate-500">{{ $tx->reference_id ?? 'N/A' }}</td>
                                        <td class="p-4 text-slate-700 dark:text-slate-300 font-medium">{{ __('wallet.method_bank_transfer') }}</td>
                                        <td class="p-4 font-bold text-slate-900 dark:text-white">{{ number_format($tx->amount) }}đ</td>
                                        <td class="p-4">
                                            @if($tx->status == 'completed')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 text-xs font-bold border border-emerald-200 dark:border-emerald-800/50"><i class="fa-solid fa-check"></i> {{ __('wallet.status_completed') }}</span>
                                            @elseif($tx->status == 'pending')
                                                @if(\Carbon\Carbon::parse($tx->created_at)->addMinutes(15)->isPast())
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400 text-xs font-bold border border-rose-200 dark:border-rose-800/50"><i class="fa-solid fa-xmark"></i> {{ __('wallet.status_cancelled') }}</span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400 text-xs font-bold border border-amber-200 dark:border-amber-800/50"><i class="fa-solid fa-spinner fa-spin"></i> {{ __('wallet.status_pending') }}</span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400 text-xs font-bold border border-rose-200 dark:border-rose-800/50"><i class="fa-solid fa-xmark"></i> {{ __('wallet.status_failed') }}</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-slate-500 text-xs whitespace-nowrap">
                                            {{ $tx->created_at->format('d/m/Y - H:i:s') }}
                                            @if($tx->status == 'pending' && !\Carbon\Carbon::parse($tx->created_at)->addMinutes(15)->isPast())
                                                <div class="mt-2 flex items-center gap-2">
                                                    <button onclick="continuePayment({{ $tx->amount }})" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition">{{ __('wallet.retry_payment') }}</button>
                                                    <button onclick="cancelTx({{ $tx->id }})" class="px-2 py-1 bg-rose-500 text-white text-xs rounded hover:bg-rose-600 transition">{{ __('wallet.cancel_button') }}</button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-500">
                                            <div class="text-4xl mb-3 opacity-20"><i class="fa-solid fa-receipt"></i></div>
                                            {{ __('wallet.history_empty') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if($paypalClientId)
    <script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency={{ $paypalCurrency }}&intent=capture&enable-funding=card"></script>
    @endif
@endsection

@push('scripts')
<script>
    let depositMethod = {!! $isVndCurrency ? "'bank'" : "'paypal'" !!};
    let cryptoMethod = 'bitcoin';

    function setDepositMethod(method, btn) {
        depositMethod = method;
        document.querySelectorAll('.deposit-method-btn').forEach(b => {
            b.classList.remove('active', 'bg-blue-50', 'dark:bg-blue-500/10', 'border-blue-500', 'text-blue-700', 'dark:text-blue-400');
            b.classList.add('bg-white', 'dark:bg-slate-900', 'border-slate-200', 'dark:border-slate-700', 'text-slate-700', 'dark:text-slate-300');
        });
        btn.classList.add('active', 'bg-blue-50', 'dark:bg-blue-500/10', 'border-blue-500', 'text-blue-700', 'dark:text-blue-400');
        btn.classList.remove('bg-white', 'dark:bg-slate-900', 'border-slate-200', 'dark:border-slate-700', 'text-slate-700', 'dark:text-slate-300');

        document.getElementById('cryptoCurrencyPicker').classList.toggle('hidden', method !== 'crypto');

        const label = document.getElementById('btnGenerateLabel');
        if (method === 'bank') label.textContent = 'TẠO MÃ QR NẠP TIỀN';
        else if (method === 'paypal') label.textContent = 'NẠP TIỀN QUA PAYPAL';
        else if (method === 'card') label.textContent = 'NẠP TIỀN QUA THẺ';
        else label.textContent = 'NẠP TIỀN QUA CRYPTO';

        // PayPal/Thẻ: ẩn nút thường, mở popup PayPal ngay trên trang (JS SDK) thay vì chuyển hướng trang.
        const btnGenerate = document.getElementById('btnGenerate');
        const paypalContainer = document.getElementById('wallet-paypal-button-container');
        if (method === 'paypal' || method === 'card') {
            btnGenerate.classList.add('hidden');
            paypalContainer.classList.remove('hidden');
            renderWalletPaypalButtons(method);
        } else {
            btnGenerate.classList.remove('hidden');
            paypalContainer.classList.add('hidden');
            paypalContainer.innerHTML = '';
        }
    }

    function renderWalletPaypalButtons(method) {
        const container = document.getElementById('wallet-paypal-button-container');
        container.innerHTML = '';

        if (typeof paypal === 'undefined') {
            container.innerHTML = '<p class="text-sm text-red-600 dark:text-red-400 text-center">' + @json(__('checkout.generic_error')) + '</p>';
            return;
        }

        let currentTx = null;
        const buttons = paypal.Buttons({
            fundingSource: method === 'card' ? paypal.FUNDING.CARD : paypal.FUNDING.PAYPAL,
            style: { layout: 'horizontal', height: 55, tagline: false },
            createOrder: function () {
                const amount = document.getElementById('customAmount').value;
                if (!amount || amount < 10000) {
                    alert('Vui lòng nhập số tiền hợp lệ (Tối thiểu 10,000đ)');
                    return Promise.reject(new Error('invalid amount'));
                }
                return fetch('{{ route('payments.paypal.create_order') }}?amount=' + amount, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'error');
                    currentTx = data.tx;
                    return data.id;
                });
            },
            onApprove: function (data) {
                return fetch('{{ route('payments.paypal.capture_order') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ orderID: data.orderID, tx: currentTx }),
                })
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        alert(@json(__('checkout.paypal_success_note')));
                        window.location.reload();
                    } else {
                        alert(result.message || @json(__('checkout.generic_error')));
                    }
                });
            },
            onError: function () {
                alert(@json(__('checkout.generic_error')));
            },
        });

        if (buttons.isEligible()) {
            buttons.render('#wallet-paypal-button-container');
        } else {
            container.innerHTML = '<p class="text-sm text-amber-600 dark:text-amber-400 text-center">' + @json(__('checkout.generic_error')) + '</p>';
        }
    }

    function setCryptoMethod(method, btn) {
        cryptoMethod = method;
        document.querySelectorAll('.crypto-method-btn').forEach(b => {
            b.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-500/10', 'text-blue-700');
        });
        btn.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-500/10', 'text-blue-700');
    }

    function handleDepositSubmit() {
        const amount = document.getElementById('customAmount').value;
        if (!amount || amount < 10000) {
            alert('Vui lòng nhập số tiền hợp lệ (Tối thiểu 10,000đ)');
            return;
        }

        if (depositMethod === 'bank') {
            generateQR();
        } else {
            payWalletWithCrypto(amount, cryptoMethod);
        }
    }

    let walletCryptoPollTimer = null;

    function copyToClipboard(elementId, btnEl) {
        const text = document.getElementById(elementId).textContent.trim();
        navigator.clipboard.writeText(text).then(() => {
            const icon = btnEl.querySelector('i');
            const original = icon.className;
            icon.className = 'fa-solid fa-check text-emerald-500';
            setTimeout(() => { icon.className = original; }, 1500);
        });
    }

    // Tên mạng lưới thực tế của từng mã pay_currency mà NOWPayments trả về — bắt buộc phải ghi rõ,
    // vì gửi crypto sai mạng (vd: USDT ERC20 thay vì TRC20) sẽ mất tiền vĩnh viễn, không hoàn lại được.
    const cryptoNetworkNames = {
        btc: 'Bitcoin',
        eth: 'Ethereum (ERC20)',
        ltc: 'Litecoin',
        usdttrc20: 'USDT · TRC20 (Tron)',
        sol: 'Solana',
    };

    function networkWarningText(payCurrency) {
        const network = cryptoNetworkNames[payCurrency] || payCurrency.toUpperCase();
        return @json(__('checkout.crypto_network_warning', ['network' => '%NETWORK%'])).replace('%NETWORK%', network);
    }

    function payWalletWithCrypto(amount, method) {
        document.getElementById('depositForm').classList.add('hidden');
        document.getElementById('cryptoWaitArea').classList.remove('hidden');
        document.getElementById('cryptoWaitLoading').classList.remove('hidden');
        document.getElementById('cryptoWaitContent').classList.add('hidden');
        document.getElementById('cryptoWaitError').classList.add('hidden');

        fetch('{{ route('payments.nowpayments.pay') }}?amount=' + amount, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ method: method }),
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('cryptoWaitLoading').classList.add('hidden');
            if (!data.success) {
                document.getElementById('cryptoWaitError').classList.remove('hidden');
                document.getElementById('cryptoWaitError').querySelector('p').textContent = data.message || 'Có lỗi xảy ra.';
                return;
            }
            document.getElementById('cryptoWaitContent').classList.remove('hidden');
            document.getElementById('cryptoWaitAmount').textContent = data.pay_amount + ' ' + data.pay_currency.toUpperCase();
            document.getElementById('cryptoWaitAddress').textContent = data.pay_address;
            document.getElementById('cryptoWaitQr').src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(data.pay_address);
            document.getElementById('cryptoWaitNetwork').innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-1"></i> ' + networkWarningText(data.pay_currency);

            const minNote = document.getElementById('cryptoWaitMinNote');
            if (data.min_bumped) {
                const extraUsd = Number(data.charged_amount_usd) - Number(data.order_amount_usd);
                document.getElementById('cryptoWaitMinRequiredText').textContent =
                    @json(__('checkout.crypto_min_required_short', ['order' => '%ORDER%', 'charged' => '%CHARGED%']))
                        .replace('%ORDER%', '$' + Number(data.order_amount_usd).toFixed(2))
                        .replace('%CHARGED%', '$' + Number(data.charged_amount_usd).toFixed(2));
                document.getElementById('cryptoWaitMinExtra').textContent = '$' + extraUsd.toFixed(2);
                minNote.classList.remove('hidden');
            } else {
                minNote.classList.add('hidden');
            }

            if (walletCryptoPollTimer) clearInterval(walletCryptoPollTimer);
            walletCryptoPollTimer = setInterval(() => {
                fetch('{{ url('/payments/nowpayments/status') }}/' + data.transaction_id, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(s => {
                        if (s.status === 'completed') {
                            clearInterval(walletCryptoPollTimer);
                            window.location.reload();
                        } else if (s.status === 'failed' || s.status === 'cancelled') {
                            clearInterval(walletCryptoPollTimer);
                            document.getElementById('cryptoWaitContent').classList.add('hidden');
                            document.getElementById('cryptoWaitError').classList.remove('hidden');
                            document.getElementById('cryptoWaitError').querySelector('p').textContent = 'Giao dịch thất bại hoặc đã hết hạn.';
                        }
                    });
            }, 5000);
        })
        .catch(() => {
            document.getElementById('cryptoWaitLoading').classList.add('hidden');
            document.getElementById('cryptoWaitError').classList.remove('hidden');
            document.getElementById('cryptoWaitError').querySelector('p').textContent = 'Không thể kết nối tới máy chủ thanh toán.';
        });
    }

    function setAmount(amount, btn) {
        document.getElementById('customAmount').value = amount;
        
        let btns = document.querySelectorAll('.amount-btn');
        btns.forEach(b => {
            b.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-600', 'active');
        });
        
        if (btn) {
            btn.classList.add('active');
        }
    }
    
    let countdownInterval = null;

    async function generateQR() {
        const amount = document.getElementById('customAmount').value;
        if(!amount || amount < 10000) {
            alert('Vui lòng nhập số tiền hợp lệ (Tối thiểu 10,000đ)');
            return;
        }

        const btn = document.getElementById('btnGenerate');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> ĐANG TẠO HOÁ ĐƠN...';
        btn.disabled = true;

        try {
            // Call API to create pending transaction invoice
            const response = await fetch('{{ route("wallet.deposit.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ amount: amount })
            });

            const data = await response.json();
            
            if (data.success) {
                // Generate QR based on Admin settings
                const bankId = '{{ $bankId }}';
                const accountNo = '{{ $accountNo }}';
                const accountName = '{{ $accountName }}';
                const template = 'compact';
                const memo = 'NAPTIEN {{ Auth::id() }}';
                
                const qrUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-${template}.png?amount=${amount}&addInfo=${memo}&accountName=${encodeURIComponent(accountName)}`;
                
                document.getElementById('qrImage').src = qrUrl;
                document.getElementById('qrAmount').innerText = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
                
                document.getElementById('depositForm').classList.add('hidden');
                document.getElementById('qrArea').classList.remove('hidden');

                startCountdown();
            } else {
                alert('Có lỗi xảy ra khi tạo hoá đơn. Vui lòng thử lại.');
            }
        } catch (error) {
            alert('Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng.');
        } finally {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    }

    function startCountdown() {
        if (countdownInterval) clearInterval(countdownInterval);
        
        let timeRemaining = 60 * 60 - 1; // 59 minutes 59 seconds
        const display = document.getElementById('countdown');
        
        countdownInterval = setInterval(() => {
            let minutes = parseInt(timeRemaining / 60, 10);
            let seconds = parseInt(timeRemaining % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timeRemaining < 0) {
                clearInterval(countdownInterval);
                display.textContent = "HẾT HẠN";
                alert('Hoá đơn đã hết hạn. Vui lòng tải lại trang để tạo hoá đơn mới.');
                window.location.reload();
            }
        }, 1000);
    }

    function resetForm() {
        if (!confirm('Bạn có chắc chắn muốn huỷ hoá đơn hiện tại để tạo mới?')) return;

        if (countdownInterval) clearInterval(countdownInterval);
        if (walletCryptoPollTimer) clearInterval(walletCryptoPollTimer);
        document.getElementById('depositForm').classList.remove('hidden');
        document.getElementById('qrArea').classList.add('hidden');
        document.getElementById('cryptoWaitArea').classList.add('hidden');
        window.location.reload(); // Reload to fetch updated pending status
    }
    
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

    function continuePayment(amount) {
        document.getElementById('customAmount').value = amount;
        generateQR();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function cancelTx(id) {
        if (confirm('Bạn có chắc muốn hủy giao dịch nạp tiền này?')) {
            fetch(`/wallet/transaction/${id}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            });
        }
    }

    // Đồng bộ nút/PayPal popup theo đúng phương thức đang chọn sẵn (Ngân hàng hoặc PayPal, tuỳ tiền tệ site).
    document.addEventListener('DOMContentLoaded', function () {
        const activeBtn = document.getElementById('method-' + depositMethod);
        if (activeBtn) setDepositMethod(depositMethod, activeBtn);
    });
</script>
@endpush
