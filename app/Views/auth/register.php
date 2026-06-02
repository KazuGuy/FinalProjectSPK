<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - TravelDSS</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #0194f3;
            --primary-dark: #007ce0;
            --accent: #ff6d00;
            --border: #dde5ef;
            --text: #1f2d3d;
            --muted: #68788c;
            --surface: #ffffff;
            --radius: 8px;
        }
        body {
            min-height: 100vh;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            background:
                linear-gradient(120deg, rgba(0,124,224,0.9), rgba(1,148,243,0.76)),
                url("https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1800&q=80") center/cover;
            color: var(--text);
            display: grid;
            place-items: center;
            padding: 1.25rem;
        }
        .auth-shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: minmax(0, 1fr) 430px;
            gap: 1.25rem;
            align-items: center;
        }
        .auth-copy { color: #fff; }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 1.4rem;
            color: #fff;
            text-decoration: none;
            font-weight: 850;
            font-size: 1.1rem;
        }
        .brand-mark {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: rgba(255,255,255,0.18);
            display: grid;
            place-items: center;
        }
        .auth-copy h1 {
            max-width: 640px;
            font-size: clamp(2rem, 5vw, 3.6rem);
            line-height: 1.04;
            font-weight: 850;
        }
        .auth-copy p {
            max-width: 520px;
            margin-top: 0.9rem;
            color: rgba(255,255,255,0.86);
            font-size: 1.02rem;
        }
        .auth-card {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid rgba(255,255,255,0.35);
            box-shadow: 0 20px 48px rgba(31,45,61,0.24);
            padding: 1.35rem;
        }
        .auth-card h2 { font-size: 1.35rem; font-weight: 850; margin-bottom: 0.2rem; }
        .auth-card .subtitle { color: var(--muted); font-size: 0.9rem; margin-bottom: 1.1rem; }
        .form-group { margin-bottom: 0.95rem; }
        .form-label { display: block; margin-bottom: 0.34rem; font-size: 0.82rem; font-weight: 750; }
        .form-control {
            width: 100%;
            min-height: 42px;
            padding: 0.62rem 0.72rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font: inherit;
            font-size: 0.92rem;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(1,148,243,0.16);
        }
        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 42px;
            width: 100%;
            border: 1px solid transparent;
            border-radius: var(--radius);
            cursor: pointer;
            font: inherit;
            font-weight: 800;
            text-decoration: none;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .alert-error {
            background: #fff0ee;
            color: #d93025;
            border: 1px solid #ffc9c2;
            border-radius: var(--radius);
            padding: 0.75rem 0.9rem;
            font-size: 0.86rem;
            font-weight: 650;
            margin-bottom: 1rem;
        }
        .alert-error div + div { margin-top: 0.3rem; }
        .auth-footer {
            margin-top: 1rem;
            color: var(--muted);
            font-size: 0.86rem;
            text-align: center;
        }
        .auth-footer a { color: var(--primary-dark); font-weight: 800; text-decoration: none; }
        @media (max-width: 820px) {
            .auth-shell { grid-template-columns: 1fr; }
            .auth-copy h1 { font-size: 2.1rem; }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <section class="auth-copy">
            <a class="brand" href="/hotels">
                <span class="brand-mark">TD</span>
                <span>TravelDSS</span>
            </a>
            <h1>Buat akun pengguna untuk evaluasi DSS.</h1>
            <p>Registrasi ini hanya membuat akun user. Akun admin dibuat oleh sistem melalui seeder atau proses internal.</p>
        </section>

        <section class="auth-card" aria-label="Form registrasi">
            <h2>Daftar Pengguna</h2>
            <p class="subtitle">Setelah daftar, Anda langsung masuk dan dapat memakai evaluasi MABAC.</p>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert-error">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/register">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control"
                        value="<?= old('name') ?>" placeholder="Nama lengkap" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= old('email') ?>" placeholder="email@domain.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control"
                        placeholder="Minimal 6 karakter" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirm" class="form-control"
                        placeholder="Ulangi password" required>
                </div>
                <button type="submit" class="btn btn-primary">Buat Akun User</button>
            </form>

            <div class="auth-footer">
                Sudah punya akun? <a href="/login">Masuk</a>
            </div>
        </section>
    </div>
</body>
</html>
