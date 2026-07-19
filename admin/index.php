<?php
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (checkPassword($password)) {
        $_SESSION['tropea_auth'] = true;
        header('Location: /admin/dashboard.php');
        exit;
    }
    $error = 'Contraseña incorrecta';
}

if (isLoggedIn()) {
    header('Location: /admin/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin — Tropea Boutique</title>
  <link rel="icon" href="/assets/logo.svg" type="image/svg+xml"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Inter',sans-serif;background:#0f0f0f;min-height:100vh;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
    .bg{position:absolute;inset:0;background:radial-gradient(ellipse at 30% 50%,rgba(212,96,122,.12) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(244,167,185,.08) 0%,transparent 50%)}
    .card{position:relative;z-index:2;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:24px;padding:48px 40px;width:100%;max-width:400px;backdrop-filter:blur(20px)}
    .logo-wrap{text-align:center;margin-bottom:36px}
    .logo-wrap img{height:48px;filter:invert(1) brightness(1.8);opacity:.9}
    .badge{display:inline-block;font-size:10px;font-weight:600;letter-spacing:2.5px;text-transform:uppercase;color:#d4607a;background:rgba(212,96,122,.12);padding:4px 12px;border-radius:20px;margin-top:10px}
    h1{font-family:'Playfair Display',serif;font-size:26px;color:#fff;text-align:center;margin-bottom:8px}
    .subtitle{font-size:13px;color:rgba(255,255,255,.4);text-align:center;margin-bottom:36px}
    label{display:block;font-size:11px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:8px}
    input[type=password]{width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:14px 16px;font-size:15px;color:#fff;font-family:'Inter',sans-serif;outline:none;transition:border-color .2s}
    input[type=password]:focus{border-color:#d4607a;background:rgba(212,96,122,.06)}
    .btn{width:100%;margin-top:20px;padding:15px;background:#d4607a;color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;transition:all .2s;letter-spacing:.3px}
    .btn:hover{background:#c0506a;transform:translateY(-1px)}
    .error{background:rgba(212,96,122,.15);border:1px solid rgba(212,96,122,.3);border-radius:10px;padding:12px 16px;font-size:13px;color:#f4a7b9;margin-bottom:20px;text-align:center}
    .fish{position:absolute;opacity:.06;font-size:120px;pointer-events:none}
    .f1{top:-30px;left:-40px;transform:rotate(-15deg)}
    .f2{bottom:-20px;right:-30px;transform:rotate(20deg)}
  </style>
</head>
<body>
  <div class="bg"></div>
  <div class="card">
    <div class="logo-wrap">
      <img src="/assets/logo.svg" alt="Tropea Boutique"/>
      <br/><span class="badge">Panel de administración</span>
    </div>
    <h1>Bienvenida 🌸</h1>
    <p class="subtitle">Introduce tu contraseña para entrar</p>
    <?php if (!empty($error)): ?>
      <div class="error">🔒 <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" placeholder="••••••••" autofocus required/>
      <button type="submit" class="btn">Entrar al panel →</button>
    </form>
  </div>
</body>
</html>
