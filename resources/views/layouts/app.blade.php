<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UCC-CS | @yield('title', 'Consumable Management System')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        /* ══════════════════════════════════════
           RESET & BASE
        ══════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f4f6f8; color: #111; min-height: 100vh; }

        /* ══════════════════════════════════════
           CSS VARIABLES — GREEN PALETTE
        ══════════════════════════════════════ */
        :root {
            --green-dark:    #1a6b3a;
            --green-darker:  #155a30;
            --green-light:   #f0faf4;
            --green-accent:  #6ee7b7;
            --sidebar-bg:    #1a3a2a;
            --sidebar-w:     240px;
            --topbar-h:      64px;
            --text-primary:  #111;
            --text-secondary:#555;
            --text-muted:    #aaa;
            --border:        #e8eaf0;
            --card-shadow:   0 2px 12px rgba(0,0,0,0.06);
            --red:           #e24b4a;
            --orange:        #ef9f27;
            --blue:          #1a56db;
            --blue-light:    #eff6ff;
        }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
            transition: transform 0.3s ease;
        }
        .sidebar-brand {
            padding: 1.25rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 10px;
        }
        .brand-icon {
            width: 38px; height: 38px;
            background: #fff;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden; padding: 3px;
        }
        .brand-icon img { width: 100%; height: 100%; object-fit: contain; }
        .brand-text-main { font-size: 13px; font-weight: 700; color: #fff; line-height: 1.2; }
        .brand-text-sub  { font-size: 10px; color: rgba(255,255,255,0.45); }

        .sidebar-user {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 10px;
        }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--green-dark);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
            flex-shrink: 0; text-transform: uppercase;
        }
        .user-info-name { font-size: 12px; font-weight: 600; color: #fff; line-height: 1.3; }
        .user-info-role {
            font-size: 10px; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.8px; padding: 1px 7px; border-radius: 10px;
            display: inline-block; margin-top: 2px;
        }
        .role-user       { background: rgba(110,231,183,0.2); color: #6ee7b7; }
        .role-admin      { background: rgba(52,211,153,0.25); color: #a7f3d0; }
        .role-superadmin { background: rgba(239,159,39,0.2);  color: #fcd34d; }
        .user-campus { font-size: 10px; color: rgba(255,255,255,0.4); margin-top: 1px; display: flex; align-items: center; gap: 4px; }

        .sidebar-nav { flex: 1; padding: 0.75rem 0; overflow-y: auto; }
        .nav-section-label {
            font-size: 9px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.5px; color: rgba(255,255,255,0.3);
            padding: 0.75rem 1.2rem 0.4rem;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 1.2rem;
            font-size: 13px; font-weight: 500;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.18s; cursor: pointer;
        }
        .nav-item:hover  { background: rgba(255,255,255,0.06); color: #fff; }
        .nav-item.active { background: rgba(255,255,255,0.1); color: #fff; border-left-color: var(--green-accent); }
        .nav-item i { font-size: 17px; flex-shrink: 0; }
        .nav-item.logout { color: rgba(226,75,74,0.8); }
        .nav-item.logout:hover { background: rgba(226,75,74,0.1); color: var(--red); }

        .sidebar-footer {
            padding: 0.75rem 1.2rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        /* Sidebar overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 49;
        }
        .sidebar-overlay.show { display: block; }

        /* ══════════════════════════════════════
           TOPBAR
        ══════════════════════════════════════ */
        .topbar {
            position: fixed; top: 0;
            left: var(--sidebar-w); right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 40;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .topbar-left  { display: flex; align-items: center; gap: 12px; }
        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .page-title-bar { font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
        .page-badge { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 3px 8px; border-radius: 6px; }
        .badge-admin      { background: rgba(26,107,58,0.1); color: var(--green-dark); }
        .badge-superadmin { background: rgba(239,159,39,0.1); color: #ef9f27; }
        .badge-user       { background: rgba(26,107,58,0.08); color: var(--green-dark); }

        .topbar-btn {
            width: 36px; height: 36px; border-radius: 8px;
            border: 1px solid var(--border); background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--text-secondary);
            font-size: 17px; transition: all 0.18s; text-decoration: none; position: relative;
        }
        .topbar-btn:hover { background: var(--green-light); color: var(--green-dark); border-color: var(--green-dark); }

        .datetime-chip { display: flex; gap: 8px; align-items: center; }
        .chip {
            background: var(--green-dark); color: #fff;
            padding: 5px 12px; border-radius: 8px;
            font-size: 12px; font-weight: 600;
        }

        /* ══════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════ */
        .main-wrap { margin-left: var(--sidebar-w); padding-top: var(--topbar-h); min-height: 100vh; }
        .main-content { padding: 1.5rem; }

        /* ══════════════════════════════════════
           CARDS
        ══════════════════════════════════════ */
        .card { background: #fff; border-radius: 12px; border: 1px solid var(--border); box-shadow: var(--card-shadow); }
        .card-body { padding: 1.25rem; }
        .card-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
        .card-title  { font-size: 14px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
        .card-title i { color: var(--green-dark); }

        /* ══════════════════════════════════════
           STATS GRID
        ══════════════════════════════════════ */
        .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1.25rem; }
        .stat-card { background: #fff; border-radius: 12px; border: 1px solid var(--border); padding: 1.2rem; display: flex; align-items: center; gap: 1rem; box-shadow: var(--card-shadow); transition: transform 0.15s; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .stat-icon.green  { background: var(--green-light); color: var(--green-dark); }
        .stat-icon.blue   { background: var(--green-light); color: var(--green-dark); }
        .stat-icon.orange { background: #fff8f0; color: #ef9f27; }
        .stat-icon.red    { background: #fff5f5; color: var(--red); }
        .stat-value { font-size: 28px; font-weight: 700; color: var(--text-primary); line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-secondary); margin-top: 3px; }

        /* ══════════════════════════════════════
           HERO BANNER
        ══════════════════════════════════════ */
        .hero-banner {
            background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-darker) 100%);
            border-radius: 12px; padding: 1.8rem 2rem;
            color: #fff; display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 1.25rem;
            position: relative; overflow: hidden;
        }
        .hero-banner::before { content: ''; position: absolute; top: -40px; right: -40px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.05); }
        .hero-banner::after  { content: ''; position: absolute; bottom: -60px; right: 80px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.04); }
        .hero-left { position: relative; z-index: 1; flex: 1; }
        .hero-greeting { font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .hero-sub { font-size: 13px; color: rgba(255,255,255,0.75); margin-top: 6px; max-width: 480px; line-height: 1.5; }
        .hero-chips { display: flex; gap: 8px; margin-top: 1rem; flex-wrap: wrap; }
        .hero-chip { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; color: #fff; }
        .hero-chip span { color: rgba(255,255,255,0.65); font-weight: 400; margin-right: 4px; }
        .hero-right { position: relative; z-index: 1; flex-shrink: 0; }
        .btn-add {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.3);
            color: #fff; padding: 10px 20px; border-radius: 10px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            text-decoration: none; transition: background 0.2s; white-space: nowrap;
        }
        .btn-add:hover { background: rgba(255,255,255,0.25); color: #fff; }

        /* ══════════════════════════════════════
           GRID LAYOUTS
        ══════════════════════════════════════ */
        .two-col   { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }
        .three-col { display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }

        /* ══════════════════════════════════════
           DATA TABLE
        ══════════════════════════════════════ */
        .data-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .data-table { width: 100%; border-collapse: collapse; min-width: 560px; }
        .data-table th { padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted); background: #fafafa; border-bottom: 1px solid var(--border); white-space: nowrap; }
        .data-table td { padding: 12px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; font-size: 13px; color: var(--text-primary); }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #f6fbf8; }
        .cell-primary   { font-weight: 600; color: var(--text-primary); font-size: 13px; }
        .cell-secondary { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

        /* ══════════════════════════════════════
           CHIP BADGES
        ══════════════════════════════════════ */
        .chip-badge       { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }
        .chip-campus      { background: var(--green-light); color: var(--green-dark); }
        .chip-type        { background: #f4f0ff; color: #7c3aed; }
        .chip-status-active   { background: var(--green-light); color: var(--green-dark); }
        .chip-status-inactive { background: #fff5f5; color: var(--red); }
        .chip-dash        { color: var(--text-muted); font-size: 12px; }

        /* ══════════════════════════════════════
           TABLE ICON BUTTONS
        ══════════════════════════════════════ */
        .table-actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .table-icon-btn { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; font-size: 14px; border: none; cursor: pointer; transition: opacity 0.15s, transform 0.1s; text-decoration: none; flex-shrink: 0; }
        .table-icon-btn:hover  { opacity: 0.8; transform: translateY(-1px); }
        .table-icon-btn.view   { background: var(--green-light); color: var(--green-dark); }
        .table-icon-btn.edit   { background: #f4f0ff; color: #7c3aed; }
        .table-icon-btn.delete { background: #fff5f5; color: var(--red); }
        .table-icon-btn.archive{ background: #fff8f0; color: var(--orange); }

        /* ══════════════════════════════════════
           FILTER PILLS
        ══════════════════════════════════════ */
        .filter-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px; }
        .filter-pills { display: flex; gap: 6px; flex-wrap: wrap; }
        .filter-pill { padding: 7px 16px; border-radius: 20px; border: 1.5px solid var(--border); background: #fff; color: var(--text-secondary); font-size: 12.5px; font-weight: 500; cursor: pointer; transition: all 0.15s; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-block; }
        .filter-pill:hover  { border-color: var(--green-dark); color: var(--green-dark); }
        .filter-pill.active { background: var(--green-dark); border-color: var(--green-dark); color: #fff; font-weight: 600; }

        /* ══════════════════════════════════════
           BUTTON UTILITIES
        ══════════════════════════════════════ */
        .btn-table-action { display: flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; font-family: 'Inter', sans-serif; transition: opacity 0.15s; text-decoration: none; }
        .btn-table-action.green { background: var(--green-dark); color: #fff; }
        .btn-table-action.green:hover { opacity: 0.88; }
        .btn-back-link { display: flex; align-items: center; gap: 6px; padding: 11px 22px; border-radius: 8px; border: 1.5px solid var(--border); background: #fff; color: var(--text-secondary); font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.18s; cursor: pointer; font-family: 'Inter', sans-serif; }
        .btn-back-link:hover { border-color: var(--red); color: var(--red); }

        /* ══════════════════════════════════════
           TAB TOGGLE
        ══════════════════════════════════════ */
        .tab-toggle-btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; border-radius: 8px; border: 1.5px solid var(--border); background: #fff; color: var(--text-secondary); font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.15s; font-family: 'Inter', sans-serif; }
        .tab-toggle-btn:hover  { border-color: var(--green-dark); color: var(--green-dark); }
        .tab-toggle-btn.active { background: var(--green-dark); border-color: var(--green-dark); color: #fff; }

        /* ══════════════════════════════════════
           MODAL SYSTEM
        ══════════════════════════════════════ */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 300; align-items: center; justify-content: center; padding: 1rem; }
        .modal-overlay.open { display: flex; }
        .modal-box-sm { background: #fff; border-radius: 14px; padding: 1.5rem; width: 100%; max-width: 420px; max-height: 92vh; overflow-y: auto; box-shadow: 0 24px 64px rgba(0,0,0,0.18); animation: modalDropIn 0.2s ease; }
        .modal-box-lg { background: #fff; border-radius: 14px; padding: 1.5rem; width: 100%; max-width: 640px; max-height: 92vh; overflow-y: auto; box-shadow: 0 24px 64px rgba(0,0,0,0.18); animation: modalDropIn 0.2s ease; }
        @keyframes modalDropIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .modal-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
        .modal-title-sm { font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
        .modal-title-sm i { color: var(--green-dark); }
        .modal-close { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--border); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 14px; flex-shrink: 0; }
        .modal-close:hover { background: #fff5f5; color: var(--red); border-color: var(--red); }
        .modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.5rem; }
        .modal-form-group { margin-bottom: 0.85rem; }
        .modal-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #555; margin-bottom: 5px; display: block; }
        .modal-input { width: 100%; padding: 10px 12px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #111; outline: none; transition: border-color 0.2s; background: #fff; }
        .modal-input:focus { border-color: var(--green-dark); }
        .modal-hint { font-size: 11px; margin-top: 3px; }
        .modal-hint.error   { color: var(--red); }
        .modal-hint.success { color: var(--green-dark); }
        .modal-btn-primary { width: 100%; padding: 11px; background: var(--green-dark); color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 1rem; transition: background 0.2s; }
        .modal-btn-primary:hover { background: var(--green-darker); }

        /* ══════════════════════════════════════
           PAGINATION
        ══════════════════════════════════════ */
        .pagination { display: flex; align-items: center; gap: 4px; list-style: none; flex-wrap: wrap; }
        .pagination li { display: flex; }
        .pagination li span, .pagination li a { display: flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; font-size: 13px; font-weight: 500; border-radius: 8px; text-decoration: none; color: var(--text-secondary); border: 1px solid var(--border); background: #fff; transition: all 0.15s; }
        .pagination li a:hover { background: var(--green-light); border-color: var(--green-dark); color: var(--green-dark); }
        .pagination li.active span { background: var(--green-dark); border-color: var(--green-dark); color: #fff; font-weight: 600; }
        .pagination li.disabled span { color: var(--text-muted); background: #fafafa; cursor: not-allowed; }
        .pagination-wrap { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 1rem 1.25rem; border-top: 1px solid var(--border); }
        .pagination-info { font-size: 12.5px; color: var(--text-muted); }

        /* ══════════════════════════════════════
           ALERTS
        ══════════════════════════════════════ */
        .alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 10px; margin-bottom: 1rem; animation: slideDown 0.35s ease; }
        .alert-success { background: var(--green-light); border: 1.5px solid var(--green-dark); }
        .alert-success i { color: var(--green-dark); flex-shrink: 0; }
        .alert-error { background: #fff5f5; border: 1.5px solid var(--red); }
        .alert-error i { color: var(--red); flex-shrink: 0; }
        .alert-text { font-size: 13px; line-height: 1.5; }
        .alert-text strong { display: block; margin-bottom: 2px; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

        /* ══════════════════════════════════════
           EMPTY STATE
        ══════════════════════════════════════ */
        .empty-state { text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); }
        .empty-state i { font-size: 36px; margin-bottom: 0.75rem; display: block; }
        .empty-state p { font-size: 13px; }

        /* ══════════════════════════════════════
           DETAIL GRID
        ══════════════════════════════════════ */
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .detail-row { display: flex; flex-direction: column; gap: 3px; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border); font-size: 13px; }
        .detail-row span { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .detail-row strong { color: var(--text-primary); font-weight: 500; }
        .detail-section { margin-bottom: 1.25rem; }
        .detail-section-title { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.6rem; display: flex; align-items: center; gap: 6px; }

        /* ══════════════════════════════════════
           SETTINGS DROPDOWN
        ══════════════════════════════════════ */
        .settings-wrap { position: relative; }
        .settings-dropdown { display: none; position: absolute; top: calc(100% + 8px); right: 0; background: #fff; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); min-width: 220px; z-index: 200; overflow: hidden; animation: dropIn 0.18s ease; }
        .settings-dropdown.open { display: block; }
        @keyframes dropIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
        .settings-header { padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); background: var(--green-light); }
        .settings-user-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .settings-user-email { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .settings-item { display: flex; align-items: center; gap: 10px; padding: 10px 1rem; font-size: 13px; font-weight: 500; color: var(--text-secondary); text-decoration: none; transition: background 0.15s; width: 100%; background: none; border: none; cursor: pointer; font-family: 'Inter', sans-serif; text-align: left; }
        .settings-item:hover { background: var(--green-light); color: var(--green-dark); }
        .settings-item i { font-size: 16px; color: var(--green-dark); }
        .settings-logout { color: var(--red) !important; }
        .settings-logout i { color: var(--red) !important; }
        .settings-logout:hover { background: #fff5f5 !important; }
        .settings-divider { height: 1px; background: var(--border); margin: 4px 0; }

        /* ══════════════════════════════════════
           ITEM CARDS GRID (Browse Items)
        ══════════════════════════════════════ */
        .items-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.1rem;
            margin-bottom: 1.25rem;
        }

        .item-card {
            background: #fff;
            border: 1px solid var(--border);
            border-left: 4px solid var(--green-dark);
            border-radius: 12px;
            padding: 1.1rem;
            transition: transform 0.15s, box-shadow 0.15s;
            display: flex; flex-direction: column;
        }
        .item-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.07); }
        .item-card.status-critical { border-left-color: var(--red); }
        .item-card.status-low      { border-left-color: var(--orange); }
        .item-card.status-available{ border-left-color: var(--green-dark); }
        .item-card.status-out      { border-left-color: #bbb; opacity: 0.72; }

        .item-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 6px; margin-bottom: 0.6rem; }
        .item-card-category { font-size: 11px; color: var(--text-muted); background: var(--green-light); padding: 3px 9px; border-radius: 10px; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; flex-shrink: 0; }
        .item-card-name  { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 3px; line-height: 1.3; }
        .item-card-brand { font-size: 11.5px; color: var(--text-muted); margin-bottom: 0.75rem; min-height: 16px; }
        .item-card-meta  { display: flex; justify-content: space-between; align-items: center; background: #fafafa; border-radius: 8px; padding: 8px 10px; margin-bottom: 0.85rem; }
        .item-card-meta-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted); margin-bottom: 2px; }
        .item-card-meta-value { font-size: 14px; font-weight: 700; color: var(--text-primary); }

        .item-status-badge { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 10px; display: inline-flex; align-items: center; gap: 3px; white-space: nowrap; }
        .item-status-badge.available { background: var(--green-light); color: var(--green-dark); }
        .item-status-badge.low       { background: #fff8f0; color: var(--orange); }
        .item-status-badge.critical  { background: #fff5f5; color: var(--red); }
        .item-status-badge.out       { background: #f5f5f5; color: #999; }

        .item-card-btn { width: 100%; padding: 9px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; gap: 7px; transition: opacity 0.15s, transform 0.1s; margin-top: auto; }
        .item-card-btn.can-request   { background: var(--green-dark); color: #fff; }
        .item-card-btn.can-request:hover { opacity: 0.88; transform: translateY(-1px); }
        .item-card-btn.in-cart       { background: var(--green-light); color: var(--green-dark); border: 1.5px solid var(--green-dark); }
        .item-card-btn.cannot-request{ background: #f5f5f5; color: #aaa; cursor: not-allowed; }

        /* ══════════════════════════════════════
           FLOATING CART BUTTON
        ══════════════════════════════════════ */
        .floating-cart {
            position: fixed; bottom: 28px; right: 28px;
            width: 56px; height: 56px; border-radius: 50%;
            background: var(--green-dark); color: #fff;
            border: none; cursor: pointer; z-index: 100;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 16px rgba(26,107,58,0.35);
            transition: transform 0.15s, background 0.2s;
        }
        .floating-cart:hover { background: var(--green-darker); transform: scale(1.08); }
        .floating-cart-badge {
            position: absolute; top: -4px; right: -4px;
            background: var(--red); color: #fff;
            font-size: 11px; font-weight: 700;
            width: 20px; height: 20px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        /* ══════════════════════════════════════
           CART MODAL ROWS
        ══════════════════════════════════════ */
        .cart-row { display: flex; align-items: flex-start; gap: 10px; padding: 12px 0; border-bottom: 1px solid var(--border); flex-wrap: wrap; }
        .cart-row:last-child { border-bottom: none; }
        .cart-row-name { font-size: 13px; font-weight: 600; color: var(--text-primary); flex: 1; min-width: 120px; }
        .cart-qty-input { width: 68px; padding: 6px 8px; border: 1.5px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; text-align: center; outline: none; }
        .cart-qty-input:focus { border-color: var(--green-dark); }
        .cart-remove-btn { width: 28px; height: 28px; border-radius: 6px; background: #fff5f5; color: var(--red); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

        /* ══════════════════════════════════════
           SCROLLBAR
        ══════════════════════════════════════ */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #b2d8c2; border-radius: 4px; }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media(max-width:1100px) {
            .items-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media(max-width:1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .three-col  { grid-template-columns: 1fr; }
        }
        @media(max-width:768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar   { left: 0; }
            .main-wrap{ margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .two-col    { grid-template-columns: 1fr; }
            .hero-banner { flex-direction: column; gap: 1rem; }
            .hero-right  { width: 100%; }
            .hero-right .btn-add { width: 100%; justify-content: center; }
            .pagination-wrap { flex-direction: column; align-items: flex-start; }
            .floating-cart { bottom: 20px; right: 20px; }
            .datetime-chip .chip:first-child { display: none; }
        }
        @media(max-width:640px) {
            .items-grid { grid-template-columns: 1fr; }
        }
        @media(max-width:480px) {
            .stats-grid  { grid-template-columns: 1fr; }
            .hero-chips  { flex-wrap: wrap; }
            .modal-box-lg, .modal-box-sm { padding: 1.25rem; }
            .modal-grid  { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
            .main-content{ padding: 1rem; }
            .hero-banner { padding: 1.4rem 1.2rem; }
            .hero-greeting { font-size: 18px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

@include('partials.sidebar')

<div class="main-wrap">
    @include('partials.topbar')

    <div class="main-content">

        @if(session('error'))
        <div class="alert alert-error">
            <i class="ti ti-alert-circle"></i>
            <div class="alert-text"><strong>Error</strong>{{ session('error') }}</div>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success">
            <i class="ti ti-circle-check"></i>
            <div class="alert-text"><strong>Success</strong>{{ session('success') }}</div>
        </div>
        @endif

        @yield('content')
    </div>
</div>

{{-- ═══ FLOATING CHATBOT ═══ --}}
<style>
.chatbot-fab {
    position: fixed; bottom: 28px; right: 28px; z-index: 400;
    width: 56px; height: 56px; border-radius: 50%;
    background: var(--green-dark); color: #fff; border: none;
    cursor: pointer; box-shadow: 0 4px 16px rgba(26,107,58,0.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; transition: transform 0.2s, background 0.2s;
}
.chatbot-fab:hover { background: var(--green-darker); transform: scale(1.08); }
.chatbot-fab .fab-badge {
    position: absolute; top: -4px; right: -4px;
    background: var(--red); color: #fff;
    font-size: 10px; font-weight: 700;
    width: 18px; height: 18px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
}

.chatbot-window {
    position: fixed; bottom: 96px; right: 28px; z-index: 400;
    width: 360px; height: 540px;
    background: #fff; border-radius: 16px;
    box-shadow: 0 16px 48px rgba(0,0,0,0.18);
    display: flex; flex-direction: column;
    overflow: hidden;
    animation: chatSlideIn 0.25s ease;
    display: none;
}
@keyframes chatSlideIn {
    from { opacity: 0; transform: translateY(16px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.chatbot-window.open { display: flex; }

.chatbot-header {
    background: var(--green-dark); color: #fff;
    padding: 12px 16px; display: flex; align-items: center; gap: 10px; flex-shrink: 0;
}
.chatbot-header-icon { width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 18px; }
.chatbot-header-title { font-size: 14px; font-weight: 700; flex: 1; }
.chatbot-header-sub   { font-size: 11px; color: rgba(255,255,255,0.7); }
.chatbot-close { background: none; border: none; color: #fff; font-size: 18px; cursor: pointer; padding: 4px; border-radius: 6px; }
.chatbot-close:hover { background: rgba(255,255,255,0.15); }

.chatbot-messages { flex: 1; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 10px; }

.chat-msg { display: flex; flex-direction: column; }
.chat-msg.user { align-items: flex-end; }
.chat-msg.bot  { align-items: flex-start; }

.chat-bubble {
    max-width: 82%; padding: 9px 12px; border-radius: 12px;
    font-size: 13px; line-height: 1.55; white-space: pre-wrap; word-break: break-word;
}
.chat-bubble.user { background: var(--green-dark); color: #fff; border-radius: 12px 12px 4px 12px; }
.chat-bubble.bot  { background: #f4f6f4; color: var(--text-primary); border-radius: 12px 12px 12px 4px; border: 1px solid var(--border); }
.chat-bubble.bot strong { color: var(--green-dark); }

.chat-time { font-size: 10px; color: var(--text-muted); margin-top: 3px; }

.chat-actions { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
.chat-action-btn {
    padding: 6px 12px; border-radius: 20px;
    border: 1.5px solid var(--green-dark); background: #fff;
    color: var(--green-dark); font-size: 12px; font-weight: 600;
    cursor: pointer; font-family: inherit; transition: all 0.15s;
}
.chat-action-btn:hover:not(:disabled) { background: var(--green-dark); color: #fff; }
.chat-action-btn:disabled { border-color: #ccc; color: #ccc; cursor: not-allowed; }

.chat-typing { display: flex; align-items: center; gap: 4px; padding: 8px 12px; background: #f4f6f4; border-radius: 12px; width: fit-content; }
.chat-typing span { width: 7px; height: 7px; border-radius: 50%; background: #aaa; animation: typingDot 1.2s infinite; }
.chat-typing span:nth-child(2) { animation-delay: 0.2s; }
.chat-typing span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typingDot { 0%,80%,100% { transform: scale(0.8); opacity:0.5; } 40% { transform: scale(1); opacity:1; } }

.chatbot-input-wrap { padding: 10px; border-top: 1px solid var(--border); display: flex; gap: 8px; flex-shrink: 0; background: #fff; }
.chatbot-input { flex: 1; padding: 9px 12px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 13px; font-family: inherit; outline: none; resize: none; }
.chatbot-input:focus { border-color: var(--green-dark); }
.chatbot-send { width: 36px; height: 36px; border-radius: 10px; background: var(--green-dark); color: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; align-self: flex-end; }
.chatbot-send:hover { background: var(--green-darker); }

@media(max-width:480px) {
    .chatbot-window { width: calc(100vw - 20px); right: 10px; bottom: 86px; }
    .chatbot-fab { bottom: 20px; right: 16px; }
}
</style>

{{-- FAB Button --}}
<button class="chatbot-fab" onclick="toggleChatbot()" title="Chat with UCC-CS Assistant" id="chatbot-fab">
    <i class="ti ti-message-chatbot" id="chatbot-fab-icon"></i>
</button>

{{-- Chat Window --}}
<div class="chatbot-window" id="chatbot-window">
    <div class="chatbot-header">
        <div class="chatbot-header-icon"><i class="ti ti-robot"></i></div>
        <div style="flex:1;">
            <div class="chatbot-header-title">UCC-CS Assistant</div>
            <div class="chatbot-header-sub">Online • Typically replies instantly</div>
        </div>
        <button class="chatbot-close" onclick="toggleChatbot()"><i class="ti ti-x"></i></button>
    </div>

    <div class="chatbot-messages" id="chatbot-messages"></div>

    <div class="chatbot-input-wrap">
        <textarea class="chatbot-input" id="chatbot-input" rows="1"
                  placeholder="Type a message..."
                  onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendChatMessage();}"></textarea>
        <button class="chatbot-send" onclick="sendChatMessage()"><i class="ti ti-send"></i></button>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let chatOpen     = false;
let chatStarted  = false;

function toggleChatbot() {
    chatOpen = !chatOpen;
    const win  = document.getElementById('chatbot-window');
    const icon = document.getElementById('chatbot-fab-icon');
    win.classList.toggle('open', chatOpen);
    icon.className = chatOpen ? 'ti ti-x' : 'ti ti-message-chatbot';

    if (chatOpen && !chatStarted) {
        chatStarted = true;
        sendMessage('__start__', true);
    }
    if (chatOpen) scrollChatBottom();
}

function scrollChatBottom() {
    const msgs = document.getElementById('chatbot-messages');
    setTimeout(() => msgs.scrollTop = msgs.scrollHeight, 50);
}

function appendMessage(body, type, actions, time) {
    const msgs = document.getElementById('chatbot-messages');

    // Remove typing indicator if present
    const typing = msgs.querySelector('.chat-typing-wrap');
    if (typing) typing.remove();

    const wrap = document.createElement('div');
    wrap.className = `chat-msg ${type === 'user' ? 'user' : 'bot'}`;

    // Parse markdown-lite: **bold**
    const formatted = body.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

    wrap.innerHTML = `
        <div class="chat-bubble ${type === 'user' ? 'user' : 'bot'}">${formatted}</div>
        <div class="chat-time">${time || new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}</div>
        ${actions && actions.length ? `<div class="chat-actions">${actions.map(a => `
            <button class="chat-action-btn" ${a.disabled ? 'disabled' : ''}
                    onclick="${a.url ? `window.open('${a.url}','_blank')` : `sendMessage('${a.value}')`}">
                ${a.label}
            </button>`).join('')}</div>` : ''}
    `;
    msgs.appendChild(wrap);
    scrollChatBottom();
}

function showTyping() {
    const msgs = document.getElementById('chatbot-messages');
    const wrap = document.createElement('div');
    wrap.className = 'chat-msg bot chat-typing-wrap';
    wrap.innerHTML = `<div class="chat-typing"><span></span><span></span><span></span></div>`;
    msgs.appendChild(wrap);
    scrollChatBottom();
}

async function sendMessage(value, silent) {
    if (!value) return;

    const input = document.getElementById('chatbot-input');

    if (!silent) {
        const userText = input.value.trim() || value;
        // Don't show system values as user messages
        if (!value.startsWith('__')) {
            appendMessage(userText, 'user');
        }
        input.value = '';
    }

    showTyping();

    try {
        const res  = await fetch('{{ route("chatbot.message") }}', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({ message: value }),
        });
        const data = await res.json();

        if (data.redirect) {
            appendMessage(data.response, 'bot');
            setTimeout(() => window.location.href = data.redirect, 800);
            return;
        }

        appendMessage(data.response, 'bot', data.actions);
    } catch(e) {
        const typing = document.getElementById('chatbot-messages').querySelector('.chat-typing-wrap');
        if (typing) typing.remove();
        appendMessage('Sorry, something went wrong. Please try again.', 'bot');
    }
}

function sendChatMessage() {
    const input = document.getElementById('chatbot-input');
    const val   = input.value.trim();
    if (!val) return;
    sendMessage(val);
}

// Reset chatbot session on page unload (optional — comment out to persist state)
// window.addEventListener('beforeunload', () => fetch('{{ route("chatbot.reset") }}', {method:'POST',headers:{'X-CSRF-TOKEN':CSRF}}));
</script>

<script>
// ── CLOCK ──
function updateClock() {
    const now  = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const el   = document.getElementById('live-clock');
    if (el) el.textContent = time;
}
setInterval(updateClock, 1000);
updateClock();

// ── SIDEBAR ──
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
    document.getElementById('sidebar-overlay').classList.toggle('show');
}
function closeSidebar() {
    document.querySelector('.sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').classList.remove('show');
}

// ── SETTINGS DROPDOWN ──
function toggleSettings() {
    document.getElementById('settings-dropdown').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('settings-wrap');
    if (wrap && !wrap.contains(e.target)) {
        const dd = document.getElementById('settings-dropdown');
        if (dd) dd.classList.remove('open');
    }
    const notifBtn = document.querySelector('[onclick*="toggleNotifDropdown"]');
    const notifDd  = document.getElementById('notif-dropdown');
    if (notifDd && notifBtn && !notifBtn.contains(e.target) && !notifDd.contains(e.target)) {
        notifDd.classList.remove('open');
    }
});

// ── AUTO-DISMISS ALERTS ──
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.alert').forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() { alert.remove(); }, 500);
        }, 5000);
    });
});
</script>

@stack('scripts')
</body>
</html>