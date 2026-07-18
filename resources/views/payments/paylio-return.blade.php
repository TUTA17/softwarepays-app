<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $success ? 'Thanh toán thành công' : 'Thanh toán chưa hoàn tất' }}</title>
<style>
    body { font-family: system-ui, -apple-system, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #0f172a; color: #f1f5f9; }
    .box { text-align: center; padding: 2rem; max-width: 380px; }
    .icon { font-size: 3rem; margin-bottom: 1rem; }
    p { color: #94a3b8; }
    a { color: #60a5fa; }
</style>
</head>
<body>
<div class="box">
    <div class="icon">{{ $success ? '✅' : '⚠️' }}</div>
    <h2>{{ $success ? 'Thanh toán thành công!' : 'Thanh toán chưa hoàn tất' }}</h2>
    <p>{{ $message }}</p>
    <p id="fallback" style="display:none;"><a href="{{ $fallbackUrl }}">Bấm vào đây để tiếp tục</a></p>
</div>
<script>
    // Cùng domain với trang mở popup -> có thể gọi thẳng window.opener.paylioPaymentCompleted()
    // để trang chính (checkout/ví) tự xử lý tiếp mà không cần polling.
    if (window.opener && !window.opener.closed && typeof window.opener.paylioPaymentCompleted === 'function') {
        try {
            window.opener.paylioPaymentCompleted({{ $success ? 'true' : 'false' }}, {!! json_encode($message) !!});
            window.close();
        } catch (e) {}
        setTimeout(function () { document.getElementById('fallback').style.display = 'block'; }, 600);
    } else {
        document.getElementById('fallback').style.display = 'block';
        @if($success)
        window.location.href = {!! json_encode($fallbackUrl) !!};
        @endif
    }
</script>
</body>
</html>
