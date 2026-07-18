@extends('theme::layouts.app')

@section('title', __('checkout.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="mb-8 border-b border-slate-200 dark:border-slate-800 pb-4">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">{{ __('checkout.title') }}</h1>
        <p class="text-slate-500 mt-1">{{ __('checkout.subtitle') }}</p>
    </div>

    <!-- Error/Success Messages -->
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-md">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 rounded-r-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Cột Trái: Thông tin & Thanh toán -->
        <div class="w-full lg:w-3/5 space-y-6">
            
            <!-- Box 1: Thông tin người mua -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user text-blue-500"></i> {{ __('checkout.buyer_info_heading') }}
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('checkout.account_name_label') }}</label>
                        <input type="text" readonly value="{{ auth()->user()->name }}" class="w-full bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-600 dark:text-slate-400 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('checkout.email_label') }}</label>
                        <input type="text" readonly value="{{ auth()->user()->email }}" class="w-full bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-600 dark:text-slate-400 cursor-not-allowed">
                        <p class="text-xs text-slate-500 mt-1">{{ __('checkout.email_delivery_note') }}</p>
                    </div>
                </div>
            </div>

            <!-- Box 2: Phương thức thanh toán -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-blue-500"></i> {{ __('checkout.payment_method_legend') }}
                </h2>

                @php
                    $isVndCurrency = session('currency', 'VND') === 'VND';
                    $displayCurrency = session('currency', 'VND');
                    $displayRate = \App\Helpers\CurrencyHelper::rate($displayCurrency);

                    // Khách Việt Nam thấy nội địa + crypto; các nước khác chỉ thấy crypto —
                    // tách theo quốc gia thực (session('geo_country'), độc lập với tiền tệ/ngôn ngữ khách tự đổi tay).
                    $isVietnam = session('geo_country') === 'VN';

                    $domesticMethods = [
                        'wallet' => ['label' => __('checkout.wallet_short_label') . ' (USD)', 'desc' => __('wallet.balance_label') . ': ' . \App\Helpers\CurrencyHelper::formatWalletBalance(auth()->user()->balance)],
                        'momo' => ['label' => __('checkout.momo_label'), 'desc' => __('checkout.vietqr_transfer_desc')],
                        'zalopay' => ['label' => __('checkout.zalopay_label'), 'desc' => __('checkout.vietqr_transfer_desc')],
                        'vnpay' => ['label' => __('checkout.vnpay_label'), 'desc' => __('checkout.vietqr_transfer_desc')],
                        'vietqr' => ['label' => __('checkout.vietqr_label'), 'desc' => __('checkout.vietqr_transfer_desc')],
                        'napas' => ['label' => __('checkout.napas_label'), 'desc' => __('checkout.vietqr_transfer_desc')],
                    ];

                    $cryptoMethods = [
                        'bitcoin' => __('checkout.btc_label'),
                        'ethereum' => 'Ethereum',
                        'litecoin' => 'Litecoin',
                        'usdt' => __('checkout.usdt_label'),
                        'solana' => 'Solana',
                    ];

                    // Paylio: khách trả bằng thẻ/PayPal/Stripe/Klarna... qua trang hosted của Paylio,
                    // tiền về ví USDC (Polygon) của mình — thêm song song với crypto (NOWPayments),
                    // mọi khách (VN hay không) đều thấy cùng danh sách phương thức quốc tế này.
                    $intlMethods = $cryptoMethods + ['paylio' => __('checkout.paylio_label')];
                    $intlShortLabels = $cryptoMethods + ['paylio' => __('checkout.paylio_short_label')];

                    $defaultMethod = $isVietnam ? 'wallet' : 'bitcoin';
                @endphp

                @if($isVietnam)
                <p class="text-xs font-bold uppercase text-slate-400 mb-2">{{ __('wallet.deposit_domestic_heading') }}</p>
                <div class="space-y-3 mb-6">
                    @foreach($domesticMethods as $key => $m)
                    @php $methodDisabled = $key !== 'wallet' && !$isVndCurrency; @endphp
                    @continue($methodDisabled)
                    <label class="payment-method-option relative flex rounded-lg border {{ $key === $defaultMethod ? 'cursor-pointer border-blue-500 bg-blue-50/50 dark:bg-blue-500/10' : 'cursor-pointer border-slate-200 dark:border-slate-700' }} p-4 shadow-sm focus:outline-none"
                           data-method="{{ $key }}" data-currency="{{ $key === 'wallet' ? 'USD' : 'VND' }}" data-fee-pct="{{ $feeConfig['fee_pct_'.$key] ?? 0 }}" data-fee-fixed="{{ $feeConfig['fee_fixed_vnd'] ?? 0 }}" data-intl="0" data-disabled="0">
                        <input type="radio" name="payment_method" value="{{ $key }}" class="sr-only payment-method-radio" {{ $key === $defaultMethod ? 'checked' : '' }}>
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-slate-900 dark:text-white">{{ $m['label'] }}</span>
                                <span class="mt-1 flex items-center text-sm text-slate-500 dark:text-slate-400">{{ $m['desc'] }}</span>
                            </span>
                        </span>
                        <i class="fa-solid fa-circle-check payment-check-icon text-blue-600 dark:text-blue-400 text-xl {{ $key === $defaultMethod ? '' : 'opacity-0' }}"></i>
                    </label>
                    @endforeach
                </div>
                @endif

                <p class="text-xs font-bold uppercase text-slate-400 mb-2">{{ __('checkout.crypto_payment_heading') }}</p>
                <div class="space-y-3">
                    @foreach($intlMethods as $key => $label)
                    @php
                        $basic = (float) ($feeConfig['intl_'.$key.'_basic_pct'] ?? 0);
                        $intlPct = (float) ($feeConfig['intl_'.$key.'_intl_pct'] ?? 0);
                        $fxPct = (float) ($feeConfig['intl_'.$key.'_fx_pct'] ?? 0);
                        $fixedUsd = (float) ($feeConfig['intl_'.$key.'_fixed_usd'] ?? 0);
                        $isDefault = $key === $defaultMethod;
                    @endphp
                    <label class="payment-method-option relative flex cursor-pointer rounded-lg border {{ $isDefault ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-500/10' : 'border-slate-200 dark:border-slate-700' }} p-4 shadow-sm"
                           data-method="{{ $key }}" data-short-label="{{ $intlShortLabels[$key] }}" data-basic-pct="{{ $basic }}" data-intl-pct="{{ $intlPct }}" data-fx-pct="{{ $fxPct }}" data-fixed-usd="{{ $fixedUsd }}" data-intl="1" data-disabled="0">
                        <input type="radio" name="payment_method" value="{{ $key }}" class="sr-only payment-method-radio" {{ $isDefault ? 'checked' : '' }}>
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-slate-900 dark:text-white">{{ $label }}</span>
                                <span class="mt-1 flex items-center text-sm text-slate-500 dark:text-slate-400">{{ __('checkout.intl_pay_ready_note') }}</span>
                                @if($key === 'paylio')
                                <span class="mt-2 flex items-center gap-2 text-slate-400 dark:text-slate-500">
                                    <i class="fa-brands fa-cc-visa text-lg" title="Visa"></i>
                                    <i class="fa-brands fa-cc-mastercard text-lg" title="Mastercard"></i>
                                    <i class="fa-brands fa-apple-pay text-xl" title="Apple Pay"></i>
                                    <i class="fa-brands fa-google-pay text-xl" title="Google Pay"></i>
                                    <i class="fa-solid fa-building-columns text-lg" title="{{ __('wallet.deposit_bank_qr_label') }}"></i>
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">Binance Pay</span>
                                </span>
                                @endif
                            </span>
                        </span>
                        <i class="fa-solid fa-circle-check payment-check-icon text-blue-600 dark:text-blue-400 text-xl {{ $isDefault ? '' : 'opacity-0' }}"></i>
                    </label>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Cột Phải: Hóa đơn & Đặt hàng -->
        <div class="w-full lg:w-2/5">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 sticky top-24">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">{{ __('checkout.summary_heading') }}</h2>
                
                <!-- Danh sách sản phẩm -->
                <div class="flow-root mb-6">
                    <ul role="list" class="-my-4 divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($cart as $id => $details)
                        <li class="flex items-center py-4">
                            <div class="h-16 w-24 flex-shrink-0 overflow-hidden rounded-md border border-slate-200 dark:border-slate-700 bg-white">
                                <img src="{{ $details['image'] }}" alt="{{ $details['name'] }}" class="h-full w-full object-contain object-center">
                            </div>
                            <div class="ml-4 flex flex-1 flex-col">
                                <div>
                                    <div class="flex flex-col sm:flex-row justify-between text-base font-medium text-slate-900 dark:text-white">
                                        <h3 class="line-clamp-2 text-sm pr-2">{{ $details['name'] }}</h3>
                                        <p class="sm:ml-4 mt-1 sm:mt-0 text-sm whitespace-nowrap text-green-600">{!! \App\Helpers\CurrencyHelper::formatPrice($details['price']) !!}</p>
                                    </div>
                                </div>
                                <div class="flex flex-1 items-end justify-between text-sm">
                                    <p class="text-slate-500 dark:text-slate-400">{{ __('checkout.quantity_short') }}: {{ $details['quantity'] }}</p>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Box Khuyến Mãi -->
                <div class="border-t border-slate-200 dark:border-slate-700 py-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('checkout.discount_code_label') }}</label>
                        <button type="button" onclick="document.getElementById('couponModal').classList.remove('hidden')" class="text-sm font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400">
                            <i class="fa-solid fa-list mr-1"></i> {{ __('checkout.choose_saved_coupon') }}
                        </button>
                    </div>
                    
                    @if($applied_coupon)
                        <div class="flex items-center justify-between p-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-lg">
                            <div>
                                <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">
                                    <i class="fa-solid fa-check-circle mr-1"></i> {{ __('checkout.coupon_applied_prefix') }}: {{ $applied_coupon->code }}
                                </p>
                                <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1">{{ $applied_coupon->description }}</p>
                            </div>
                            <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-2" title="{{ __('checkout.remove_coupon_title') }}">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="code" placeholder="{{ __('checkout.discount_code_placeholder') }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none uppercase">
                            <button type="submit" class="bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">{{ __('checkout.discount_code_apply') }}</button>
                        </form>
                    @endif
                </div>

                <!-- Tổng tiền -->
                <div class="border-t border-slate-200 dark:border-slate-700 pt-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('checkout.subtotal') }}</p>
                        <p id="summary-subtotal" class="text-sm font-medium text-slate-900 dark:text-white" data-base-vnd="{{ $total }}">{!! \App\Helpers\CurrencyHelper::formatPrice($total) !!}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('checkout.discount_amount') }}</p>
                        <p id="summary-discount" class="text-sm font-medium text-red-500" data-base-vnd="{{ $discount_amount }}">- {!! \App\Helpers\CurrencyHelper::formatPrice($discount_amount) !!}</p>
                    </div>
                    <div id="payment-fee-row" class="flex items-center justify-between hidden">
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('checkout.payment_fee_label') }}</p>
                        <p id="payment-fee-amount" class="text-sm font-medium text-slate-900 dark:text-white">0đ</p>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700">
                        <p class="text-lg font-bold text-slate-900 dark:text-white">{{ __('checkout.total') }}</p>
                        <p id="final-total-amount" class="text-2xl font-bold text-green-600 dark:text-green-400" data-base-total="{{ $final_total }}">{!! \App\Helpers\CurrencyHelper::formatPrice($final_total) !!}</p>
                    </div>
                </div>

                <!-- Nút Đặt Hàng -->
                <form id="checkout-form" action="{{ route('cart.checkout.process') }}" method="POST" class="mt-6">
                    @csrf
                    <input type="hidden" name="payment_method" id="selected_payment_method" value="{{ $defaultMethod }}">
                    @php
                        // Ví chỉ còn 1 số dư USD duy nhất -> phải so với tổng đơn ĐÃ quy đổi sang USD, không phải VNĐ gốc.
                        $userBalance = auth()->user()->balance;
                        $finalTotalUsd = round($final_total * \App\Helpers\CurrencyHelper::rate('USD'), 2);
                    @endphp
                    <div id="checkout-submit-area" data-user-balance="{{ $userBalance }}">
                        @if($isVietnam)
                            @if($userBalance >= $finalTotalUsd)
                                <button type="submit" id="checkout-submit-btn" class="w-full flex justify-center items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-4 rounded-xl text-lg font-bold shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-1">
                                    <i class="fa-solid fa-credit-card"></i>
                                    <span id="checkout-submit-label">{{ __('checkout.pay_with', ['method' => __('checkout.wallet_short_label')]) }}</span>
                                </button>
                            @else
                                <div class="p-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg mb-3">
                                    <p class="text-sm text-red-600 dark:text-red-400 text-center">{{ __('flash.wallet_insufficient') }}</p>
                                </div>
                                <a href="{{ route('wallet.show') }}" class="w-full flex justify-center items-center gap-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-6 py-4 rounded-xl text-lg font-bold transition">
                                    <i class="fa-solid fa-wallet"></i>
                                    {{ __('wallet.deposit_button') }}
                                </a>
                            @endif
                        @else
                            <div id="intl-pay-btn-placeholder"></div>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Modal Chọn Mã Giảm Giá -->
<div id="couponModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('couponModal').classList.add('hidden')"></div>

    <div class="fixed inset-0 z-[101] w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modal-title">{{ __('checkout.discount_code_label') }}</h3>
                    <button type="button" onclick="document.getElementById('couponModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-500">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="p-6 max-h-[60vh] overflow-y-auto space-y-4">
                    @forelse($saved_coupons as $coupon)
                        @php
                            $isEligible = $total >= $coupon->min_order_amount && $coupon->isValid();
                        @endphp
                        
                        <div class="border rounded-xl p-4 flex gap-4 {{ $isEligible ? 'border-blue-200 dark:border-blue-500/30 bg-blue-50/30 dark:bg-blue-500/10' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 opacity-60' }}">
                            <div class="w-16 h-16 shrink-0 rounded-lg flex flex-col items-center justify-center {{ $isEligible ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">
                                <i class="fa-solid fa-ticket text-xl mb-1"></i>
                                <span class="text-[10px] font-bold uppercase">{{ $coupon->discount_type == 'percent' ? $coupon->discount_value.'%' : \App\Helpers\CurrencyHelper::formatPrice($coupon->discount_value) }}</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-sm {{ $isEligible ? 'text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400' }}">{{ $coupon->code }}</h4>
                                <p class="text-xs text-slate-500 mt-1 mb-2 line-clamp-2">{{ $coupon->description }}</p>
                                @if(!$isEligible)
                                    <p class="text-xs text-red-500 font-medium mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ __('checkout.coupon_min_order_note', ['amount' => number_format($coupon->min_order_amount).'đ']) }}</p>
                                @endif
                            </div>
                            <div class="flex items-center justify-center">
                                @if($isEligible)
                                    <form action="{{ route('cart.coupon.apply') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="code" value="{{ $coupon->code }}">
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-sm transition">{{ __('checkout.use_button') }}</button>
                                    </form>
                                @else
                                    <button disabled class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 font-bold rounded-lg text-xs cursor-not-allowed">{{ __('checkout.use_button') }}</button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-slate-500 text-sm">{{ __('checkout.no_saved_coupons') }}</p>
                            <a href="{{ route('coupons.index') }}" class="text-blue-600 font-bold text-sm mt-2 inline-block hover:underline">{{ __('checkout.explore_promotions') }}</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thanh toán Crypto (NOWPayments) -->
<div id="cryptoModal" class="fixed inset-0 z-[100] hidden" aria-modal="true" role="dialog">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('cryptoModal').classList.add('hidden')"></div>
    <div class="fixed inset-0 z-[101] w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative rounded-2xl bg-white dark:bg-slate-800 text-center shadow-xl sm:w-full sm:max-w-md p-6">
                <button type="button" onclick="document.getElementById('cryptoModal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-500">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">{{ __('checkout.crypto_modal_title') }}</h3>
                <div id="cryptoModalLoading" class="py-10">
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-blue-500"></i>
                    <p class="mt-3 text-sm text-slate-500">{{ __('checkout.crypto_initializing') }}</p>
                </div>
                <div id="cryptoModalContent" class="hidden">
                    <p class="text-sm text-slate-500 mb-2">{{ __('checkout.crypto_send_exact_amount') }}</p>
                    <p id="cryptoPayNetwork" class="mb-3 text-sm font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg py-2 px-3"></p>
                    <div class="flex items-center justify-center gap-2 mb-3">
                        <p id="cryptoPayAmount" class="text-xl font-bold text-slate-900 dark:text-white"></p>
                        <button type="button" onclick="copyToClipboard('cryptoPayAmount', this)" class="text-slate-400 hover:text-blue-500 transition-colors" title="{{ __('wallet.pay_copy') }}">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <img id="cryptoPayQr" class="mx-auto mb-3 rounded-lg border border-slate-200 dark:border-slate-700" width="180" height="180" alt="QR code">
                    <div class="flex items-center gap-2">
                        <p id="cryptoPayAddress" class="flex-1 text-xs break-all bg-slate-100 dark:bg-slate-900 rounded-lg p-3 font-mono text-slate-700 dark:text-slate-300"></p>
                        <button type="button" onclick="copyToClipboard('cryptoPayAddress', this)" class="shrink-0 text-slate-400 hover:text-blue-500 transition-colors" title="{{ __('wallet.pay_copy') }}">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <div id="cryptoMinAmountNote" class="hidden mt-3 rounded-lg border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 p-3">
                        <p class="text-xs text-red-600 dark:text-red-400"><i class="fa-solid fa-circle-info mr-1"></i><span id="cryptoMinAmountRequiredText"></span></p>
                        <p class="mt-1 text-2xl font-black text-red-600 dark:text-red-400">+<span id="cryptoMinAmountExtra"></span></p>
                        <p class="text-xs text-red-600 dark:text-red-400">{{ __('checkout.crypto_extra_credited_label') }}</p>
                    </div>
                    <p class="mt-4 text-sm text-amber-600 dark:text-amber-400"><i class="fa-solid fa-hourglass-half mr-1"></i> {{ __('checkout.crypto_waiting_confirmation') }}</p>
                </div>
                <div id="cryptoModalError" class="hidden py-6">
                    <p class="text-sm text-red-500"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const options = document.querySelectorAll('.payment-method-option');
    const hiddenInput = document.getElementById('selected_payment_method');
    const feeRow = document.getElementById('payment-fee-row');
    const feeAmountEl = document.getElementById('payment-fee-amount');
    const finalTotalEl = document.getElementById('final-total-amount');
    const baseTotal = parseFloat(finalTotalEl.dataset.baseTotal);
    const usdRate = {{ (float) $usdRate }};
    const userBalance = parseFloat(document.getElementById('checkout-submit-area').dataset.userBalance);
    const submitArea = document.getElementById('checkout-submit-area');

    const subtotalEl = document.getElementById('summary-subtotal');
    const discountEl = document.getElementById('summary-discount');
    const subtotalVnd = parseFloat(subtotalEl.dataset.baseVnd);
    const discountVnd = parseFloat(discountEl.dataset.baseVnd);

    // Momo/zalopay/vnpay/vietqr/napas trừ thẳng từ ví USD duy nhất nhưng hiển thị giá theo đúng tiền tệ
    // khách đang chọn ở đầu trang (giống hệt cách giá sản phẩm luôn hiển thị) — không có lý do gì bắt buộc
    // phải xem bằng VNĐ chỉ vì đang chọn các phương thức này. Ví (wallet) là số dư USD thật, nên LUÔN
    // hiển thị/tính bằng USD thật, không đổi theo tiền tệ hiển thị đang chọn. Crypto cũng bắt buộc
    // đổi vì đó là tiền tệ SẼ BỊ TRỪ THẬT qua cổng thanh toán bên ngoài.
    const displayCurrency = @json($displayCurrency);
    const displayRate = {{ (float) $displayRate }};
    const currencySymbols = { VND: 'đ', USD: '$', EUR: '€', JPY: '¥', THB: '฿', CNY: '¥', KRW: '₩', RUB: '₽' };

    function resolveMethodCurrency(method, isIntl) {
        if (method === 'wallet') return { code: 'USD', rate: usdRate };
        if (!isIntl) return { code: displayCurrency, rate: displayRate };
        return { code: 'USD', rate: usdRate };
    }

    function formatByCurrency(vndAmount, code, rate) {
        if (code === 'VND') {
            return Math.round(vndAmount).toLocaleString('vi-VN') + 'đ';
        }
        const converted = vndAmount * rate;
        const decimals = code === 'JPY' ? 0 : 2;
        const symbol = currencySymbols[code] || (code + ' ');
        return symbol + converted.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
    }

    function updateSummaryCurrency(code, rate) {
        subtotalEl.textContent = formatByCurrency(subtotalVnd, code, rate);
        discountEl.textContent = '- ' + formatByCurrency(discountVnd, code, rate);
    }

    function selectMethod(el) {
        options.forEach(o => {
            o.classList.remove('border-blue-500', 'bg-blue-50/50', 'dark:bg-blue-500/10');
            o.classList.add('border-slate-200', 'dark:border-slate-700');
            o.querySelector('.payment-check-icon').classList.add('opacity-0');
            o.querySelector('.payment-method-radio').checked = false;
        });
        el.classList.remove('border-slate-200', 'dark:border-slate-700');
        el.classList.add('border-blue-500', 'bg-blue-50/50', 'dark:bg-blue-500/10');
        el.querySelector('.payment-check-icon').classList.remove('opacity-0');
        el.querySelector('.payment-method-radio').checked = true;

        const method = el.dataset.method;
        const isIntl = el.dataset.intl === '1';
        hiddenInput.value = method;

        let fee = 0;
        if (isIntl) {
            const basicPct = parseFloat(el.dataset.basicPct) || 0;
            const intlPct = parseFloat(el.dataset.intlPct) || 0;
            const fxPct = parseFloat(el.dataset.fxPct) || 0;
            const fixedUsd = parseFloat(el.dataset.fixedUsd) || 0;
            const totalUsd = baseTotal * usdRate;
            const feeUsd = ((basicPct + intlPct + fxPct) / 100) * totalUsd + fixedUsd;
            fee = feeUsd / usdRate; // quy đổi lại VNĐ để cộng vào tổng hiển thị
        } else {
            const feePct = parseFloat(el.dataset.feePct) || 0;
            const feeFixed = parseFloat(el.dataset.feeFixed) || 0;
            fee = baseTotal * feePct / 100 + feeFixed;
        }

        const { code: methodCurrency, rate: methodRate } = resolveMethodCurrency(method, isIntl);
        updateSummaryCurrency(methodCurrency, methodRate);

        if (fee > 0) {
            feeRow.classList.remove('hidden');
            feeAmountEl.textContent = formatByCurrency(fee, methodCurrency, methodRate);
        } else {
            feeRow.classList.add('hidden');
        }

        const newTotal = baseTotal + fee;
        finalTotalEl.textContent = formatByCurrency(newTotal, methodCurrency, methodRate);

        // Cập nhật nút Đặt hàng theo phương thức quốc tế: crypto mở modal chờ thanh toán thật,
        // Paylio chuyển hẳn sang trang hosted của họ (không có QR/địa chỉ ví để hiển thị tại chỗ).
        if (isIntl) {
            const label = el.dataset.shortLabel || el.querySelector('.block.text-sm').textContent.trim();
            const payWithText = @json(__('checkout.pay_with', ['method' => '%METHOD%'])).replace('%METHOD%', label);
            submitArea.innerHTML = '<button type="button" id="intl-pay-btn" class="w-full flex justify-center items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-4 rounded-xl text-lg font-bold shadow-lg transition transform hover:-translate-y-1"><i class="fa-solid fa-credit-card"></i> ' + payWithText + '</button>';
            document.getElementById('intl-pay-btn').addEventListener('click', function () {
                if (method === 'paylio') {
                    payWithPaylio();
                } else {
                    payWithCrypto(method);
                }
            });
        } else if (userBalance < (newTotal * usdRate)) {
            // Mọi phương thức nội địa đều trừ từ cùng 1 ví USD duy nhất -> luôn so sánh theo USD.
            submitArea.innerHTML = '<div class="p-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg mb-3"><p class="text-sm text-red-600 dark:text-red-400 text-center">' + @json(__('flash.wallet_insufficient')) + '</p></div>' +
                '<a href="{{ route('wallet.show') }}" class="w-full flex justify-center items-center gap-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-6 py-4 rounded-xl text-lg font-bold transition"><i class="fa-solid fa-wallet"></i> ' + @json(__('wallet.deposit_button')) + '</a>';
        } else {
            submitArea.innerHTML = '<button type="submit" class="w-full flex justify-center items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-4 rounded-xl text-lg font-bold shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-1"><i class="fa-solid fa-credit-card"></i> ' + @json(__('checkout.title')) + '</button>';
        }
    }

    options.forEach(el => {
        el.addEventListener('click', () => {
            if (el.dataset.disabled === '1') return;
            selectMethod(el);
        });
    });

    // Đồng bộ bảng tổng kết với đúng loại tiền tệ của phương thức đang được chọn sẵn (Ví) ngay khi tải trang,
    // vì Tạm tính/VAT phía trên có thể đang hiển thị theo tiền tệ hiển thị chung của site (session currency).
    const initiallySelected = document.querySelector('.payment-method-option .payment-method-radio:checked');
    if (initiallySelected) {
        selectMethod(initiallySelected.closest('.payment-method-option'));
    }

    // --- Thanh toán Crypto qua NOWPayments ---
    let cryptoPollTimer = null;

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

    // Paylio mở trong POPUP (giống các nút thanh toán khác) thay vì rời hẳn trang — trang callback
    // của Paylio (cùng domain với mình) tự gọi lại window.opener.paylioPaymentCompleted() rồi tự đóng.
    // Mở popup TRỐNG ngay lập tức (đồng bộ, trong sự kiện click) trước khi gọi fetch — nếu đợi fetch
    // xong mới window.open() thì trình duyệt sẽ chặn vì không còn coi là hành động trực tiếp của người dùng.
    function payWithPaylio() {
        const btn = document.getElementById('intl-pay-btn');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> ' + @json(__('checkout.processing_label'));

        const popup = window.open('about:blank', 'paylio_checkout', 'width=480,height=720');

        window.paylioPaymentCompleted = function (success, message) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            if (success) {
                document.getElementById('checkout-form').submit();
            } else {
                alert(message || @json(__('checkout.generic_error')));
            }
        };

        fetch('{{ route('payments.paylio.pay') }}?purpose=checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success || !data.checkout_url) {
                if (popup) popup.close();
                alert(data.message || @json(__('checkout.generic_error')));
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                return;
            }
            if (popup) {
                popup.location.href = data.checkout_url;
            } else {
                // Popup bị chặn -> rơi về hành vi cũ (chuyển hẳn trang) để không chặn luôn thanh toán.
                window.location.href = data.checkout_url;
            }
        })
        .catch(() => {
            if (popup) popup.close();
            alert(@json(__('checkout.crypto_connection_error')));
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    function payWithCrypto(method) {
        const modal = document.getElementById('cryptoModal');
        const loading = document.getElementById('cryptoModalLoading');
        const content = document.getElementById('cryptoModalContent');
        const errorBox = document.getElementById('cryptoModalError');
        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        errorBox.classList.add('hidden');

        fetch('{{ route('payments.nowpayments.pay') }}?purpose=checkout', {
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
            loading.classList.add('hidden');
            if (!data.success) {
                errorBox.classList.remove('hidden');
                errorBox.querySelector('p').textContent = data.message || @json(__('checkout.generic_error'));
                return;
            }
            content.classList.remove('hidden');
            document.getElementById('cryptoPayAmount').textContent = data.pay_amount + ' ' + data.pay_currency.toUpperCase();
            document.getElementById('cryptoPayAddress').textContent = data.pay_address;
            document.getElementById('cryptoPayQr').src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(data.pay_address);
            document.getElementById('cryptoPayNetwork').innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-1"></i> ' + networkWarningText(data.pay_currency);

            const minNote = document.getElementById('cryptoMinAmountNote');
            if (data.min_bumped) {
                const extraUsd = Number(data.charged_amount_usd) - Number(data.order_amount_usd);
                document.getElementById('cryptoMinAmountRequiredText').textContent =
                    @json(__('checkout.crypto_min_required_short', ['order' => '%ORDER%', 'charged' => '%CHARGED%']))
                        .replace('%ORDER%', '$' + Number(data.order_amount_usd).toFixed(2))
                        .replace('%CHARGED%', '$' + Number(data.charged_amount_usd).toFixed(2));
                document.getElementById('cryptoMinAmountExtra').textContent = '$' + extraUsd.toFixed(2);
                minNote.classList.remove('hidden');
            } else {
                minNote.classList.add('hidden');
            }

            if (cryptoPollTimer) clearInterval(cryptoPollTimer);
            cryptoPollTimer = setInterval(() => pollCryptoStatus(data.transaction_id), 5000);
        })
        .catch(() => {
            loading.classList.add('hidden');
            errorBox.classList.remove('hidden');
            errorBox.querySelector('p').textContent = @json(__('checkout.crypto_connection_error'));
        });
    }

    function pollCryptoStatus(transactionId) {
        fetch('{{ url('/payments/nowpayments/status') }}/' + transactionId, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'completed') {
                    clearInterval(cryptoPollTimer);
                    document.getElementById('checkout-form').submit();
                } else if (data.status === 'failed' || data.status === 'cancelled') {
                    clearInterval(cryptoPollTimer);
                    const content = document.getElementById('cryptoModalContent');
                    const errorBox = document.getElementById('cryptoModalError');
                    content.classList.add('hidden');
                    errorBox.classList.remove('hidden');
                    errorBox.querySelector('p').textContent = @json(__('checkout.crypto_failed_expired'));
                }
            });
    }

    // Tự động hoàn tất đơn hàng nếu vừa nạp ví xong quay lại trang này
    @if(session('auto_checkout'))
        document.getElementById('checkout-form').submit();
    @endif
});
</script>
@endsection
