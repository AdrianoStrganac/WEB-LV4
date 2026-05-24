<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (jePrijavljen()) { header("Location: index.php"); exit; }

$greska = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnicko_ime = trim($_POST['korisnicko_ime'] ?? '');
    $lozinka        = $_POST['lozinka'] ?? '';
    if (empty($korisnicko_ime) || empty($lozinka)) {
        $greska = "Upiši korisničko ime i lozinku.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM korisnici WHERE korisnicko_ime = ?");
        $stmt->execute([$korisnicko_ime]);
        $korisnik = $stmt->fetch();
        if ($korisnik && password_verify($lozinka, $korisnik['lozinka'])) {
            postaviSesiju($korisnik);
            header("Location: index.php"); exit;
        } else {
            $greska = "Pogrešno korisničko ime ili lozinka.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prijava</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; background: #f5f7fa; color: #333; }
        header { background: linear-gradient(135deg, #4f46e5, #3b82f6); color: white; padding: 20px; text-align: center; }
        .Menu { position: relative; }
        #menu-toggle { display: none; }
        .Menu label { display: block; padding: 15px; background: #1e293b; color: white; cursor: pointer; }
        .SubMenu { display: none; position: absolute; top: 100%; left: 0; width: 200px; background: #1e293b; border: 2px solid white; list-style: none; padding: 0; margin: 0; z-index: 100; }
        #menu-toggle:checked ~ .SubMenu { display: block; }
        .SubMenu li { padding: 10px; } .SubMenu li:hover { background: #334155; }
        .SubMenu li a { color: white; text-decoration: none; }
        footer { background: #1e293b; color: white; text-align: center; padding: 15px; margin-top: 30px; }
        .auth-box { max-width: 420px; margin: 60px auto; padding: 32px; background: #fff; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,.1); }
        .auth-box h2 { margin-bottom: 20px; }
        .auth-box label { display: block; margin-top: 12px; font-weight: bold; }
        .auth-box input { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .auth-box button { margin-top: 20px; width: 100%; padding: 10px; background: #4f46e5; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        .auth-box button:hover { background: #4338ca; }
        .greska { background: #fdd; border: 1px solid #c00; padding: 10px; border-radius: 4px; margin-bottom: 12px; }
        .auth-link { margin-top: 14px; text-align: center; }
    </style>
</head>
<body>
<div id="div1">
    <header><h1>Web aplikacija za planiranje playliste</h1></header>
    <nav class="Menu">
        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle">Menu</label>
        <ul class="SubMenu">
            <li><a href="index.php">Početna</a></li>
            <li><a href="grafikon.html">Grafikoni</a></li>
            <li><a href="gallery.php">Galerija</a></li>
            <li><a href="register.php">Registracija</a></li>
        </ul>
    </nav>
</div>
<div class="auth-box">
    <h2>Prijava</h2>
    <?php if ($greska): ?><div class="greska"><?= htmlspecialchars($greska) ?></div><?php endif; ?>
    <form method="POST" action="login.php">
        <label>Korisničko ime</label>
        <input type="text" name="korisnicko_ime" value="<?= htmlspecialchars($_POST['korisnicko_ime'] ?? '') ?>" required autofocus>
        <label>Lozinka</label>
        <input type="password" name="lozinka" required>
        <button type="submit">Prijavi se</button>
    </form>
    <p class="auth-link">Nemaš račun? <a href="register.php">Registriraj se</a></p>
</div>
<footer><p>© 2026. Web Programiranje.</p></footer>
</body>
</html>
