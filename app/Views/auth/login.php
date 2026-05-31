<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login — MABAC DSS</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #1a56db;
            --radius: 8px;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
        }
        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-brand h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        .login-brand p {
            font-size: 0.875rem;
            color: var(--muted);
            margin-top: 0.25rem;
        }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.375rem; }
        .form-control {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.9rem;
            transition: border-color 0.15s;
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,86,219,0.12); }
        .btn-primary {
            width: 100%;
            padding: 0.65rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: opacity 0.15s;
        }
        .btn-primary:hover { opacity: 0.88; }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: var(--radius);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-brand">
            <h1>MABAC DSS</h1>
            <p>Sistem Pendukung Keputusan Hotel</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error">✕ <?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                    value="<?= old('email') ?>" placeholder="email@domain.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                    placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-primary">Masuk</button>
        </form>

        <div class="login-footer">
            Lanjut tanpa akun?
            <a href="/hotels" style="color: #1a56db">Cari Hotel</a>
        </div>
    </div>
</body>
</html>
