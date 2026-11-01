<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="<?= _APP_ROOT ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="site-header">
            <div class="brand">
                <span class="brand-mark" aria-hidden="true">k</span>
                <span>kredyt.</span>
            </div>
            <span class="header-note">Kalkulator rat kredytu</span>
        </header>

        <main class="form-card login-card">
            <p class="eyebrow">Kalkulator kredytu</p>
            <h1>Zaloguj się</h1>
            <p class="form-note">Zaloguj się, aby przejść do kalkulatora.</p>

            <form action="<?= _APP_ROOT ?>/app/security/login.php" method="post">
                <div class="field">
                    <label for="id_login">Login</label>
                    <input id="id_login" type="text" name="login" autocomplete="username"
                        placeholder="Wpisz login" value="<?= htmlspecialchars($form['login'] ?? '') ?>">
                </div>
                <div class="field">
                    <label for="id_pass">Hasło</label>
                    <input id="id_pass" type="password" name="pass" autocomplete="current-password"
                        placeholder="Wpisz hasło">
                </div>
                <button type="submit">Zaloguj się <span aria-hidden="true">→</span></button>
            </form>

            <?php if (!empty($messages)): ?>
                <div class="error" role="alert">
                    <ul class="login-errors">
                        <?php foreach ($messages as $msg): ?>
                            <li><?= htmlspecialchars($msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </main>

        <footer class="site-footer">
            <span>Kalkulator rat kredytu</span>
            <span>Raty równe i malejące · Kwoty w PLN</span>
        </footer>
    </div>
</body>
</html>
