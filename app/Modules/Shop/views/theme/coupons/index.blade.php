@extends('theme::layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">{{ __('promo.page_title') }}</h1>
            <p class="text-slate-500">{{ __('promo.page_subtitle') }}</p>
        </div>
        <div>
            <a href="{{ route('coupons.my') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors shadow-sm">
                <i class="fa-solid fa-ticket mr-2 text-blue-500"></i> {{ __('promo.my_coupons_link') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($coupons as $coupon)
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-shadow relative">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>
                
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="inline-block px-3 py-1 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-bold rounded-lg text-sm border border-blue-200 dark:border-blue-500/20 uppercase tracking-wider">
                            {{ $coupon->code }}
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-500 block mb-1">{{ __('promo.discount_rate_label') }}</span>
                            @if($coupon->discount_type == 'fixed')
                                <span class="text-xl font-bold text-emerald-500">-{{ number_format($coupon->discount_value) }}đ</span>
                            @else
                                <span class="text-xl font-bold text-purple-500">-{{ $coupon->discount_value }}%</span>
                            @endif
                        </div>
                    </div>
                    
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2">{{ $coupon->description ?? __('promo.default_description') }}</h3>

                    <ul class="text-sm text-slate-500 dark:text-slate-400 space-y-2 mb-6">
                        @if($coupon->min_order_amount > 0)
                            <li><i class="fa-solid fa-cart-shopping w-5 text-center opacity-70"></i> {{ __('mycoupons.min_order_prefix') }}: <span class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($coupon->min_order_amount) }}đ</span></li>
                        @endif
                        @if($coupon->discount_type == 'percent' && $coupon->max_discount_amount)
                            <li><i class="fa-solid fa-arrow-down-wide-short w-5 text-center opacity-70"></i> {{ __('promo.max_discount_prefix') }}: <span class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($coupon->max_discount_amount) }}đ</span></li>
                        @endif
                        @if($coupon->valid_until)
                            <li><i class="fa-solid fa-clock w-5 text-center opacity-70"></i> {{ __('mycoupons.expiry_prefix') }}: <span class="font-medium text-red-500">{{ $coupon->valid_until->format('d/m/Y H:i') }}</span></li>
                        @endif
                        @if($coupon->usage_limit)
                            <li>
                                <i class="fa-solid fa-users w-5 text-center opacity-70"></i> {{ __('promo.used_count_prefix') }}:
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $coupon->used_count }}/{{ $coupon->usage_limit }}</span>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full mt-1.5 overflow-hidden">
                                    <div class="bg-blue-500 h-full rounded-full" style="width: {{ ($coupon->used_count / $coupon->usage_limit) * 100 }}%"></div>
                                </div>
                            </li>
                        @endif
                    </ul>
                    
                    @if(in_array($coupon->id, $savedCouponIds))
                        <button disabled class="w-full py-3 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold rounded-xl border border-emerald-200 dark:border-emerald-500/20 cursor-not-allowed">
                            <i class="fa-solid fa-check mr-1"></i> {{ __('promo.saved_label') }}
                        </button>
                    @else
                        <button onclick="saveCoupon({{ $coupon->id }}, this)" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition-colors shadow-blue-500/30">
                            {{ __('promo.save_now_button') }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 dark:text-slate-500 text-4xl">
                    <i class="fa-solid fa-ticket-simple"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ __('promo.empty_title') }}</h3>
                <p class="text-slate-500">{{ __('promo.empty_subtitle') }}</p>
            </div>
        @endforelse
    </div>
</div>

<script>
function saveCoupon(couponId, btnElement) {
    if(!{{ Auth::check() ? 'true' : 'false' }}) {
        alert(@json(__('promo.login_required_alert')));
        window.location.href = "{{ route('login') }}";
        return;
    }

    let originalHtml = btnElement.innerHTML;
    btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + @json(__('promo.saving_text'));
    btnElement.disabled = true;

    fetch(`{{ url('/coupons/save') }}/${couponId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            btnElement.className = "w-full py-3 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold rounded-xl border border-emerald-200 dark:border-emerald-500/20 cursor-not-allowed";
            btnElement.innerHTML = '<i class="fa-solid fa-check mr-1"></i> ' + @json(__('promo.saved_label'));
            alert(data.message);
        } else {
            alert(data.message);
            btnElement.innerHTML = originalHtml;
            btnElement.disabled = false;
        }
    })
    .catch(err => {
        alert(@json(__('promo.generic_error_alert')));
        btnElement.innerHTML = originalHtml;
        btnElement.disabled = false;
    });
}
</script>
@endsection
