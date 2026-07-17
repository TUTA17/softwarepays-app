(function () {
  var API_BASE = 'https://softwarepays.com/api/admin';

  var TAB_TITLES = {
    dashboard: 'Dashboard',
    orders: 'Đơn Hàng',
    customers: 'Khách Hàng',
    transactions: 'Giao Dịch',
  };

  var ORDER_FILTERS = [
    { value: 'all', label: 'Tất cả' },
    { value: 'pending_manual', label: 'Chờ xử lý' },
    { value: 'processing', label: 'Đang xử lý' },
    { value: 'sold', label: 'Đã giao' },
    { value: 'failed', label: 'Thất bại' },
  ];

  var STATUS_LABELS = {
    pending_manual: 'Chờ xử lý',
    sold: 'Đã giao',
    processing: 'Đang xử lý',
    failed: 'Thất bại',
  };

  var TX_TYPE_LABELS = {
    deposit: 'Nạp tiền',
    purchase: 'Mua hàng',
    affiliate_reward: 'Hoa hồng',
    referral_bonus: 'Thưởng giới thiệu',
  };

  var state = {
    token: null,
    adminId: null,
    manageAllOrders: false,
    tab: 'dashboard',
    fcmRegistered: false,

    dashboardLoaded: false,

    orderStatus: 'pending_manual',
    orderPage: 1,
    orderLastPage: 1,
    orderPendingCount: 0,
    orderLoading: false,
    ordersLoaded: false,
    fulfillmentMode: 'manual',

    customersSearch: '',
    customersPage: 1,
    customersLastPage: 1,
    customersLoading: false,
    customersLoaded: false,

    txMonth: '',
    txPage: 1,
    txLastPage: 1,
    txLoading: false,
    txLoaded: false,
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

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
  }

  function formatMoney(n) {
    return Number(n || 0).toLocaleString('vi-VN') + 'đ';
  }

  function formatMoneyByCurrency(n, currency) {
    if (currency === 'USD') return '$' + Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    return formatMoney(n);
  }

  function formatTime(iso) {
    if (!iso) return '-';
    var d = new Date(iso);
    if (isNaN(d.getTime())) return '-';
    var pad = function (n) { return n < 10 ? '0' + n : n; };
    return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
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

  // ---- Login / Logout ----
  function showLogin() {
    els.loginView.classList.remove('hidden');
    els.appShell.classList.add('hidden');
  }
  function showApp() {
    els.loginView.classList.add('hidden');
    els.appShell.classList.remove('hidden');
  }

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
      showApp();
      switchTab('dashboard');
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
    state.dashboardLoaded = state.ordersLoaded = state.customersLoaded = state.txLoaded = false;
    clearToken();
    showLogin();
    if (token) {
      fetch(API_BASE + '/logout', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
      }).catch(function () {});
    }
  }

  // ---- Tab switching ----
  function switchTab(tab) {
    state.tab = tab;
    els.headerTitle.textContent = TAB_TITLES[tab];

    Object.keys(els.tabViews).forEach(function (key) {
      els.tabViews[key].classList.toggle('hidden', key !== tab);
    });
    Array.prototype.forEach.call(els.tabButtons, function (btn) {
      btn.classList.toggle('active', btn.dataset.tab === tab);
    });

    if (tab === 'dashboard' && !state.dashboardLoaded) loadDashboard();
    if (tab === 'orders' && !state.ordersLoaded) resetAndLoadOrders();
    if (tab === 'customers' && !state.customersLoaded) resetAndLoadCustomers();
    if (tab === 'transactions' && !state.txLoaded) resetAndLoadTransactions();
  }

  function refreshCurrentTab() {
    if (state.tab === 'dashboard') { state.dashboardLoaded = false; loadDashboard(); }
    if (state.tab === 'orders') resetAndLoadOrders();
    if (state.tab === 'customers') resetAndLoadCustomers();
    if (state.tab === 'transactions') resetAndLoadTransactions();
  }

  // =========================================================
  // Dashboard
  // =========================================================
  function loadDashboard() {
    api('/dashboard').then(function (data) {
      state.dashboardLoaded = true;
      renderDashboard(data);
    }).catch(function (err) { showToast(err.message); });
  }

  function renderDashboard(data) {
    var s = data.stats || {};
    els.dashboardStats.innerHTML =
      statCard('Khách hàng', s.total_users) +
      statCard('Doanh thu', formatMoney(s.total_revenue)) +
      statCard('Key đã bán', s.total_keys_sold) +
      statCard('Tổng số dư ví', formatMoney(s.total_balance));

    var chart = data.chart || [];
    var max = Math.max.apply(null, chart.map(function (c) { return c.revenue; }).concat([1]));
    els.dashboardChart.innerHTML = chart.map(function (c) {
      var pct = Math.round((c.revenue / max) * 100);
      return '<div class="chart-bar-wrap" title="' + c.label + ': ' + formatMoney(c.revenue) + '">' +
        '<div class="chart-bar" style="height:' + Math.max(pct, 2) + '%"></div>' +
        '<div class="chart-label">' + c.label + '</div>' +
        '</div>';
    }).join('');

    els.dashboardTopGames.innerHTML = (data.top_games || []).map(function (g, i) {
      return miniRow((i + 1) + '. ' + escapeHtml(g.name), g.sold_count + ' đã bán');
    }).join('') || '<div class="empty-state">Chưa có dữ liệu</div>';

    els.dashboardTopUsers.innerHTML = (data.top_users || []).map(function (u, i) {
      return miniRow((i + 1) + '. ' + escapeHtml(u.name), formatMoney(u.total_deposit));
    }).join('') || '<div class="empty-state">Chưa có dữ liệu</div>';

    els.dashboardRecentTx.innerHTML = (data.recent_transactions || []).map(function (t) {
      var sign = t.type === 'deposit' ? '+' : (t.type === 'purchase' ? '-' : '');
      return miniRow(escapeHtml(t.user_name || '-') + ' &middot; ' + (TX_TYPE_LABELS[t.type] || t.type), sign + formatMoney(Math.abs(t.amount)));
    }).join('') || '<div class="empty-state">Chưa có dữ liệu</div>';
  }

  function statCard(label, value) {
    return '<div class="stat-card"><div class="stat-value">' + value + '</div><div class="stat-label">' + label + '</div></div>';
  }
  function miniRow(left, right) {
    return '<div class="mini-row"><span>' + left + '</span><span class="mini-row-right">' + right + '</span></div>';
  }

  // =========================================================
  // Orders (claim / lock / fulfill — same rules as web admin)
  // =========================================================
  function renderOrderChips() {
    els.filterChips.innerHTML = '';
    ORDER_FILTERS.forEach(function (f) {
      var btn = document.createElement('button');
      btn.className = 'filter-chip' + (state.orderStatus === f.value ? ' active' : '');
      var label = f.label;
      if (f.value === 'pending_manual' && state.orderPendingCount) label += ' (' + state.orderPendingCount + ')';
      btn.textContent = label;
      btn.onclick = function () {
        if (state.orderStatus === f.value) return;
        state.orderStatus = f.value;
        resetAndLoadOrders();
      };
      els.filterChips.appendChild(btn);
    });
  }

  // Chế độ xử lý đơn (thủ công / tự động) — cùng 1 setting với web, đổi ở đâu cũng áp dụng chung.
  function renderFulfillmentMode() {
    els.fulfillmentModeManual.classList.toggle('active', state.fulfillmentMode === 'manual');
    els.fulfillmentModeAuto.classList.toggle('active', state.fulfillmentMode === 'auto');
  }

  function toggleFulfillmentMode(mode) {
    if (state.fulfillmentMode === mode) return;
    if (mode === 'auto' && !confirm('Bật chế độ TỰ ĐỘNG: hệ thống sẽ tự gọi API mua Key thật ngay khi khách thanh toán, không qua duyệt tay. Xác nhận?')) return;

    api('/orders/toggle-mode', { method: 'POST', body: { mode: mode } })
      .then(function () {
        state.fulfillmentMode = mode;
        renderFulfillmentMode();
        showToast('Đã chuyển chế độ xử lý đơn hàng sang: ' + (mode === 'auto' ? 'Tự động' : 'Thủ công') + '.');
      })
      .catch(function (err) { showToast(err.message); });
  }

  function resetAndLoadOrders() {
    state.orderPage = 1;
    els.ordersList.innerHTML = '';
    renderOrderChips();
    loadOrders();
  }

  function loadOrders() {
    if (state.orderLoading) return;
    state.orderLoading = true;
    els.ordersLoadMore.classList.add('hidden');

    api('/orders?status=' + encodeURIComponent(state.orderStatus) + '&page=' + state.orderPage)
      .then(function (data) {
        state.ordersLoaded = true;
        state.orderPendingCount = data.pending_count || 0;
        state.orderLastPage = data.last_page || 1;
        state.manageAllOrders = !!data.can_manage_all;
        if (data.fulfillment_mode) state.fulfillmentMode = data.fulfillment_mode;
        renderFulfillmentMode();
        var items = (data.orders && data.orders.data) || [];
        renderOrderChips();
        renderOrders(items);
        els.ordersEmpty.classList.toggle('hidden', els.ordersList.children.length !== 0);
        els.ordersLoadMore.classList.toggle('hidden', state.orderPage >= state.orderLastPage);
      })
      .catch(function (err) { showToast(err.message); })
      .finally(function () { state.orderLoading = false; });
  }

  function loadMoreOrders() {
    if (state.orderPage >= state.orderLastPage) return;
    state.orderPage += 1;
    loadOrders();
  }

  function renderOrders(items) {
    items.forEach(function (order) {
      if (order.type === 'smm') {
        renderSmmOrderCard(order);
        return;
      }

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

  // Đơn MXH (Facebook/TikTok/YouTube...) không có claim/giao tay — chỉ xem thông tin và bấm
  // cập nhật trạng thái thực tế từ nhà cung cấp SMM (không có webhook tự động báo về).
  function renderSmmOrderCard(order) {
    var card = document.createElement('div');
    card.className = 'order-card';
    card.dataset.orderId = order.id;

    card.innerHTML =
      '<div class="order-card-top">' +
        '<div>' +
          '<div class="order-id">#' + order.id + '</div>' +
          '<div class="order-product">' + escapeHtml(order.product_name || 'N/A') + '</div>' +
          '<div class="order-buyer">' + escapeHtml(order.buyer_name || '-') + (order.buyer_email ? ' &middot; ' + escapeHtml(order.buyer_email) : '') + '</div>' +
        '</div>' +
        '<span class="badge">' + escapeHtml(order.status) + '</span>' +
      '</div>' +
      '<div class="order-time">' + formatTime(order.sold_at) + ' &middot; SL: ' + (order.quantity || 0) + ' &middot; ' + formatMoney(order.charge || 0) + '</div>';

    var actions = document.createElement('div');
    actions.className = 'order-actions';
    if (order.api_order_id) {
      var refreshBtn = document.createElement('button');
      refreshBtn.className = 'btn';
      refreshBtn.textContent = 'Cập nhật trạng thái';
      refreshBtn.onclick = function () { refreshSmmStatus(order.id); };
      actions.appendChild(refreshBtn);
    } else {
      var sendBtn = document.createElement('button');
      sendBtn.className = 'btn btn-primary';
      sendBtn.textContent = 'Gửi qua API';
      sendBtn.onclick = function () {
        if (!confirm('Gửi đơn này qua API nhà cung cấp SMM ngay?')) return;
        sendSmmToApi(order.id);
      };
      actions.appendChild(sendBtn);
    }
    card.appendChild(actions);

    els.ordersList.appendChild(card);
  }

  function refreshSmmStatus(id) {
    api('/orders/' + id + '/refresh-smm-status', { method: 'POST' })
      .then(function (data) {
        showToast('Đã cập nhật trạng thái đơn #' + id + ': ' + (data.status || ''));
        resetAndLoadOrders();
      })
      .catch(function (err) { showToast(err.message); });
  }

  function sendSmmToApi(id) {
    api('/orders/' + id + '/send-smm-to-api', { method: 'POST' })
      .then(function () {
        showToast('Đã gửi đơn #' + id + ' qua API thành công.');
        resetAndLoadOrders();
      })
      .catch(function (err) { showToast(err.message); });
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

  // =========================================================
  // Customers
  // =========================================================
  function resetAndLoadCustomers() {
    state.customersPage = 1;
    els.customersList.innerHTML = '';
    loadCustomers();
  }

  function loadCustomers() {
    if (state.customersLoading) return;
    state.customersLoading = true;
    els.customersLoadMore.classList.add('hidden');

    var qs = '?page=' + state.customersPage + (state.customersSearch ? '&search=' + encodeURIComponent(state.customersSearch) : '');
    api('/customers' + qs).then(function (data) {
      state.customersLoaded = true;
      state.customersLastPage = data.last_page || 1;
      var items = (data.users && data.users.data) || [];
      renderCustomers(items);
      els.customersEmpty.classList.toggle('hidden', els.customersList.children.length !== 0);
      els.customersLoadMore.classList.toggle('hidden', state.customersPage >= state.customersLastPage);
    }).catch(function (err) { showToast(err.message); })
      .finally(function () { state.customersLoading = false; });
  }

  function loadMoreCustomers() {
    if (state.customersPage >= state.customersLastPage) return;
    state.customersPage += 1;
    loadCustomers();
  }

  function renderCustomers(items) {
    items.forEach(function (u) {
      var card = document.createElement('div');
      card.className = 'order-card';
      card.innerHTML =
        '<div class="order-card-top">' +
          '<div>' +
            '<div class="order-product">' + escapeHtml(u.name || '-') + '</div>' +
            '<div class="order-buyer">' + escapeHtml(u.email || '-') + '</div>' +
          '</div>' +
        '</div>' +
        '<div class="order-time">Tham gia: ' + formatTime(u.created_at) + '</div>' +
        '<div class="order-actions">' +
          '<div class="mini-row"><span>Ví VNĐ</span><span class="mini-row-right">' + formatMoney(u.balance) + '</span></div>' +
          '<div class="mini-row"><span>Ví USD</span><span class="mini-row-right">' + formatMoneyByCurrency(u.balance_usd, 'USD') + '</span></div>' +
          '<div class="mini-row"><span>Điểm</span><span class="mini-row-right">' + (u.points || 0) + '</span></div>' +
          '<button class="btn btn-sm btn-add-balance-toggle">+ Cộng tiền thủ công</button>' +
          '<div class="add-balance-form hidden">' +
            '<div class="add-balance-row">' +
              '<input type="number" class="amount-input" placeholder="Số tiền" step="1" min="0">' +
              '<select class="currency-select"><option value="VND">VNĐ</option><option value="USD">USD</option></select>' +
            '</div>' +
            '<input type="text" class="note-input" placeholder="Ghi chú (không bắt buộc)">' +
            '<button class="btn btn-primary btn-block btn-add-balance-submit">Xác nhận cộng tiền</button>' +
          '</div>' +
        '</div>';

      var toggleBtn = card.querySelector('.btn-add-balance-toggle');
      var form = card.querySelector('.add-balance-form');
      var amountInput = card.querySelector('.amount-input');
      var currencySelect = card.querySelector('.currency-select');
      var noteInput = card.querySelector('.note-input');
      var submitBtn = card.querySelector('.btn-add-balance-submit');

      toggleBtn.onclick = function () {
        form.classList.toggle('hidden');
      };
      currencySelect.onchange = function () {
        var isUsd = currencySelect.value === 'USD';
        amountInput.step = isUsd ? '0.01' : '1';
        amountInput.placeholder = isUsd ? 'Số tiền ($)' : 'Số tiền (đ)';
      };
      submitBtn.onclick = function () {
        var amount = parseFloat(amountInput.value);
        if (!amount || amount <= 0) { showToast('Vui lòng nhập số tiền hợp lệ.'); return; }
        addCustomerBalance(u.id, amount, currencySelect.value, noteInput.value.trim());
      };

      els.customersList.appendChild(card);
    });
  }

  function addCustomerBalance(userId, amount, currency, note) {
    api('/customers/' + userId + '/add-balance', { method: 'POST', body: { amount: amount, currency: currency, note: note || null } })
      .then(function () {
        showToast('Đã cộng ' + formatMoneyByCurrency(amount, currency) + ' vào ví khách hàng.');
        resetAndLoadCustomers();
      })
      .catch(function (err) { showToast(err.message); });
  }

  var searchDebounceTimer = null;
  function onCustomersSearchInput() {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(function () {
      state.customersSearch = els.customersSearch.value.trim();
      resetAndLoadCustomers();
    }, 400);
  }

  // =========================================================
  // Transactions
  // =========================================================
  function resetAndLoadTransactions() {
    state.txPage = 1;
    els.transactionsList.innerHTML = '';
    loadTransactions();
  }

  function loadTransactions() {
    if (state.txLoading) return;
    state.txLoading = true;
    els.transactionsLoadMore.classList.add('hidden');

    var qs = '?page=' + state.txPage + (state.txMonth ? '&month=' + encodeURIComponent(state.txMonth) : '');
    api('/transactions' + qs).then(function (data) {
      state.txLoaded = true;
      state.txLastPage = data.last_page || 1;
      var items = (data.transactions && data.transactions.data) || [];
      renderTransactions(items);
      els.transactionsEmpty.classList.toggle('hidden', els.transactionsList.children.length !== 0);
      els.transactionsLoadMore.classList.toggle('hidden', state.txPage >= state.txLastPage);
    }).catch(function (err) { showToast(err.message); })
      .finally(function () { state.txLoading = false; });
  }

  function loadMoreTransactions() {
    if (state.txPage >= state.txLastPage) return;
    state.txPage += 1;
    loadTransactions();
  }

  function renderTransactions(items) {
    items.forEach(function (t) {
      var isDeposit = t.type === 'deposit';
      var sign = isDeposit ? '+' : (t.type === 'purchase' ? '-' : '');
      var card = document.createElement('div');
      card.className = 'order-card';
      card.innerHTML =
        '<div class="order-card-top">' +
          '<div>' +
            '<div class="order-product">' + escapeHtml(t.user_name || '-') + '</div>' +
            '<div class="order-buyer">' + escapeHtml(t.description || '') + '</div>' +
          '</div>' +
          '<span class="badge" style="' + (isDeposit ? 'background:#dcfce7;color:#15803d;' : 'background:#fee2e2;color:#b91c1c;') + '">' + (TX_TYPE_LABELS[t.type] || t.type) + '</span>' +
        '</div>' +
        '<div class="order-time">' + formatTime(t.created_at) + ' &middot; ' + t.status + '</div>' +
        '<div class="order-actions"><div class="mini-row"><span></span><span class="mini-row-right">' + sign + formatMoneyByCurrency(Math.abs(t.amount), t.currency) + '</span></div></div>';
      els.transactionsList.appendChild(card);
    });
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

    // Khi app đang MỞ (foreground), Android không tự hiện notification hệ thống cho sự kiện này
    // (chỉ tự hiện khi app chạy nền/đã tắt) -> phải tự xử lý ở đây, nếu không sẽ như "không đồng bộ":
    // đơn hàng mới vào nhưng admin đang mở app lại không thấy gì cả.
    PushNotifications.addListener('pushNotificationReceived', function (notification) {
      var title = (notification && notification.title) || 'Thông báo mới';
      var body = (notification && notification.body) || '';
      showToast(title + (body ? ': ' + body : ''));
      if (state.token) {
        resetAndLoadOrders();
      }
    });

    PushNotifications.addListener('pushNotificationActionPerformed', function () {
      if (state.token) {
        switchTab('orders');
        resetAndLoadOrders();
      }
    });
  }

  // ---- Boot ----
  function boot() {
    els = {
      loginView: $('login-view'),
      loginForm: $('login-form'),
      loginError: $('login-error'),
      loginSubmit: $('login-submit'),

      appShell: $('app-shell'),
      headerTitle: $('header-title'),
      refreshBtn: $('refresh-btn'),
      logoutBtn: $('logout-btn'),
      toast: $('toast'),

      tabButtons: document.querySelectorAll('.tab-btn'),
      tabViews: {
        dashboard: $('view-dashboard'),
        orders: $('view-orders'),
        customers: $('view-customers'),
        transactions: $('view-transactions'),
      },

      dashboardStats: $('dashboard-stats'),
      dashboardChart: $('dashboard-chart'),
      dashboardTopGames: $('dashboard-top-games'),
      dashboardTopUsers: $('dashboard-top-users'),
      dashboardRecentTx: $('dashboard-recent-tx'),

      fulfillmentModeManual: $('fulfillment-mode-manual'),
      fulfillmentModeAuto: $('fulfillment-mode-auto'),
      filterChips: $('filter-chips'),
      ordersList: $('orders-list'),
      ordersEmpty: $('orders-empty'),
      ordersLoadMore: $('orders-load-more'),

      customersSearch: $('customers-search'),
      customersList: $('customers-list'),
      customersEmpty: $('customers-empty'),
      customersLoadMore: $('customers-load-more'),

      transactionsMonth: $('transactions-month'),
      transactionsFilterClear: $('transactions-filter-clear'),
      transactionsList: $('transactions-list'),
      transactionsEmpty: $('transactions-empty'),
      transactionsLoadMore: $('transactions-load-more'),
    };

    els.loginForm.addEventListener('submit', handleLogin);
    els.refreshBtn.addEventListener('click', refreshCurrentTab);
    els.logoutBtn.addEventListener('click', function () {
      if (confirm('Đăng xuất khỏi app?')) doLogout();
    });

    Array.prototype.forEach.call(els.tabButtons, function (btn) {
      btn.addEventListener('click', function () { switchTab(btn.dataset.tab); });
    });

    els.fulfillmentModeManual.addEventListener('click', function () { toggleFulfillmentMode('manual'); });
    els.fulfillmentModeAuto.addEventListener('click', function () { toggleFulfillmentMode('auto'); });
    els.ordersLoadMore.addEventListener('click', loadMoreOrders);
    els.customersLoadMore.addEventListener('click', loadMoreCustomers);
    els.customersSearch.addEventListener('input', onCustomersSearchInput);
    els.transactionsLoadMore.addEventListener('click', loadMoreTransactions);
    els.transactionsMonth.addEventListener('change', function () {
      state.txMonth = els.transactionsMonth.value;
      resetAndLoadTransactions();
    });
    els.transactionsFilterClear.addEventListener('click', function () {
      els.transactionsMonth.value = '';
      state.txMonth = '';
      resetAndLoadTransactions();
    });

    getToken().then(function (token) {
      if (!token) { showLogin(); return; }
      state.token = token;
      api('/me').then(function (me) {
        state.adminId = me.id;
        state.manageAllOrders = !!me.manage_all_orders;
        showApp();
        switchTab('dashboard');
        registerPushNotifications();
      }).catch(function () {
        // api() already handles 401 by logging out; other errors just leave the login view up.
      });
    });
  }

  document.addEventListener('DOMContentLoaded', boot);
})();
