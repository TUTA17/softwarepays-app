<script>
(function () {
    var ADMIN_PREFIX = '{{ config('app.admin_prefix', 'admin') }}';

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function registerToken(token) {
        fetch('/' + ADMIN_PREFIX + '/push/fcm-token', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ fcm_token: token }),
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (!window.Capacitor || !window.Capacitor.isNativePlatform || !window.Capacitor.isNativePlatform()) {
            return;
        }

        var PushNotifications = window.Capacitor.Plugins && window.Capacitor.Plugins.PushNotifications;
        if (!PushNotifications) return;

        PushNotifications.checkPermissions().then(function (result) {
            if (result.receive === 'granted') return Promise.resolve(result);
            return PushNotifications.requestPermissions();
        }).then(function (result) {
            if (result.receive !== 'granted') return;
            PushNotifications.register();
        });

        PushNotifications.addListener('registration', function (token) {
            registerToken(token.value);
        });

        PushNotifications.addListener('registrationError', function (err) {
            console.warn('FCM registration error', err);
        });

        PushNotifications.addListener('pushNotificationActionPerformed', function (action) {
            var url = action.notification && action.notification.data && action.notification.data.url;
            if (url) window.location.href = url;
        });
    });
})();
</script>
