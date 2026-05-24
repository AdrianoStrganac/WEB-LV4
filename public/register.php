<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (jePrijavljen()) { header("Location: index.php"); exit; }

$greske = []; $uspjeh = ''; $old = ['korisnicko_ime' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnicko_ime = trim($_POST['korisnicko_ime'] ?? '');
    $email          = trim($_POST['email']          ?? '');
    $lozinka        = $_POST['lozinka']              ?? '';
    $lozinka2       = $_POST['lozinka2']             ?? '';

    if (empty($korisnicko_ime) || strlen($korisnicko_ime) < 3) $greske[] = "Korisničko ime mora imati najmanje 3 znaka.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))     $greske[] = "Email nije ispravan.";
    if (strlen($lozinka) < 6)                                            $greske[] = "Lozinka mora imati najmanje 6 znakova.";
    if ($lozinka !== $lozinka2)                                          $greske[] = "Lozinke se ne podudaraju.";

    if (empty($greske)) {
        $stmt = $pdo->prepare("SELECT id FROM korisnici WHERE korisnicko_ime = ? OR email = ?");
        $stmt->execute([$korisnicko_ime, $email]);
        if ($stmt->fetch()) $greske[] = "Korisničko ime ili email već postoji.";
    }

    if (empty($greske)) {
        $hash = password_hash($lozinka, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO korisnici (korisnicko_ime, email, lozinka) VALUES (?, ?, ?)");
        $stmt->execute([$korisnicko_ime, $email, $hash]);
        $uspjeh = "Registracija uspješna! <a href='login.php'>Prijavi se</a>.";
    } else {
        $old = ['korisnicko_ime' => htmlspecialchars($korisnicko_ime), 'email' => htmlspecialchars($email)];
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registracija</title>
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
        .greska  { background: #fdd; border: 1px solid #c00; padding: 10px; border-radius: 4px; margin-bottom: 12px; }
        .greska ul { margin: 0; padding-left: 18px; }
        .uspjeh  { background: #dfd; border: 1px solid #0a0; padding: 10px; border-radius: 4px; margin-bottom: 12px; }
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
            <li><a href="login.php">Prijava</a></li>
        </ul>
    </nav>
</div>
<div class="auth-box">
    <h2>Registracija</h2>
    <?php if (!empty($greske)): ?>
        <div class="greska"><ul><?php foreach($greske as $g): ?><li><?= $g ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>
    <?php if ($uspjeh): ?>
        <div class="uspjeh"><?= $uspjeh ?></div>
    <?php else: ?>
    <form method="POST" action="register.php">
        <label>Korisničko ime</label>
        <input type="text" name="korisnicko_ime" value="<?= $old['korisnicko_ime'] ?>" required maxlength="50">
        <label>Email</label>
        <input type="email" name="email" value="<?= $old['email'] ?>" required>
        <label>Lozinka <small>(min. 6 znakova)</small></label>
        <input type="password" name="lozinka" required minlength="6">
        <label>Ponovi lozinku</label>
        <input type="password" name="lozinka2" required>
        <button type="submit">Registriraj se</button>
    </form>
    <p class="auth-link">Već imaš račun? <a href="login.php">Prijavi se</a></p>
    <?php endif; ?>
</div>
<footer><p>© 2026. Web Programiranje.</p></footer>
</body>
</html>
