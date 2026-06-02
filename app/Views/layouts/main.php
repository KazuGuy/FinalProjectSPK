<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'TravelDSS Hotel' ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #0194f3;
            --primary-dark: #007ce0;
            --primary-soft: #eaf7ff;
            --accent: #ff6d00;
            --accent-soft: #fff3e8;
            --success: #0f9f6e;
            --danger: #d93025;
            --warning: #b7791f;
            --border: #dde5ef;
            --border-strong: #c8d5e3;
            --text: #1f2d3d;
            --muted: #68788c;
            --bg: #f5f7fb;
            --surface: #ffffff;
            --radius: 8px;
            --shadow: 0 10px 28px rgba(31, 45, 61, 0.09);
            --nav-h: 66px;
        }

        body {
            min-height: 100vh;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        a { color: var(--primary); }

        .app-shell { min-height: 100vh; }

        .site-nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            height: var(--nav-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 14px rgba(31, 45, 61, 0.06);
        }

        .nav-inner {
            width: min(1180px, calc(100% - 32px));
            height: 100%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            color: var(--text);
            text-decoration: none;
            min-width: max-content;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary), #00b8ff);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 800;
            letter-spacing: 0;
        }

        .brand-copy strong {
            display: block;
            color: var(--primary-dark);
            font-size: 1.02rem;
            line-height: 1.1;
        }

        .brand-copy span {
            display: block;
            color: var(--muted);
            font-size: 0.74rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.2rem;
            flex: 1;
            justify-content: center;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 650;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.15s, color 0.15s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--primary-soft);
            color: var(--primary-dark);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            min-width: max-content;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            color: var(--muted);
            font-size: 0.86rem;
            font-weight: 600;
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            display: grid;
            place-items: center;
            font-weight: 800;
        }

        .content-wrap {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 1.4rem 0 2.5rem;
        }

        .page-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin: 0 0 1.1rem;
        }

        .page-header h1 {
            font-size: clamp(1.28rem, 2vw, 1.8rem);
            font-weight: 800;
            color: var(--text);
        }

        .page-header p {
            color: var(--muted);
            font-size: 0.92rem;
            margin-top: 0.18rem;
        }

        .hero-band {
            margin: -1.4rem calc(50% - 50vw) 1.4rem;
            padding: 2rem max(16px, calc((100vw - 1180px) / 2)) 4.3rem;
            color: #fff;
            background:
                linear-gradient(120deg, rgba(0, 124, 224, 0.96), rgba(1, 148, 243, 0.9)),
                url("https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1800&q=80") center/cover;
        }

        .hero-band h1 {
            max-width: 760px;
            font-size: clamp(1.8rem, 4vw, 3.2rem);
            line-height: 1.05;
            font-weight: 850;
        }

        .hero-band p {
            max-width: 640px;
            margin-top: 0.65rem;
            color: rgba(255, 255, 255, 0.88);
            font-size: 1rem;
        }

        .search-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 1rem;
        }

        .search-panel.overlap { margin-top: -4.2rem; margin-bottom: 1.2rem; }

        .search-grid {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 160px 160px auto;
            gap: 0.75rem;
            align-items: end;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 1rem;
            align-items: start;
        }

        .results-stack { display: flex; flex-direction: column; gap: 0.85rem; }

        .card,
        .filter-panel,
        .result-card,
        .map-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(31, 45, 61, 0.05);
        }

        .card { padding: 1.25rem; }
        .filter-panel { padding: 1rem; position: sticky; top: calc(var(--nav-h) + 16px); }
        .result-card { padding: 1rem; }
        .map-card { overflow: hidden; }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            min-height: 38px;
            padding: 0.52rem 0.95rem;
            border: 1px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.88rem;
            font-weight: 750;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.15s, border-color 0.15s, color 0.15s, opacity 0.15s;
        }

        .btn:hover { opacity: 0.9; }
        .btn:disabled { cursor: not-allowed; opacity: 0.5; }
        .btn-primary { background: var(--accent); color: #fff; border-color: var(--accent); }
        .btn-blue { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-danger { background: var(--danger); color: #fff; border-color: var(--danger); }
        .btn-ghost { background: #fff; border-color: var(--border-strong); color: var(--text); }
        .btn-soft { background: var(--primary-soft); color: var(--primary-dark); border-color: transparent; }
        .btn-sm { min-height: 30px; padding: 0.3rem 0.62rem; font-size: 0.78rem; }

        .form-group { margin-bottom: 1rem; }
        .form-label {
            display: block;
            color: var(--text);
            font-size: 0.82rem;
            font-weight: 750;
            margin-bottom: 0.35rem;
        }

        .form-control {
            width: 100%;
            min-height: 40px;
            padding: 0.58rem 0.72rem;
            border: 1px solid var(--border-strong);
            border-radius: 8px;
            background: #fff;
            color: var(--text);
            font: inherit;
            font-size: 0.9rem;
        }

        textarea.form-control { min-height: 96px; resize: vertical; }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(1, 148, 243, 0.16);
        }

        .form-hint { color: var(--muted); font-size: 0.78rem; margin-top: 0.28rem; }
        .form-error { color: var(--danger); font-size: 0.78rem; margin-top: 0.28rem; }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            border-radius: 8px;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .alert-success { background: #eafaf3; color: #08734f; border: 1px solid #bcebd8; }
        .alert-error { background: #fff0ee; color: var(--danger); border: 1px solid #ffc9c2; }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .badge-benefit { background: #eafaf3; color: #08734f; }
        .badge-cost { background: #fff0ee; color: var(--danger); }
        .badge-deal { background: var(--accent-soft); color: #bd4d00; }

        .review-score {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            height: 32px;
            padding: 0 0.55rem;
            border-radius: 8px 8px 8px 2px;
            background: #0a6ada;
            color: #fff;
            font-weight: 850;
            font-size: 0.88rem;
        }

        .muted { color: var(--muted); }
        .price { color: var(--accent); font-weight: 850; }
        .split-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        th {
            text-align: left;
            padding: 0.78rem 0.85rem;
            color: var(--muted);
            background: #f7faff;
            border-bottom: 1px solid var(--border);
            font-size: 0.75rem;
            font-weight: 850;
            text-transform: uppercase;
        }

        td {
            padding: 0.85rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fbfdff; }

        .map-search {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0.55rem;
            margin-bottom: 0.75rem;
        }

        .map-results {
            display: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            margin: -0.35rem 0 0.75rem;
            background: #fff;
        }

        .map-result-item {
            width: 100%;
            display: block;
            text-align: left;
            padding: 0.65rem 0.75rem;
            border: 0;
            border-bottom: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            cursor: pointer;
            font: inherit;
            font-size: 0.84rem;
        }

        .map-result-item:last-child { border-bottom: 0; }
        .map-result-item:hover { background: var(--primary-soft); }

        .locked-note {
            background: #fff8ed;
            border: 1px solid #ffd8a8;
            color: #8a4b00;
            border-radius: 8px;
            padding: 1rem;
        }

        .pager { margin-top: 1.25rem; }

        @media (max-width: 900px) {
            .site-nav { height: auto; }
            .nav-inner {
                width: min(100% - 24px, 720px);
                min-height: var(--nav-h);
                flex-wrap: wrap;
                padding: 0.65rem 0;
            }
            .nav-links {
                order: 3;
                width: 100%;
                overflow-x: auto;
                justify-content: flex-start;
                padding-bottom: 0.1rem;
            }
            .content-wrap { width: min(100% - 24px, 720px); }
            .content-grid,
            .search-grid {
                grid-template-columns: 1fr;
            }
            .filter-panel { position: static; }
            .page-header { align-items: flex-start; flex-direction: column; }
        }

        @media (max-width: 560px) {
            .brand-copy span { display: none; }
            .nav-actions { width: 100%; justify-content: space-between; }
            .user-chip span { max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .card,
            .result-card,
            .filter-panel { padding: 1rem; }
            .hero-band { padding-top: 1.4rem; }
        }
    </style>
</head>
<body>
<?php
$currentUser = session()->get('user') ?? ['name' => 'Guest', 'role' => 'guest'];
$role        = $currentUser['role'] ?? 'guest';
$isAdmin     = $role === 'admin';
$isGuest     = $role === 'guest';
$isUser      = $role === 'user';
$initial     = strtoupper(substr($currentUser['name'] ?? 'G', 0, 1));
$active      = static fn(string $prefix): string => str_starts_with(uri_string(), $prefix) ? 'active' : '';
?>

<div class="app-shell">
    <header class="site-nav">
        <div class="nav-inner">
            <a href="<?= $isAdmin ? '/admin/dashboard' : '/hotels' ?>" class="brand">
                <span class="brand-mark">TD</span>
                <span class="brand-copy">
                    <strong>TravelDSS</strong>
                    <span>Hotel decision support</span>
                </span>
            </a>

            <nav class="nav-links" aria-label="Navigasi utama">
                <?php if ($isAdmin): ?>
                    <a href="/admin/dashboard" class="nav-link <?= $active('admin/dashboard') ?>">Dashboard</a>
                    <a href="/admin/criteria" class="nav-link <?= $active('admin/criteria') ?>">Kriteria</a>
                    <a href="/admin/hotels" class="nav-link <?= $active('admin/hotels') ?>">Hotel</a>
                    <a href="/admin/poi" class="nav-link <?= $active('admin/poi') ?>">POI</a>
                <?php else: ?>
                    <a href="/hotels" class="nav-link <?= $active('hotels') ?>">Hotel</a>
                    <a href="/poi" class="nav-link <?= $active('poi') ?>">POI</a>
                    <a href="/evaluation" class="nav-link <?= $active('evaluation') ?>">Evaluasi DSS</a>
                <?php endif; ?>
            </nav>

            <div class="nav-actions">
                <?php if ($isGuest): ?>
                    <a href="/guest" class="btn btn-soft btn-sm">Guest</a>
                    <a href="/login" class="btn btn-ghost btn-sm">Masuk</a>
                    <a href="/register" class="btn btn-primary btn-sm">Daftar</a>
                <?php else: ?>
                    <div class="user-chip">
                        <span class="avatar"><?= esc($initial) ?></span>
                        <span><?= esc($currentUser['name']) ?></span>
                    </div>
                    <a href="/logout" class="btn btn-ghost btn-sm">Keluar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="content-wrap">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>
