@extends('theme::layouts.app')

@section('title', 'Chính Sách Bảo Hành - SoftwarePays')
@section('meta_description', 'Chính sách bảo hành SoftwarePays: phạm vi bảo hành, thời hạn báo cáo, cách yêu cầu bảo hành và chính sách hoàn tiền cho Key Game, phần mềm và thẻ quà tặng.')

@section('content')
@include('blog::theme.partials.auto-translate', ['autoTranslateSourceLang' => 'vi'])
<div class="relative min-h-screen py-16 overflow-hidden">
    <!-- Abstract Background -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-10%] right-[-5%] w-[40%] h-[40%] rounded-full bg-amber-500/10 mix-blend-screen blur-[100px]"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[40%] h-[40%] rounded-full bg-orange-500/10 mix-blend-screen blur-[100px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 mb-6 border border-amber-500/30 shadow-[0_0_30px_rgba(245,158,11,0.2)]">
                <i class="fa-solid fa-shield-halved text-4xl text-amber-500"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-display font-black text-slate-900 dark:text-white tracking-tight mb-4">Chính Sách Bảo Hành</h1>
            <p class="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                Cập nhật lần cuối: <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ date('d/m/Y') }}</span>
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 items-start">
            <!-- Sidebar Table of Contents -->
            <div class="lg:w-1/4 w-full sticky top-28 hidden lg:block">
                <div class="glass-card p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4 border-b border-slate-200 dark:border-slate-700 pb-4">Mục Lục</h3>
                    <nav class="space-y-3">
                        <a href="#pham-vi-bao-hanh" class="block text-slate-500 hover:text-amber-500 dark:text-slate-400 dark:hover:text-amber-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> Phạm vi bảo hành
                        </a>
                        <a href="#thoi-han-bao-hanh" class="block text-slate-500 hover:text-amber-500 dark:text-slate-400 dark:hover:text-amber-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> Thời hạn báo cáo
                        </a>
                        <a href="#quy-trinh-bao-hanh" class="block text-slate-500 hover:text-amber-500 dark:text-slate-400 dark:hover:text-amber-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> Cách yêu cầu bảo hành
                        </a>
                        <a href="#khong-bao-hanh" class="block text-slate-500 hover:text-amber-500 dark:text-slate-400 dark:hover:text-amber-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> Trường hợp không bảo hành
                        </a>
                        <a href="#hoan-tien" class="block text-slate-500 hover:text-amber-500 dark:text-slate-400 dark:hover:text-amber-400 transition-colors font-medium text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i> Chính sách hoàn tiền
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:w-3/4 w-full">
                <div class="glass-card p-8 md:p-12 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl">
                    <div class="prose prose-lg prose-slate dark:prose-invert max-w-none">

                        <p class="text-xl text-slate-600 dark:text-slate-300 leading-relaxed mb-10 font-medium">
                            Vì Key Game, phần mềm và thẻ quà tặng trên SoftwarePays là sản phẩm kỹ thuật số được giao ngay sau khi thanh toán, chính sách bảo hành dưới đây quy định rõ những trường hợp được hỗ trợ và cách yêu cầu bảo hành khi có sự cố.
                        </p>

                        <div id="pham-vi-bao-hanh" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-lg">1</span>
                                Phạm vi bảo hành
                            </h2>
                            <div class="space-y-4 text-slate-600 dark:text-slate-400 pl-4 border-l-2 border-amber-500/30">
                                <p><strong class="text-slate-800 dark:text-slate-200">Key/Code bị lỗi:</strong> Chúng tôi bảo hành khi Key hoặc mã code bị <strong>lỗi (Invalid)</strong> hoặc <strong>trùng lặp (Duplicated)</strong>, được xác nhận ngay tại thời điểm bạn kích hoạt.</p>
                                <p><strong class="text-slate-800 dark:text-slate-200">Lỗi từ hệ thống hoặc nhà phát hành:</strong> Nếu sự cố được xác minh là do lỗi từ hệ thống SoftwarePays hoặc từ nhà phát hành sản phẩm, bạn sẽ được đổi Key mới hoặc hoàn tiền tương ứng.</p>
                                <p>Chính sách này áp dụng cho mọi loại sản phẩm số: Key Game, phần mềm bản quyền, thẻ quà tặng, gói đăng ký, và các sản phẩm số khác trên SoftwarePays.</p>
                            </div>
                        </div>

                        <div id="thoi-han-bao-hanh" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 text-lg">2</span>
                                Thời hạn báo cáo
                            </h2>
                            <div class="bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 p-6 rounded-2xl text-slate-700 dark:text-slate-300">
                                <p class="mb-0">
                                    Mọi yêu cầu bảo hành phải được báo cáo cho bộ phận CSKH trong vòng <strong>24 giờ</strong> kể từ thời điểm mua hàng. Sau khoảng thời gian này, chúng tôi không thể đảm bảo hỗ trợ do dữ liệu giao dịch từ phía nhà cung cấp có thể không còn truy xuất được.
                                </p>
                            </div>
                        </div>

                        <div id="quy-trinh-bao-hanh" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-lg">3</span>
                                Cách yêu cầu bảo hành
                            </h2>
                            <div class="space-y-4 text-slate-600 dark:text-slate-400 pl-4 border-l-2 border-blue-500/30">
                                <p>Nếu key/code bạn nhận được không hoạt động, hãy làm theo các bước sau:</p>
                                <ol class="list-decimal pl-5 space-y-2">
                                    <li>Kiểm tra lại theo hướng dẫn tại <a href="/tin-tuc/game-key-already-redeemed-or-invalid" class="text-blue-600 dark:text-blue-400 underline">bài viết xử lý lỗi "Đã kích hoạt" hoặc "Không hợp lệ"</a> — nhiều trường hợp chỉ do chọn sai khu vực/nền tảng khi kích hoạt.</li>
                                    <li>Nếu vẫn lỗi, liên hệ đội ngũ hỗ trợ qua <a href="/support" class="text-blue-600 dark:text-blue-400 underline">trang Hỗ Trợ</a> trong vòng 24 giờ kể từ khi mua.</li>
                                    <li>Cung cấp mã đơn hàng, ảnh chụp lỗi khi kích hoạt, và thời gian bạn thực hiện kích hoạt để đội ngũ CSKH xác minh nhanh nhất.</li>
                                </ol>
                            </div>
                        </div>

                        <div id="khong-bao-hanh" class="mb-12">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-lg">4</span>
                                Trường hợp không được bảo hành
                            </h2>
                            <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 p-6 rounded-2xl text-slate-700 dark:text-slate-300">
                                <ul class="list-disc pl-5 space-y-2 mb-0">
                                    <li>Key/code đã được kích hoạt thành công trên tài khoản của bạn.</li>
                                    <li>Bạn đổi ý sau khi đã nhận sản phẩm, hoặc mua nhầm phiên bản/khu vực.</li>
                                    <li>Tài khoản của bạn bị khóa do vi phạm điều khoản sử dụng (chia sẻ tài khoản, gian lận thanh toán...).</li>
                                    <li>Yêu cầu được báo cáo sau thời hạn 24 giờ quy định ở Mục 2.</li>
                                </ul>
                            </div>
                        </div>

                        <div id="hoan-tien">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-lg">5</span>
                                Chính sách hoàn tiền
                            </h2>
                            <p class="text-slate-600 dark:text-slate-400 pl-4 border-l-2 border-emerald-500/30">
                                Do đặc thù sản phẩm kỹ thuật số được giao ngay lập tức, SoftwarePays <strong>không hỗ trợ hoàn trả</strong> sau khi Key đã được giao thành công vào tài khoản của bạn, <strong>trừ khi</strong> sự cố được xác minh là lỗi từ hệ thống SoftwarePays hoặc từ nhà phát hành sản phẩm — trong trường hợp đó, bạn sẽ được đổi Key mới hoặc hoàn tiền vào ví SoftwarePays.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Bottom CTA -->
                <div class="mt-12 text-center">
                    <p class="text-slate-500 dark:text-slate-400 mb-6">Còn thắc mắc về chính sách bảo hành? Đội ngũ hỗ trợ luôn sẵn sàng giúp bạn.</p>
                    <a href="{{ route('pages.support') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold transition-transform hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                        <i class="fa-solid fa-envelope"></i> Liên Hệ Hỗ Trợ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    html { scroll-behavior: smooth; }
</style>
@endsection
