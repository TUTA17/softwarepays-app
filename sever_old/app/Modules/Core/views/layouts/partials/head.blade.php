<head>
    @php
        $siteFavicon = \App\Modules\Core\Models\Setting::where('name','favicon')->where('type','general_tab')->value('value');
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ $siteFavicon ? asset($siteFavicon) : asset('auth_assets/images/logo_large.png') }}" type="image/png">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container": "#eeedf1",
                        "tertiary-fixed": "#a0f0f0",
                        "secondary-fixed": "#d1e4ff",
                        "outline": "#727783",
                        "on-error-container": "#93000a",
                        "surface-container-low": "#f4f3f6",
                        "surface-container-highest": "#e2e2e5",
                        "primary-fixed-dim": "#a8c8ff",
                        "on-tertiary-container": "#98e9e8",
                        "secondary": "#456080",
                        "on-tertiary-fixed": "#002020",
                        "on-secondary": "#ffffff",
                        "on-surface-variant": "#424752",
                        "surface-tint": "#005db5",
                        "primary-container": "#005fb8",
                        "surface-bright": "#faf9fc",
                        "on-primary": "#ffffff",
                        "surface": "#faf9fc",
                        "on-secondary-fixed": "#001d36",
                        "on-tertiary": "#ffffff",
                        "on-background": "#1a1c1e",
                        "tertiary-container": "#036b6b",
                        "on-primary-container": "#cadcff",
                        "inverse-surface": "#2f3033",
                        "tertiary": "#005151",
                        "primary": "#00488d",
                        "on-secondary-container": "#445f7f",
                        "primary-fixed": "#d6e3ff",
                        "inverse-primary": "#a8c8ff",
                        "error-container": "#ffdad6",
                        "on-primary-fixed": "#001b3d",
                        "on-secondary-fixed-variant": "#2d4867",
                        "on-primary-fixed-variant": "#00468b",
                        "surface-dim": "#dadadd",
                        "secondary-container": "#bed9ff",
                        "surface-container-lowest": "#ffffff",
                        "tertiary-fixed-dim": "#84d4d3",
                        "secondary-fixed-dim": "#adc9ed",
                        "surface-container-high": "#e8e8eb",
                        "surface-variant": "#e2e2e5",
                        "background": "#faf9fc",
                        "outline-variant": "#c2c6d4",
                        "on-surface": "#1a1c1e",
                        "on-tertiary-fixed-variant": "#004f4f",
                        "inverse-on-surface": "#f1f0f4",
                        "on-error": "#ffffff",
                        "error": "#ba1a1a"
                    },
                    fontFamily: {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    },
                    borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body { font-family: 'Inter', sans-serif; font-size: 13px; }
        h1, h2, h3, .font-headline { font-family: 'Inter', sans-serif; }
        .glass-panel {
            background: rgba(250, 249, 252, 0.7);
            backdrop-filter: blur(20px);
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e2e5; border-radius: 10px; }
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: rgba(37,99,235,0.06);
            --bg-body: #f1f5f9;
            --bg-card: #ffffff;
            --bg-card-hover: #f1f5f9;
            --bg-sidebar: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
            --info: #2563eb;
            --radius: 0.75rem;
            --shadow: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-lg: 0 4px 12px rgba(0,0,0,0.08);
            --sidebar-width: 240px;
        }

        /* === DARK THEME SUPPORT === */
        body.dark-theme {
            --bg-body: #0f172a;        /* Slate 900 */
            --bg-card: #1e293b;        /* Slate 800 */
            --bg-card-hover: #334155;  /* Slate 700 */
            --bg-sidebar: #1e293b;     /* Slate 800 */
            --text-primary: #f8fafc;   /* Slate 50 */
            --text-secondary: #cbd5e1; /* Slate 300 */
            --text-muted: #94a3b8;     /* Slate 400 */
            --border: #334155;         /* Slate 700 */
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
            color-scheme: dark;
        }
        body.dark-theme .top-header,
        body.dark-theme .dropdown-menu,
        body.dark-theme .user-dropdown,
        body.dark-theme .user-dropdown-footer,
        body.dark-theme .theme-opt.active,
        body.dark-theme .filter-section,
        body.dark-theme .status-bar,
        body.dark-theme .page-btn,
        body.dark-theme table th,
        body.dark-theme .select2-container--default .select2-selection--single,
        body.dark-theme .select2-dropdown {
            background-color: var(--bg-card);
            color: var(--text-primary);
        }
        body.dark-theme .user-dropdown-logo { background-color: var(--bg-card); border: 1px solid var(--border); }
        body.dark-theme .search-input input,
        body.dark-theme .form-control {
            background-color: var(--bg-body); color: var(--text-primary); border-color: var(--border);
        }
        body.dark-theme .form-control:focus { background-color: var(--bg-card); }
        body.dark-theme .tab-item:hover, body.dark-theme .tab-item.active { background-color: var(--bg-card); }
        body.dark-theme .theme-options { background: var(--bg-body); border-color: var(--border); }
        body.dark-theme .theme-opt.active { background: var(--bg-card); border-color: var(--border); }
        body.dark-theme table th { background: var(--bg-card); }
        body.dark-theme table tr:hover td { background: var(--bg-card-hover); }
        body.dark-theme .btn-outline { color: var(--text-primary); }
        body.dark-theme .nav-item:hover { background: var(--bg-card-hover); color: var(--text-primary); }
        body.dark-theme .nav-item.active { background: rgba(37,99,235,0.15); color: #60a5fa; border-left-color: #60a5fa; }
        body.dark-theme .nav-group.expanded .nav-group-toggle { background: rgba(37,99,235,0.15); color: #60a5fa; }
        body.dark-theme .status-item { background: var(--bg-card); }
        body.dark-theme .status-item:hover { background: var(--bg-card-hover); }
        body.dark-theme .status-item.active { background: var(--primary); }
        body.dark-theme .cs-container { background-color: var(--bg-body); }
        /* Cập nhật các màu chữ cứng */
        body.dark-theme .timeline-title, body.dark-theme .user-name, body.dark-theme .card-title,
        body.dark-theme .page-title, body.dark-theme .filter-title { color: var(--text-primary); }
        /* Native select & input styling */
        body.dark-theme select, body.dark-theme select.form-control,
        body.dark-theme input[type="date"], body.dark-theme input[type="datetime-local"] {
            background-color: var(--bg-body) !important; color: var(--text-primary) !important;
            border-color: var(--border) !important; color-scheme: dark;
        }
        body.dark-theme select option { background: var(--bg-card); color: var(--text-primary); }
        body.dark-theme .pagination-bar, body.dark-theme .pagination-controls select {
            background: var(--bg-card); color: var(--text-primary); border-color: var(--border);
        }
        body.dark-theme .image-preview { background: var(--bg-card) !important; }
        body.dark-theme .tab-nav { background: var(--bg-body) !important; }
        body.dark-theme .status-dropdown { background: var(--bg-card) !important; border-color: var(--border) !important; }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; font-size: 13px; background: var(--bg-body); color: var(--text-primary); min-height: 100vh; -webkit-font-smoothing: antialiased; transition: background-color 0.3s, color 0.3s; }
        .sidebar, .top-header, .card, .form-control, .btn { transition: background-color 0.3s, border-color 0.3s, color 0.3s; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

        /* === SIDEBAR === */
        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0;
            width: var(--sidebar-width); background: var(--bg-sidebar);
            border-right: 1px solid var(--border); z-index: 50;
            display: flex; flex-direction: column; padding-top: 16px;
            box-shadow: 1px 0 4px rgba(0,0,0,0.04);
        }
        .sidebar-header { padding: 0 20px 20px; }
        .sidebar-header .brand { display: flex; align-items: center; gap: 10px; }
        .sidebar-logo {
            width: 36px; height: 36px;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }
        .sidebar-logo img { width: 100%; height: 100%; object-fit: contain; }
        .sidebar-brand { font-size: 15px; font-weight: 800; color: #1e40af; line-height: 1.2; }
        .sidebar-brand small { display: block; font-size: 10px; font-weight: 500; color: var(--text-muted); }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 0 8px; }
        .nav-section { padding: 14px 14px 6px; }
        .nav-section-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.08em; color: var(--text-muted);
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; color: var(--text-secondary); text-decoration: none;
            font-size: 13px; font-weight: 500; transition: all 0.2s ease;
            border-radius: 0 8px 8px 0; margin-bottom: 1px; position: relative;
            border-left: 3px solid transparent; margin-left: 0;
        }
        .nav-item:hover { background: #f1f5f9; color: var(--text-primary); }
        .nav-item.active {
            background: #dbeafe;
            color: #1e40af;
            font-weight: 700;
            border-left-color: #2563eb;
            box-shadow: 0 1px 3px rgba(37,99,235,.12);
        }
        .nav-item.active .material-symbols-outlined { color: #2563eb; font-variation-settings: 'FILL' 1; }
        .nav-item .material-symbols-outlined { font-size: 20px; transition: color 0.2s; }
        .nav-badge {
            margin-left: auto; background: var(--primary); color: white;
            font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px;
        }

        /* === SIDEBAR DROPDOWN === */
        .nav-group { margin-bottom: 4px; }
        .nav-group-toggle {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 14px; cursor: pointer; transition: all 0.2s;
            color: var(--text-secondary); font-size: 13px; font-weight: 600; border-radius: 10px;
        }
        .nav-group-toggle:hover { background: var(--bg-card-hover); color: var(--text-primary); }
        .nav-group-toggle .left-box { display: flex; align-items: center; gap: 12px; }
        .nav-group-toggle .left-box .material-symbols-outlined { font-size: 20px; }
        .nav-group-toggle .arrow { font-size: 18px; transition: transform 0.2s; color: var(--text-muted); }
        .nav-group.expanded .nav-group-toggle { background: #eff6ff; color: var(--primary); }
        .nav-group.expanded .nav-group-toggle .material-symbols-outlined { font-variation-settings: 'FILL' 1; }
        .nav-group.expanded .arrow { transform: rotate(180deg); color: var(--primary); }
        .nav-group-menu { display: none; padding-left: 16px; margin-top: 4px; }
        .nav-group.expanded .nav-group-menu { display: block; }
        .nav-group-menu .nav-item { padding: 8px 14px; font-size: 13px; }
        .nav-group-menu .nav-item .material-symbols-outlined { font-size: 18px; }

        /* === MAIN === */
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }

        /* === HEADER === */
        .top-header {
            position: sticky; top: 0; z-index: 40;
            background: var(--bg-card); border-bottom: 1px solid var(--border);
            padding: 0 32px; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: var(--shadow);
        }
        .header-left { display: flex; align-items: center; gap: 16px; }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-secondary); }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb .separator { color: var(--text-muted); }
        .header-right { display: flex; align-items: center; gap: 12px; }
        .header-btn {
            width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border);
            background: transparent; color: var(--text-secondary); cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: all 0.15s;
        }
        .header-btn:hover { background: var(--bg-card-hover); color: var(--text-primary); }
        .user-menu {
            display: flex; align-items: center; gap: 10px; cursor: pointer;
            padding: 6px 12px; border-radius: 10px; transition: background 0.15s;
        }
        .user-menu:hover { background: var(--bg-card-hover); }
        .user-avatar {
            width: 32px; height: 32px; background: var(--primary);
            border-radius: 999px; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px; color: white;
        }
        .user-info { display: flex; flex-direction: column; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .user-role { font-size: 11px; color: var(--text-muted); }

        /* === PAGE CONTENT === */
        .page-content { padding: 28px 32px; max-width: 1600px; }
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .page-title { font-size: 22px; font-weight: 700; color: var(--text-primary); }
        .page-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 2px; }
        .page-badge {
            display: inline-flex; padding: 3px 10px; border-radius: 6px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.05em; background: rgba(37,99,235,0.1); color: var(--primary);
            margin-left: 12px; vertical-align: middle;
        }

        /* === CARDS === */
        .card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-sm);
        }
        .card-header {
            padding: 16px 20px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 14px; font-weight: 700; color: var(--text-primary); }
        .card-body { padding: 20px; }

        /* === BUTTONS === */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px; border: none;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: all 0.15s; text-decoration: none; font-family: 'Inter', sans-serif;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text-secondary); }
        .btn-outline:hover { background: var(--bg-card-hover); color: var(--text-primary); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-icon { padding: 8px; width: 34px; height: 34px; justify-content: center; border-radius: 8px; }

        /* === TABLE === */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table th {
            padding: 12px 16px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
            color: var(--text-muted); text-align: left;
            border-bottom: 1px solid var(--border); background: var(--bg-body);
        }
        table td {
            padding: 12px 16px; font-size: 13px;
            border-bottom: 1px solid var(--border); color: var(--text-secondary);
        }
        table tr:hover td { background: var(--bg-card-hover); }

        /* === FORMS === */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 11px; font-weight: 700;
            color: var(--text-muted); margin-bottom: 6px;
            text-transform: uppercase; letter-spacing: 0.03em;
        }
        .form-control {
            width: 100%; padding: 9px 14px;
            background: var(--bg-body); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text-primary);
            font-size: 13px; font-family: 'Inter', sans-serif; transition: all 0.15s;
        }
        .form-control:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12); background: var(--bg-card);
        }
        .form-control::placeholder { color: var(--text-muted); }
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px;
        }

        /* === BADGES === */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 600;
        }
        .badge-success { background: rgba(22,163,74,0.1); color: var(--success); }
        .badge-danger { background: rgba(220,38,38,0.1); color: var(--danger); }
        .badge-warning { background: rgba(217,119,6,0.1); color: var(--warning); }
        .badge-info { background: rgba(37,99,235,0.1); color: var(--info); }
        .badge-primary { background: rgba(37,99,235,0.1); color: var(--primary); }
        .badge-muted { background: var(--bg-body); color: var(--text-muted); }

        /* === STATS === */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px; margin-bottom: 24px;
        }
        .stat-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 20px;
            display: flex; align-items: flex-start; justify-content: space-between;
            box-shadow: var(--shadow-sm);
        }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 20px;
        }
        .stat-value { font-size: 26px; font-weight: 800; margin-top: 6px; }
        .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }

        /* === STATUS BAR (like reference) === */
        .status-bar {
            display: flex; gap: 0; margin-bottom: 20px;
            border: 1px solid var(--border); border-radius: var(--radius);
            overflow: hidden; background: var(--bg-card);
        }
        .status-item {
            padding: 10px 20px; font-size: 13px; font-weight: 600;
            border-right: 1px solid var(--border); cursor: pointer;
            transition: all 0.15s; color: var(--text-secondary);
        }
        .status-item:last-child { border-right: none; }
        .status-item:hover { background: var(--bg-card-hover); }
        .status-item.active { background: var(--primary); color: white; }
        .status-item .count { font-weight: 800; }

        /* === FILTER SECTION === */
        .filter-section {
            background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius);
            padding: 20px; margin-bottom: 20px; box-shadow: var(--shadow-sm);
        }
        .filter-header {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;
        }
        .filter-title { font-size: 14px; font-weight: 700; color: var(--text-primary); }
        .filter-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px; align-items: end;
        }
        .filter-bar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .search-input { flex: 1; min-width: 200px; position: relative; }
        .search-input input {
            width: 100%; padding: 9px 14px 9px 38px;
            background: var(--bg-body); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text-primary);
            font-size: 13px; font-family: 'Inter', sans-serif;
        }
        .search-input input:focus { outline: none; border-color: var(--primary); background: var(--bg-card); }
        .search-input i, .search-input .material-symbols-outlined {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%); color: var(--text-muted); font-size: 18px;
        }

        /* === ALERTS === */
        .alert {
            padding: 12px 16px; border-radius: 10px; margin-bottom: 16px;
            font-size: 14px; display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: rgba(22,163,74,0.1); border: 1px solid rgba(22,163,74,0.25); color: var(--success); }
        .alert-danger { background: rgba(220,38,38,0.1); border: 1px solid rgba(220,38,38,0.25); color: var(--danger); }
        .alert-warning { background: rgba(217,119,6,0.1); border: 1px solid rgba(217,119,6,0.25); color: var(--warning); }

        /* === PAGINATION === */
        .pagination {
            display: flex; align-items: center; gap: 4px;
            justify-content: center; padding: 16px; list-style: none;
        }
        .pagination li a, .pagination li span {
            padding: 8px 14px; border-radius: 8px; font-size: 13px;
            color: var(--text-secondary); text-decoration: none; border: 1px solid var(--border);
        }
        .pagination li a:hover { background: var(--bg-card-hover); }
        .pagination li.active span { background: var(--primary); color: white; border-color: var(--primary); }
        .pagination li.disabled span { opacity: 0.4; }

        /* === DROPDOWN MENU === */
        .dropdown { position: relative; }
        .dropdown-menu {
            display: none; position: absolute; top: 100%; right: 0;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; min-width: 180px; padding: 4px;
            box-shadow: var(--shadow-lg); z-index: 200;
        }
        .dropdown-menu.show { display: block; }
        .dropdown-item {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 12px; color: var(--text-secondary);
            text-decoration: none; font-size: 13px; border-radius: 8px;
        }
        .dropdown-item:hover { background: var(--bg-card-hover); color: var(--text-primary); }

        /* === USER DROPDOWN (PREMIUM) === */
        .user-dropdown {
            display: none; position: absolute; top: calc(100% + 12px); right: 0;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 12px; width: 320px; padding: 0;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.15); z-index: 200; overflow: hidden;
            transform-origin: top right;
        }
        .user-dropdown.show { display: block; animation: dropdown-anim 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes dropdown-anim { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

        .user-dropdown-header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            padding: 24px 20px; display: flex; align-items: center; gap: 14px;
            color: white; position: relative; overflow: hidden;
        }
        .user-dropdown-header::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(circle at 80% 120%, rgba(255,255,255,0.2) 0%, transparent 60%),
            linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 100% 100%, 20px 20px, 20px 20px;
            opacity: 0.8; pointer-events: none;
        }
        .user-dropdown-logo {
            width: 50px; height: 50px; background: var(--bg-card); border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); flex-shrink: 0; z-index: 1;
            overflow: hidden; padding: 4px;
        }
        .user-dropdown-logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .user-dropdown-name { font-size: 18px; font-weight: 700; z-index: 1; line-height: 1.2; }

        .user-dropdown-body { padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .user-action-item {
            display: flex; align-items: center; justify-content: space-between;
            text-decoration: none; color: var(--text-primary); transition: all 0.2s;
        }
        .user-action-item:hover .user-action-text span:first-child { color: var(--primary); }
        .user-action-left { display: flex; align-items: center; gap: 14px; }
        .user-action-icon { color: #10b981; font-size: 24px; display: flex; align-items: center; }
        .user-action-text { display: flex; flex-direction: column; }
        .user-action-text span:first-child { font-size: 14px; font-weight: 600; color: var(--text-secondary); transition: color 0.2s; }
        .user-action-text span:last-child { font-size: 12px; color: var(--text-muted); }
        .user-action-arrow { color: #cbd5e1; font-size: 20px; }

        .theme-selector { padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .theme-title { font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 12px; }
        .theme-options { display: flex; background: var(--bg-body); border-radius: 8px; overflow: hidden; padding: 4px; border: 1px solid var(--border); }
        .theme-opt {
            flex: 1; text-align: center; padding: 6px; font-size: 13px; font-weight: 600;
            color: var(--text-muted); cursor: pointer; transition: all 0.2s; border-radius: 6px;
        }
        .theme-opt.active { background: var(--bg-card); color: var(--primary); box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border); }

        .user-dropdown-footer {
            padding: 16px 20px; display: flex; align-items: center; justify-content: space-between;
            background: var(--bg-card); border-radius: 0 0 12px 12px;
        }
        .btn-logout {
            background: rgba(37,99,235,0.1); color: var(--primary); padding: 8px 16px; border-radius: 6px;
            font-size: 13px; font-weight: 700; border: none; cursor: pointer; transition: 0.2s;
            display: inline-flex; align-items: center; gap: 6px; font-family: inherit;
        }
        .btn-logout:hover { background: #dbeafe; color: #1e40af; }
        .link-settings { font-size: 13px; font-weight: 600; color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .link-settings:hover { color: var(--primary); }

        /* === TIMELINE === */
        .timeline { position: relative; padding-left: 32px; }
        .timeline::before {
            content: ''; position: absolute; left: 11px; top: 8px; bottom: 0;
            width: 2px; background: #e2e8f0;
        }
        .timeline-item { position: relative; margin-bottom: 24px; }
        .timeline-icon {
            position: absolute; left: -32px; top: 4px;
            width: 24px; height: 24px; border-radius: 999px;
            display: flex; align-items: center; justify-content: center;
            border: 3px solid white; z-index: 1;
        }
        .timeline-icon .material-symbols-outlined { font-size: 12px; font-variation-settings: 'FILL' 1; }
        .timeline-content {
            background: var(--bg-body); border-radius: 10px; padding: 16px;
        }
        .timeline-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;
        }
        .timeline-title { font-size: 14px; font-weight: 700; color: var(--text-primary); }
        .timeline-date { font-size: 11px; color: var(--text-muted); font-weight: 500; }
        .timeline-body { font-size: 13px; color: var(--text-secondary); line-height: 1.6; }
        .timeline-tags { margin-top: 10px; display: flex; gap: 6px; flex-wrap: wrap; }
        .timeline-tag {
            padding: 2px 8px; font-size: 10px; font-weight: 700;
            border-radius: 4px; text-transform: uppercase;
        }

        /* === INFO LIST (like profile card) === */
        .info-list { }
        .info-item { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border); }
        .info-item:last-child { border-bottom: none; }
        .info-item .material-symbols-outlined { color: var(--text-muted); font-size: 20px; margin-top: 2px; }
        .info-label { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; }
        .info-value { font-size: 14px; font-weight: 600; color: var(--text-primary); margin-top: 2px; }

        /* === TAB NAV === */
        .tab-nav { display: flex; border-bottom: 1px solid var(--border); background: var(--bg-body); }
        .tab-item {
            padding: 14px 24px; font-size: 13px; font-weight: 600;
            color: var(--text-muted); cursor: pointer; transition: all 0.15s;
            border-bottom: 2px solid transparent; text-decoration: none;
        }
        .tab-item:hover { color: var(--text-primary); background: var(--bg-card); }
        .tab-item.active { color: var(--primary); border-bottom-color: var(--primary); background: var(--bg-card); }

        /* === STATS CHIPS BAR (inline design) === */
        .stats-chips {
            display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;
        }
        .stats-chips .divider {
            width: 1px; height: 28px; background: var(--border); margin: 0 8px;
        }
        .stat-chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 6px;
            font-size: 13px; font-weight: 500; border: 1px solid;
        }
        .stat-chip .count { font-weight: 700; }
        .stat-chip-blue { background: #eff6ff; border-color: #bfdbfe; color: #1e3a5f; }
        .stat-chip-amber { background: #fffbeb; border-color: #fde68a; color: #78350f; }
        .stat-chip-green { background: #f0fdf4; border-color: #bbf7d0; color: #14532d; }
        .stat-chip-purple { background: #faf5ff; border-color: #e9d5ff; color: #3b0764; }
        .stat-chip-slate { background: var(--bg-body); border-color: var(--border); color: var(--text-secondary); }
        .stat-chip-indigo { background: #eef2ff; border-color: #c7d2fe; color: #312e81; }
        .stat-chip-emerald { background: #ecfdf5; border-color: #a7f3d0; color: #064e3b; }
        .stat-chip-red { background: #fef2f2; border-color: #fecaca; color: #7f1d1d; }

        /* === TABLE CHECK === */
        .table-check {
            width: 16px; height: 16px; border-radius: 4px;
            border: 1px solid #cbd5e1; cursor: pointer;
            accent-color: var(--primary);
        }

        /* === ENHANCED PAGINATION === */
        .pagination-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 20px; border-top: 1px solid var(--border); background: #fafbfc;
        }
        .pagination-info { font-size: 13px; color: var(--text-muted); }
        .pagination-info strong { color: var(--text-primary); font-weight: 700; }
        .pagination-controls { display: flex; align-items: center; gap: 6px; }
        .pagination-perpage {
            display: flex; align-items: center; gap: 8px; margin-right: 16px;
        }
        .pagination-perpage label { font-size: 12px; color: var(--text-muted); }
        .pagination-perpage select {
            padding: 4px 28px 4px 8px; border-radius: 6px; border: 1px solid var(--border);
            font-size: 12px; font-family: 'Inter', sans-serif;
            background: var(--bg-card); cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center;
        }
        .page-btn {
            width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--border); border-radius: 4px; background: var(--bg-card);
            font-size: 12px; font-weight: 700; color: var(--text-secondary);
            cursor: pointer; transition: all 0.15s; text-decoration: none;
        }
        .page-btn:hover { background: var(--bg-card-hover); }
        .page-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }
        .page-btn.disabled { opacity: 0.4; cursor: not-allowed; }

        /* === FILTER TOOLBAR === */
        .filter-toolbar {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;
        }
        .filter-actions { display: flex; align-items: center; gap: 8px; }

        .form-select.is-invalid {
            border-color: var(--danger);
        }

        /* --- Responsive --- */
        .sidebar-toggle { display: none; }
        @media (max-width: 1280px) {
            :root { --sidebar-width: 220px; }
            .nav-item { font-size: 12px; padding: 8px 12px; }
            .page-content { padding: 20px; }
        }
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); width: 260px; z-index: 1000; box-shadow: 4px 0 20px rgba(0,0,0,0.15); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: block; }
            .page-content { padding: 16px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .filter-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .top-header { padding: 0 16px; height: 56px; }
            .page-content { padding: 12px; }
            .page-title { font-size: 18px; }
            .stats-grid { grid-template-columns: 1fr; }
            .breadcrumb { font-size: 12px; }
            .user-info { display: none; }
        }
        @media (max-width: 480px) {
            .page-header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .filter-bar { flex-direction: column; }
            .search-input { min-width: 100%; }
        }

        /* Select2 override */
        .select2-container--default .select2-selection--single {
            background: var(--bg-body); border: 1px solid var(--border);
            border-radius: 8px; height: 38px; padding: 4px 8px;
            font-family: 'Inter', sans-serif; font-size: 13px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-primary); line-height: 28px; padding-left: 6px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px; right: 8px; }
        .select2-dropdown { border: 1px solid var(--border); border-radius: 10px; box-shadow: var(--shadow-lg); overflow: hidden; }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid var(--border); border-radius: 8px; padding: 8px 12px;
            font-family: 'Inter', sans-serif; font-size: 13px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] { background: var(--primary); }
        .select2-container--default .select2-results__option { padding: 8px 12px; font-size: 14px; }
        .select2-container { width: 100% !important; }

        /* === CUSTOM POPUP NOTIFICATIONS === */
        #global-popup-container {
            position: fixed; top: 20px; right: 20px; z-index: 999999;
            display: flex; flex-direction: column; gap: 10px; pointer-events: none;
        }
        .popup {
            margin: 0; box-shadow: 4px 4px 10px -10px rgba(0, 0, 0, 1); width: 320px;
            justify-content: space-between; align-items: center; display: flex;
            border-radius: 6px; padding: 12px 16px; font-weight: 500; pointer-events: auto;
            animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0; transform: translateX(100%); background: white;
        }
        @keyframes slideIn { to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeOut { to { opacity: 0; transform: translateX(100%); } }
        .popup svg { width: 1.25rem; height: 1.25rem; }
        .popup-icon { display: flex; align-items: center; flex-shrink: 0; }
        .popup-icon svg { margin: 0; display: flex; align-items: center; }
        .close-icon { margin-left: auto; cursor: pointer; padding: 4px; border-radius: 4px; transition: background 0.15s; }
        .close-icon:hover { background: rgba(0,0,0,0.05); }
        .close-path { fill: grey; }
        
        .success-popup { background-color: #edfbd8; border: solid 1px #84d65a; }
        .success-icon path { fill: #84d65a; }
        .success-message { color: #2b641e; margin-left: 10px; margin-right: 10px; font-size: 13px; line-height: 1.4; flex: 1; }
        
        .alert-popup { background-color: #fefce8; border: solid 1px #facc15; }
        .alert-icon path { fill: #facc15; }
        .alert-message { color: #ca8a04; margin-left: 10px; margin-right: 10px; font-size: 13px; line-height: 1.4; flex: 1; }
        
        .error-popup { background-color: #fef2f2; border: solid 1px #f87171; }
        .error-icon path { fill: #f87171; }
        .error-message { color: #991b1b; margin-left: 10px; margin-right: 10px; font-size: 13px; line-height: 1.4; flex: 1; }
        
        .info-popup { background-color: #eff6ff; border: solid 1px #1d4ed8; }
        .info-icon path { fill: #1d4ed8; }
        .info-message { color: #1d4ed8; margin-left: 10px; margin-right: 10px; font-size: 13px; line-height: 1.4; flex: 1; }
    </style>
    @stack('styles')
</head>
