<script>
(function () {
    var ADMIN_PREFIX = '{{ config('app.admin_prefix', 'admin') }}';
    var SW_URL = '/sw.js';
    var SCOPE = '/' + ADMIN_PREFIX + '/';

    function urlBase64ToUint8Array(base64String) {
        var padding = '='.repeat((4 - base64String.length % 4) % 4);
        var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        var rawData = window.atob(base64);
        var outputArray = new Uint8Array(rawData.length);
        for (var i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function updateBellIcon() {
        var icon = document.getElementById('push-notif-icon');
        if (!icon) return;
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            icon.textContent = 'notifications_off';
            return;
        }
        navigator.serviceWorker.getRegistration(SCOPE).then(function (reg) {
            if (!reg) { icon.textContent = 'notifications'; return; }
            reg.pushManager.getSubscription().then(function (sub) {
                icon.style.color = sub ? '#2563eb' : '';
                icon.textContent = sub ? 'notifications_active' : 'notifications';
            });
        });
    }

    function notify(type, msg) {
        if (typeof showPopup === 'function') { showPopup(type, msg); }
    }

    window.togglePushNotification = function () {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            notify('error', 'Trình duyệt này không hỗ trợ thông báo đẩy.');
            return;
        }

        navigator.serviceWorker.register(SW_URL, { scope: SCOPE }).then(function (reg) {
            reg.pushManager.getSubscription().then(function (existing) {
                if (existing) {
                    fetch('/' + ADMIN_PREFIX + '/push/unsubscribe', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                        body: JSON.stringify({ endpoint: existing.endpoint }),
                    }).finally(function () {
                        existing.unsubscribe().then(updateBellIcon);
                        notify('info', 'Đã tắt thông báo đẩy trên thiết bị này.');
                    });
                    return;
                }

                Notification.requestPermission().then(function (permission) {
                    if (permission !== 'granted') {
                        notify('alert', 'Bạn cần cho phép thông báo để nhận nhắc đơn hàng / tài khoản mới.');
                        return;
                    }

                    fetch('/' + ADMIN_PREFIX + '/push/public-key')
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            return reg.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: urlBase64ToUint8Array(data.publicKey),
                            });
                        })
                        .then(function (sub) {
                            return fetch('/' + ADMIN_PREFIX + '/push/subscribe', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                                body: JSON.stringify(sub),
                            });
                        })
                        .then(function () {
                            updateBellIcon();
                            notify('success', 'Đã bật thông báo đẩy! Bạn sẽ nhận thông báo khi có đơn hàng hoặc tài khoản mới.');
                        })
                        .catch(function () {
                            notify('error', 'Không thể bật thông báo đẩy, vui lòng thử lại.');
                        });
                });
            });
        });
    };

    document.addEventListener('DOMContentLoaded', function () {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register(SW_URL, { scope: SCOPE }).then(updateBellIcon).catch(function () {});
        }
    });
})();
</script>
