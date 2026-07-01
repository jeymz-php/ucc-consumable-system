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
           CSS VARIABLES
        ══════════════════════════════════════ */
        :root {
            --blue:          #1a56db;
            --blue-dark:     #1648c0;
            --blue-light:    #eff6ff;
            --blue-accent:   #93c5fd;
            --sidebar-w:     240px;
            --topbar-h:      64px;
            --text-primary:  #111;
            --text-secondary:#555;
            --text-muted:    #aaa;
            --border:        #e8eaf0;
            --card-shadow:   0 2px 12px rgba(0,0,0,0.06);
            --red:           #e24b4a;
            --orange:        #ef9f27;
            --green:         #1a6b3a;
        }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: #0f2d6b;
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
            background: var(--blue);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
            flex-shrink: 0; text-transform: uppercase;
        }
        .user-info-name  { font-size: 12px; font-weight: 600; color: #fff; line-height: 1.3; }
        .user-info-role  {
            font-size: 10px; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.8px; padding: 1px 7px; border-radius: 10px;
            display: inline-block; margin-top: 2px;
        }
        .role-user       { background: rgba(147,197,253,0.2); color: #93c5fd; }
        .role-admin      { background: rgba(59,130,246,0.3);  color: #bfdbfe; }
        .role-superadmin { background: rgba(239,159,39,0.2);  color: #fcd34d; }
        .user-campus     { font-size: 10px; color: rgba(255,255,255,0.4); margin-top: 1px; display: flex; align-items: center; gap: 4px; }

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
        .nav-item:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .nav-item.active { background: rgba(255,255,255,0.1); color: #fff; border-left-color: var(--blue-accent); }
        .nav-item i { font-size: 17px; flex-shrink: 0; }
        .nav-item.logout { color: rgba(226,75,74,0.8); }
        .nav-item.logout:hover { background: rgba(226,75,74,0.1); color: var(--red); }

        .sidebar-footer {
            padding: 0.75rem 1.2rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

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
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .page-title-bar { font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
        .page-badge { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 3px 8px; border-radius: 6px; }
        .badge-admin      { background: rgba(59,130,246,0.1);  color: #3b82f6; }
        .badge-superadmin { background: rgba(239,159,39,0.1);  color: #ef9f27; }
        .badge-user       { background: rgba(26,86,219,0.1);   color: var(--blue); }

        .topbar-right { display: flex; align-items: center; gap: 10px; }
        .topbar-btn {
            width: 36px; height: 36px; border-radius: 8px;
            border: 1px solid var(--border); background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--text-secondary);
            font-size: 17px; transition: all 0.18s; text-decoration: none; position: relative;
        }
        .topbar-btn:hover { background: var(--blue-light); color: var(--blue); border-color: var(--blue); }
        .topbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--blue);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
            cursor: pointer; text-transform: uppercase;
        }
        .datetime-chip { display: flex; gap: 8px; align-items: center; }
        .chip {
            background: var(--blue); color: #fff;
            padding: 5px 12px; border-radius: 8px;
            font-size: 12px; font-weight: 600;
        }

        /* ══════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════ */
        .main-wrap { margin-left: var(--sidebar-w); padding-top: var(--topbar-h); flex: 1; min-height: 100vh; }
        .main-content { padding: 1.5rem; }

        /* ══════════════════════════════════════
           CARDS
        ══════════════════════════════════════ */
        .card { background: #fff; border-radius: 12px; border: 1px solid var(--border); box-shadow: var(--card-shadow); }
        .card-body { padding: 1.25rem; }
        .card-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .card-title  { font-size: 14px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
        .card-title i { color: var(--blue); }

        /* ══════════════════════════════════════
           STATS GRID
        ══════════════════════════════════════ */
        .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1.25rem; }
        .stat-card { background: #fff; border-radius: 12px; border: 1px solid var(--border); padding: 1.2rem; display: flex; align-items: center; gap: 1rem; box-shadow: var(--card-shadow); transition: transform 0.15s; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .stat-icon.blue   { background: var(--blue-light); color: var(--blue); }
        .stat-icon.green  { background: #f0faf4; color: #1a6b3a; }
        .stat-icon.orange { background: #fff8f0; color: #ef9f27; }
        .stat-icon.red    { background: #fff5f5; color: var(--red); }
        .stat-value { font-size: 28px; font-weight: 700; color: var(--text-primary); line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-secondary); margin-top: 3px; }

        /* ══════════════════════════════════════
           HERO BANNER
        ══════════════════════════════════════ */
        .hero-banner {
            background: linear-gradient(135deg, var(--blue) 0%, #2563eb 100%);
            border-radius: 12px; padding: 1.8rem 2rem;
            color: #fff; display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 1.25rem;
            position: relative; overflow: hidden;
        }
        .hero-banner::before { content: ''; position: absolute; top: -40px; right: -40px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.05); }
        .hero-banner::after  { content: ''; position: absolute; bottom: -60px; right: 80px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.04); }
        .hero-left { position: relative; z-index: 1; }
        .hero-greeting { font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .hero-sub { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 6px; max-width: 480px; line-height: 1.5; }
        .hero-chips { display: flex; gap: 8px; margin-top: 1rem; }
        .hero-chip { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; color: #fff; }
        .hero-chip span { color: rgba(255,255,255,0.65); font-weight: 400; margin-right: 4px; }
        .hero-right { position: relative; z-index: 1; }
        .btn-add {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.3);
            color: #fff; padding: 10px 20px; border-radius: 10px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            text-decoration: none; transition: background 0.2s;
        }
        .btn-add:hover { background: rgba(255,255,255,0.25); color: #fff; }

        /* ══════════════════════════════════════
           TWO COL / THREE COL
        ══════════════════════════════════════ */
        .two-col   { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }
        .three-col { display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }

        /* ══════════════════════════════════════
           DATA TABLE
        ══════════════════════════════════════ */
        .data-table-wrap { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted); background: #fafafa; border-bottom: 1px solid var(--border); white-space: nowrap; }
        .data-table td { padding: 12px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; font-size: 13px; color: var(--text-primary); }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #f9fbff; }
        .cell-primary  { font-weight: 600; color: var(--text-primary); font-size: 13px; }
        .cell-secondary{ font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

        /* ══════════════════════════════════════
           CHIP BADGES
        ══════════════════════════════════════ */
        .chip-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }
        .chip-campus         { background: var(--blue-light); color: var(--blue); }
        .chip-type           { background: #f4f0ff; color: #7c3aed; }
        .chip-equipment-zero { background: #f5f5f5; color: #999; }
        .chip-equipment-has  { background: #f0faf4; color: #1a6b3a; }
        .chip-status-active  { background: #f0faf4; color: #1a6b3a; }
        .chip-status-inactive{ background: #fff5f5; color: var(--red); }
        .chip-dash           { color: var(--text-muted); font-size: 12px; }

        /* ══════════════════════════════════════
           TABLE ICON BUTTONS
        ══════════════════════════════════════ */
        .table-actions { display: flex; gap: 6px; align-items: center; }
        .table-icon-btn { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; font-size: 14px; border: none; cursor: pointer; transition: opacity 0.15s, transform 0.1s; text-decoration: none; }
        .table-icon-btn:hover { opacity: 0.8; transform: translateY(-1px); }
        .table-icon-btn.view   { background: var(--blue-light); color: var(--blue); }
        .table-icon-btn.edit   { background: #f4f0ff; color: #7c3aed; }
        .table-icon-btn.delete { background: #fff5f5; color: var(--red); }
        .table-icon-btn.archive{ background: #fff8f0; color: var(--orange); }

        /* ══════════════════════════════════════
           FILTER PILLS
        ══════════════════════════════════════ */
        .filter-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px; }
        .filter-pills { display: flex; gap: 6px; flex-wrap: wrap; }
        .filter-pill { padding: 7px 16px; border-radius: 20px; border: 1.5px solid var(--border); background: #fff; color: var(--text-secondary); font-size: 12.5px; font-weight: 500; cursor: pointer; transition: all 0.15s; font-family: 'Inter', sans-serif; text-decoration: none; }
        .filter-pill:hover { border-color: var(--blue); color: var(--blue); }
        .filter-pill.active { background: var(--blue); border-color: var(--blue); color: #fff; font-weight: 600; }

        /* ══════════════════════════════════════
           BTN TABLE ACTION
        ══════════════════════════════════════ */
        .btn-table-action { display: flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; font-family: 'Inter', sans-serif; transition: opacity 0.15s; text-decoration: none; }
        .btn-table-action.blue  { background: var(--blue); color: #fff; }
        .btn-table-action.blue:hover { opacity: 0.88; }
        .btn-back-link { display: flex; align-items: center; gap: 6px; padding: 11px 22px; border-radius: 8px; border: 1.5px solid var(--border); background: #fff; color: var(--text-secondary); font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.18s; cursor: pointer; font-family: 'Inter', sans-serif; }
        .btn-back-link:hover { border-color: var(--red); color: var(--red); }

        /* ══════════════════════════════════════
           MODAL SYSTEM
        ══════════════════════════════════════ */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 300; align-items: center; justify-content: center; padding: 1rem; }
        .modal-overlay.open { display: flex; }
        .modal-box-sm { background: #fff; border-radius: 14px; padding: 1.5rem; width: 100%; max-width: 420px; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 64px rgba(0,0,0,0.18); animation: modalDropIn 0.2s ease; }
        .modal-box-lg { background: #fff; border-radius: 14px; padding: 1.5rem; width: 100%; max-width: 640px; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 64px rgba(0,0,0,0.18); animation: modalDropIn 0.2s ease; }
        @keyframes modalDropIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .modal-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
        .modal-title-sm { font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
        .modal-title-sm i { color: var(--blue); }
        .modal-close { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--border); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 14px; }
        .modal-close:hover { background: #fff5f5; color: var(--red); border-color: var(--red); }
        .modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.5rem; }
        .modal-form-group { margin-bottom: 0.85rem; }
        .modal-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #555; margin-bottom: 5px; }
        .modal-input { width: 100%; padding: 10px 12px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #111; outline: none; transition: border-color 0.2s; background: #fff; }
        .modal-input:focus { border-color: var(--blue); }
        .modal-hint { font-size: 11px; margin-top: 3px; }
        .modal-hint.error   { color: var(--red); }
        .modal-hint.success { color: #1a6b3a; }
        .modal-btn-primary { width: 100%; padding: 11px; background: var(--blue); color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 1rem; transition: background 0.2s; }
        .modal-btn-primary:hover { background: var(--blue-dark); }

        /* ══════════════════════════════════════
           PAGINATION
        ══════════════════════════════════════ */
        .pagination { display: flex; align-items: center; gap: 4px; list-style: none; flex-wrap: wrap; }
        .pagination li { display: flex; }
        .pagination li span, .pagination li a { display: flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; font-size: 13px; font-weight: 500; border-radius: 8px; text-decoration: none; color: var(--text-secondary); border: 1px solid var(--border); background: #fff; transition: all 0.15s; }
        .pagination li a:hover { background: var(--blue-light); border-color: var(--blue); color: var(--blue); }
        .pagination li.active span { background: var(--blue); border-color: var(--blue); color: #fff; font-weight: 600; }
        .pagination li.disabled span { color: var(--text-muted); background: #fafafa; cursor: not-allowed; }
        .pagination-wrap { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 1rem 1.25rem; border-top: 1px solid var(--border); }
        .pagination-info { font-size: 12.5px; color: var(--text-muted); }

        /* ══════════════════════════════════════
           ALERTS
        ══════════════════════════════════════ */
        .alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 10px; margin-bottom: 1rem; animation: slideDown 0.35s ease; }
        .alert-success { background: #f0faf4; border: 1.5px solid #1a6b3a; }
        .alert-success i { color: #1a6b3a; }
        .alert-error { background: #fff5f5; border: 1.5px solid var(--red); }
        .alert-error i { color: var(--red); }
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
        .settings-header { padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); background: var(--blue-light); }
        .settings-user-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .settings-user-email { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .settings-item { display: flex; align-items: center; gap: 10px; padding: 10px 1rem; font-size: 13px; font-weight: 500; color: var(--text-secondary); text-decoration: none; transition: background 0.15s; width: 100%; background: none; border: none; cursor: pointer; font-family: 'Inter', sans-serif; text-align: left; }
        .settings-item:hover { background: var(--blue-light); color: var(--blue); }
        .settings-item i { font-size: 16px; color: var(--blue); }
        .settings-logout { color: var(--red) !important; }
        .settings-logout i { color: var(--red) !important; }
        .settings-logout:hover { background: #fff5f5 !important; }
        .settings-divider { height: 1px; background: var(--border); margin: 4px 0; }

        /* ══════════════════════════════════════
           SCROLLBAR
        ══════════════════════════════════════ */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c5cce0; border-radius: 4px; }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media(max-width:1024px) { .stats-grid { grid-template-columns: repeat(2,1fr); } .three-col { grid-template-columns: 1fr; } }
        @media(max-width:768px)  { .sidebar { transform: translateX(-100%); } .sidebar.open { transform: translateX(0); } .topbar, .main-wrap { left: 0; margin-left: 0; } .stats-grid { grid-template-columns: 1fr 1fr; } .two-col { grid-template-columns: 1fr; } }
        @media(max-width:560px)  { .modal-grid { grid-template-columns: 1fr; } .detail-grid { grid-template-columns: 1fr; } }
    </style>
    @stack('styles')
</head>
<body>

@include('partials.sidebar')

<div class="main-wrap">
    @include('partials.topbar')

    <div class="main-content">

        @if(session('error'))
        <div class="alert alert-error"><i class="ti ti-alert-circle"></i><div class="alert-text"><strong>Error</strong>{{ session('error') }}</div></div>
        @endif

        @if(session('success'))
        <div class="alert alert-success"><i class="ti ti-circle-check"></i><div class="alert-text"><strong>Success</strong>{{ session('success') }}</div></div>
        @endif

        @yield('content')
    </div>
</div>

<script>
function updateClock() {
    const now  = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const el   = document.getElementById('live-clock');
    if (el) el.textContent = time;
}
setInterval(updateClock, 1000);
updateClock();

function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
}

function toggleSettings() {
    document.getElementById('settings-dropdown').classList.toggle('open');
}

document.addEventListener('click', function(e) {
    const wrap = document.getElementById('settings-wrap');
    if (wrap && !wrap.contains(e.target)) {
        const dd = document.getElementById('settings-dropdown');
        if (dd) dd.classList.remove('open');
    }
});
</script>

@stack('scripts')
</body>
</html>