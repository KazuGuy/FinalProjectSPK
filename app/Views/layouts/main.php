<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MABAC Hotel DSS' ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1a56db;
            --primary-dark: #1e429f;
            --sidebar-w: 240px;
            --radius: 8px;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
            --bg: #f9fafb;
            --surface: #ffffff;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
            letter-spacing: -0.3px;
        }

        .sidebar-brand span {
            font-weight: 400;
            color: var(--muted);
            font-size: 0.75rem;
            display: block;
            margin-top: 2px;
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            flex: 1;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            padding: 0.5rem 0.75rem 0.25rem;
            margin-top: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius);
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            transition: background 0.15s, color 0.15s;
        }

        .nav-link:hover { background: #f3f4f6; color: var(--text); }
        .nav-link.active { background: #eff6ff; color: var(--primary); font-weight: 600; }
        .nav-link svg { width: 18px; height: 18px; flex-shrink: 0; }

        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            font-size: 0.8rem;
            color: var(--muted);
        }

        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0.875rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title { font-weight: 600; font-size: 1rem; }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            font-size: 0.875rem;
            color: var(--muted);
        }

        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex; align-items: center; justify-content: center;
        }

        .content { padding: 1.75rem; flex: 1; }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .card-title { font-weight: 600; font-size: 0.9375rem; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: opacity 0.15s, background 0.15s;
        }
        .btn:hover { opacity: 0.88; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-danger  { background: #ef4444; color: white; }
        .btn-ghost   { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-sm      { padding: 0.3rem 0.625rem; font-size: 0.8rem; }

        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        th { text-align: left; font-weight: 600; padding: 0.625rem 0.875rem; border-bottom: 2px solid var(--border); color: var(--muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; }
        td { padding: 0.75rem 0.875rem; border-bottom: 1px solid var(--border); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: var(--bg); }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .badge-benefit { background: #dcfce7; color: #166534; }
        .badge-cost    { background: #fee2e2; color: #991b1b; }

        .form-group { margin-bottom: 1.125rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.375rem; }
        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.875rem;
            background: var(--surface);
            color: var(--text);
            transition: border-color 0.15s;
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,86,219,0.12); }
        .form-hint  { font-size: 0.78rem; color: var(--muted); margin-top: 0.25rem; }
        .form-error { font-size: 0.78rem; color: #ef4444; margin-top: 0.25rem; }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .page-header h1 { font-size: 1.25rem; font-weight: 700; }
        .page-header p  { font-size: 0.875rem; color: var(--muted); margin-top: 0.125rem; }
    </style>
</head>
<body>

<?php
// ── Satu tempat ambil session, aman untuk guest ──
$currentUser = session()->get('user') ?? ['name' => 'Guest', 'role' => 'guest'];
$isAdmin     = $currentUser['role'] === 'admin';
$isGuest     = $currentUser['role'] === 'guest';
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        MABAC DSS
        <span>Hotel Recommender</span>
    </div>
    <nav class="sidebar-nav">
        <?php if ($isAdmin): ?>
            <div class="nav-label">Admin</div>
            <a href="/admin/criteria" class="nav-link <?= str_starts_with(uri_string(), 'admin/criteria') ? 'active' : '' ?>">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Kriteria
            </a>
            <a href="/admin/hotels" class="nav-link <?= str_starts_with(uri_string(), 'admin/hotels') ? 'active' : '' ?>">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Hotel
            </a>
            <a href="/admin/poi" class="nav-link <?= str_starts_with(uri_string(), 'admin/poi') ? 'active' : '' ?>">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Point of Interest
            </a>
        <?php else: ?>
            <div class="nav-label">Menu</div>
            <a href="/hotels" class="nav-link <?= str_starts_with(uri_string(), 'hotels') ? 'active' : '' ?>">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari Hotel
            </a>
            <a href="/evaluation" class="nav-link <?= str_starts_with(uri_string(), 'evaluation') ? 'active' : '' ?>">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Evaluasi MABAC
            </a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        v1.0 &mdash; DSS MABAC
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <span class="topbar-title"><?= $title ?? '' ?></span>
        <div class="topbar-user">
            <div class="avatar"><?= strtoupper(substr($currentUser['name'], 0, 1)) ?></div>
            <?= esc($currentUser['name']) ?>
            &nbsp;|&nbsp;
            <?php if ($isGuest): ?>
                <a href="/login" style="color: var(--muted); text-decoration: none; font-size: 0.8rem;">Login</a>
            <?php else: ?>
                <a href="/logout" style="color: var(--muted); text-decoration: none; font-size: 0.8rem;">Keluar</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">✓ <?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">✕ <?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>