(function () {
  var API_BASE = 'https://softwarepays.com/api/admin';

  var FILTERS = [
    { value: 'pending_manual', label: 'Chờ xử lý' },
    { value: 'sold', label: 'Đã giao' },
    { value: 'processing', label: 'Đang xử lý' },
    { value: 'failed', label: 'Thất bại' },
    { value: 'all', label: 'Tất cả' },
  ];

  var STATUS_LABELS = {
    pending_manual: 'Chờ xử lý',
    sold: 'Đã giao',
    processing: 'Đang xử lý',
    failed: 'Thất bại',
  };

  var state = {
    token: null,
    adminId: null,
    manageAllOrders: false,
    status: 'pending_manual',
    page: 1,
    lastPage: 1,
    orders: [],
    pendingCount: 0,
    loading: false,
    fcmRegistered: false,
  };

  var els = {};

  function $(id) { return document.getElementById(id); }

  function plugins() {
    return (window.Capacitor && window.Capacitor.Plugins) || {};
  }

  function showToast(msg) {
    els.toast.textContent = msg;
    els.toast.classList.remove('hidden');
    setTimeout(function () { els.toast.classList.add('hidden'); }, 3000);
  }

  // ---- Token storage (Capacitor Preferences on device, localStorage fallback) ----
  function getToken() {
    var Preferences = plugins().Preferences;
    if (!Preferences) return Promise.resolve(localStorage.getItem('kg_token'));
    return Preferences.get({ key: 'kg_token' }).then(function (r) { return r.value; });
  }
  function setToken(token) {
    var Preferences = plugins().Preferences;
    if (!Preferences) { localStorage.setItem('kg_token', token); return Promise.resolve(); }
    return Preferences.set({ key: 'kg_token', value: token });
  }
  function clearToken() {
    var Preferences = plugins().Preferences;
    if (!Preferences) { localStorage.removeItem('kg_token'); return Promise.resolve(); }
    return Preferences.remove({ key: 'kg_token' });
  }

  // ---- API helper ----
  function api(path, options) {
    options = options || {};
    var headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };
    if (state.token) headers['Authorization'] = 'Bearer ' + state.token;
    if (options.headers) Object.assign(headers, options.headers);

    return fetch(API_BASE + path, {
      method: options.method || 'GET',
      headers: headers,
      body: options.body ? JSON.stringify(options.body) : undefined,
    }).then(function (res) {
      if (res.status === 401) {
        doLogout();
        throw new Error('Phiên đăng nhập hết hạn, vui lòng đăng nhập lại.');
      }
      return res.json().catch(function () { return {}; }).then(function (data) {
        if (!res.ok) throw new Error(data.message || 'Có lỗi xảy ra');
        return data;
      });
    });
  }

  // ---- View switching ----
  function showLogin() {
    els.loginView.classList.remove('hidden');
    els.ordersView.classList.add('hidden');
  }
  function showOrders() {
    els.loginView.classList.add('hidden');
    els.ordersView.classList.remove('hidden');
  }

  // ---- Login ----
  function handleLogin(e) {
    e.preventDefault();
    var email = $('login-email').value.trim();
    var password = $('login-password').value;
    els.loginError.textContent = '';
    els.loginSubmit.disabled = true;

    fetch(API_BASE + '/login', {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email, password: password }),
    }).then(function (res) {
      return res.json().then(function (data) { return { ok: res.ok, data: data }; });
    }).then(function (result) {
      if (!result.ok) throw new Error(result.data.message || 'Đăng nhập thất bại');
      state.token = result.data.token;
      state.adminId = result.data.admin.id;
      state.manageAllOrders = !!result.data.admin.manage_all_orders;
      return setToken(state.token);
    }).then(function () {
      showOrders();
      resetAndLoadOrders();
      registerPushNotifications();
    }).catch(function (err) {
      els.loginError.textContent = err.message;
    }).finally(function () {
      els.loginSubmit.disabled = false;
    });
  }

  function doLogout() {
    var token = state.token;
    state.token = null;
    state.adminId = null;
    state.manageAllOrders = false;
    clearToken();
    showLogin();
    if (token) {
      fetch(API_BASE + '/logout', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
      }).catch(function () {});
    }
  }

  // ---- Filter chips ----
  function renderChips() {
    els.filterChips.innerHTML = '';
    FILTERS.forEach(function (f) {
      var btn = document.createElement('button');
      btn.className = 'filter-chip' + (state.status === f.value ? ' active' : '');
      var label = f.label;
      if (f.value === 'pending_manual' && state.pendingCount) label += ' (' + state.pendingCount + ')';
      btn.textContent = label;
      btn.onclick = function () {
        if (state.status === f.value) return;
        state.status = f.value;
        resetAndLoadOrders();
      };
      els.filterChips.appendChild(btn);
    });
  }

  // ---- Orders list ----
  function resetAndLoadOrders() {
    state.page = 1;
    state.orders = [];
    els.ordersList.innerHTML = '';
    renderChips();
    loadOrders();
  }

  function loadOrders() {
    if (state.loading) return;
    state.loading = true;
    els.loadMoreBtn.classList.add('hidden');

    api('/orders?status=' + encodeURIComponent(state.status) + '&page=' + state.page)
      .then(function (data) {
        state.pendingCount = data.pending_count || 0;
        state.lastPage = data.last_page || 1;
        var items = (data.orders && data.orders.data) || [];
        state.orders = state.orders.concat(items);
        renderChips();
        renderOrders(items);
        els.emptyState.classList.toggle('hidden', state.orders.length !== 0);
        els.loadMoreBtn.classList.toggle('hidden', state.page >= state.lastPage);
      })
      .catch(function (err) { showToast(err.message); })
      .finally(function () { state.loading = false; });
  }

  function loadMore() {
    if (state.page >= state.lastPage) return;
    state.page += 1;
    loadOrders();
  }

  function formatTime(iso) {
    if (!iso) return '-';
    var d = new Date(iso);
    if (isNaN(d.getTime())) return '-';
    var pad = function (n) { return n < 10 ? '0' + n : n; };
    return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
  }

  function renderOrders(items) {
    items.forEach(function (order) {
      var card = document.createElement('div');
      card.className = 'order-card';
      card.dataset.orderId = order.id;

      var statusLabel = STATUS_LABELS[order.status] || order.status;
      var badgeClass = 'badge badge-' + order.status;

      var assignedToMe = order.assigned_admin_id && order.assigned_admin_id === state.adminId;
      var lockedByOther = order.assigned_admin_id && !assignedToMe;
      var canAct = !lockedByOther || state.manageAllOrders;

      var assigneeLine;
      if (assignedToMe) {
        assigneeLine = '<span class="badge" style="background:#dbeafe;color:#1e40af;">Bạn đang xử lý</span>';
      } else if (order.assigned_admin_name) {
        assigneeLine = '<span class="assignee-text">Người xử lý: ' + escapeHtml(order.assigned_admin_name) + '</span>';
      } else {
        assigneeLine = '<span class="assignee-text">Chưa nhận</span>';
      }

      card.innerHTML =
        '<div class="order-card-top">' +
          '<div>' +
            '<div class="order-id">#' + order.id + '</div>' +
            '<div class="order-product">' + escapeHtml(order.product_name || 'N/A') + '</div>' +
            '<div class="order-buyer">' + escapeHtml(order.buyer_name || '-') + (order.buyer_email ? ' &middot; ' + escapeHtml(order.buyer_email) : '') + '</div>' +
          '</div>' +
          '<span class="' + badgeClass + '">' + statusLabel + '</span>' +
        '</div>' +
        '<div class="order-time">' + formatTime(order.sold_at) + ' &middot; ' + assigneeLine + '</div>';

      var actions = document.createElement('div');
      actions.className = 'order-actions';

      if (order.status === 'pending_manual') {
        if (!order.assigned_admin_id) {
          var claimBtn = document.createElement('button');
          claimBtn.className = 'btn btn-primary';
          claimBtn.textContent = 'Nhận đơn';
          claimBtn.onclick = function () { claimOrder(order.id); };
          actions.appendChild(claimBtn);
        } else if (lockedByOther && !state.manageAllOrders) {
          var lockedMsg = document.createElement('div');
          lockedMsg.className = 'assignee-text';
          lockedMsg.textContent = 'Đơn đã được ' + (order.assigned_admin_name || 'người khác') + ' nhận xử lý.';
          actions.appendChild(lockedMsg);
        }

        if (canAct && order.assigned_admin_id) {
          var form = document.createElement('div');
          form.innerHTML =
            '<input type="text" placeholder="Nhập key/nội dung giao cho khách" class="key-input">' +
            '<input type="text" placeholder="Ghi chú (không bắt buộc)" class="note-input">' +
            '<div class="order-actions-row">' +
              '<button class="btn btn-primary btn-fulfill-manual">Giao tay</button>' +
              '<button class="btn btn-fulfill-api">Lấy key qua API</button>' +
            '</div>';
          actions.appendChild(form);

          form.querySelector('.btn-fulfill-manual').onclick = function () {
            var keyCode = form.querySelector('.key-input').value.trim();
            var note = form.querySelector('.note-input').value.trim();
            if (!keyCode) { showToast('Vui lòng nhập key trước.'); return; }
            fulfillManual(order.id, keyCode, note);
          };
          form.querySelector('.btn-fulfill-api').onclick = function () {
            if (!confirm('Gọi API nhà cung cấp để mua key thật cho đơn này?')) return;
            fulfillViaApi(order.id);
          };
        }

        if (lockedByOther && state.manageAllOrders) {
          var releaseBtn = document.createElement('button');
          releaseBtn.className = 'btn btn-sm';
          releaseBtn.textContent = 'Bỏ nhận';
          releaseBtn.onclick = function () { releaseOrder(order.id); };
          actions.appendChild(releaseBtn);
        }
      } else if (order.key_code) {
        var keyDiv = document.createElement('div');
        keyDiv.className = 'order-key';
        keyDiv.textContent = order.key_code;
        actions.appendChild(keyDiv);
        if (order.note) {
          var noteDiv = document.createElement('div');
          noteDiv.className = 'assignee-text';
          noteDiv.textContent = 'Ghi chú: ' + order.note;
          actions.appendChild(noteDiv);
        }
      }
      if (order.status === 'failed' && order.error_message) {
        var errDiv = document.createElement('div');
        errDiv.className = 'error-text';
        errDiv.textContent = order.error_message;
        actions.appendChild(errDiv);
      }

      card.appendChild(actions);
      els.ordersList.appendChild(card);
    });
  }

  function fulfillManual(id, keyCode, note) {
    api('/orders/' + id + '/fulfill-manual', { method: 'POST', body: { key_code: keyCode, note: note || null } })
      .then(function () {
        showToast('Đã giao key thủ công cho đơn #' + id + '.');
        resetAndLoadOrders();
      })
      .catch(function (err) { showToast(err.message); });
  }

  function fulfillViaApi(id) {
    api('/orders/' + id + '/fulfill-api', { method: 'POST' })
      .then(function () {
        showToast('Đã lấy key qua API thành công cho đơn #' + id + '.');
        resetAndLoadOrders();
      })
      .catch(function (err) { showToast(err.message); });
  }

  function claimOrder(id) {
    api('/orders/' + id + '/claim', { method: 'POST' })
      .then(function () {
        showToast('Bạn đã nhận xử lý đơn #' + id + '.');
        resetAndLoadOrders();
      })
      .catch(function (err) { showToast(err.message); });
  }

  function releaseOrder(id) {
    api('/orders/' + id + '/release', { method: 'POST' })
      .then(function () {
        showToast('Đã bỏ nhận đơn #' + id + '.');
        resetAndLoadOrders();
      })
      .catch(function (err) { showToast(err.message); });
  }

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
  }

  // ---- Push notifications (FCM via Capacitor native bridge) ----
  function registerPushNotifications() {
    if (state.fcmRegistered) return;
    var PushNotifications = plugins().PushNotifications;
    if (!PushNotifications) return;
    state.fcmRegistered = true;

    PushNotifications.checkPermissions().then(function (result) {
      if (result.receive === 'granted') return result;
      return PushNotifications.requestPermissions();
    }).then(function (result) {
      if (result.receive !== 'granted') return;
      PushNotifications.register();
    });

    PushNotifications.addListener('registration', function (token) {
      api('/push/fcm-token', { method: 'POST', body: { fcm_token: token.value } }).catch(function () {});
    });

    PushNotifications.addListener('registrationError', function (err) {
      console.warn('FCM registration error', err);
    });

    PushNotifications.addListener('pushNotificationActionPerformed', function () {
      if (state.token) resetAndLoadOrders();
    });
  }

  // ---- Boot ----
  function boot() {
    els = {
      loginView: $('login-view'),
      ordersView: $('orders-view'),
      loginForm: $('login-form'),
      loginError: $('login-error'),
      loginSubmit: $('login-submit'),
      filterChips: $('filter-chips'),
      ordersList: $('orders-list'),
      emptyState: $('empty-state'),
      loadMoreBtn: $('load-more-btn'),
      refreshBtn: $('refresh-btn'),
      logoutBtn: $('logout-btn'),
      toast: $('toast'),
    };

    els.loginForm.addEventListener('submit', handleLogin);
    els.refreshBtn.addEventListener('click', resetAndLoadOrders);
    els.logoutBtn.addEventListener('click', function () {
      if (confirm('Đăng xuất khỏi app?')) doLogout();
    });
    els.loadMoreBtn.addEventListener('click', loadMore);

    getToken().then(function (token) {
      if (!token) { showLogin(); return; }
      state.token = token;
      api('/me').then(function (me) {
        state.adminId = me.id;
        state.manageAllOrders = !!me.manage_all_orders;
        showOrders();
        resetAndLoadOrders();
        registerPushNotifications();
      }).catch(function () {
        // api() already handles 401 by logging out; other errors just leave the login view up.
      });
    });
  }

  document.addEventListener('DOMContentLoaded', boot);
})();
