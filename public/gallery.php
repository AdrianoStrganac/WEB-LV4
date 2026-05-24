<?php
// public/gallery.php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$prijavljen = jePrijavljen();
$poruka     = '';
$poruka_tip = '';

if ($prijavljen && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_slike    = (int)($_POST['id_slike'] ?? 0);
    $ocjena      = (int)($_POST['ocjena']   ?? 0);
    $korisnik_id = $_SESSION['korisnik_id'];

    if ($id_slike > 0 && $ocjena >= 1 && $ocjena <= 5) {
        $stmt = $pdo->prepare("
            INSERT INTO ocjene (id_korisnika, id_slike, ocjena)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE ocjena = VALUES(ocjena), vrijeme_ocjene = CURRENT_TIMESTAMP
        ");
        $stmt->execute([$korisnik_id, $id_slike, $ocjena]);
        $poruka     = "✅ Ocjena spremljena!";
        $poruka_tip = 'uspjeh';
    } else {
        $poruka     = "⚠️ Nevažeća ocjena.";
        $poruka_tip = 'upozorenje';
    }
}

$korisnik_id = $prijavljen ? $_SESSION['korisnik_id'] : 0;

$stmt = $pdo->prepare("
    SELECT s.id, s.naziv_datoteke, s.opis, s.putanja,
           ROUND(AVG(o.ocjena), 1) AS prosjek,
           COUNT(o.ocjena) AS broj_ocjena,
           MAX(CASE WHEN o.id_korisnika = ? THEN o.ocjena END) AS moja_ocjena
    FROM slike s
    LEFT JOIN ocjene o ON o.id_slike = s.id
    GROUP BY s.id
    ORDER BY s.id ASC
");
$stmt->execute([$korisnik_id]);
$slike = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galerija – Playlist aplikacija</title>
    <style>
        /* ===== galerija_S.css ===== */
        header { background-color: #e88235; padding: 20px; text-align: start; }
        body { font-family: 'Abel', sans-serif; margin: 0; padding: 0; }
        h1 { font-weight: 100; font-size: 40px; text-align: center; margin-top: 30px; }
        .galerija h1 { color: darkblue; font-size: 2rem; text-align: center; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; padding: 20px; max-width: 1200px; margin: 0 auto; }
        .galerija_slika { display: flex; flex-direction: column; align-items: center; background: #f9f9f9; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.3s ease; }
        .galerija_slika:hover { transform: scale(1.05); }
        .galerija_slika img { width: 100%; height: auto; display: block; }
        .galerija_slika figcaption { padding: 10px; text-align: center; font-weight: bold; background: #fff; width: 100%; box-sizing: border-box; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); }
        .modal:target { display: flex; align-items: center; justify-content: center; }
        .modal-content { position: relative; max-width: 90%; max-height: 90%; }
        .modal-content img { width: 100%; height: auto; }
        .close { position: absolute; top: -10px; right: -10px; color: white; font-size: 30px; font-weight: bold; text-decoration: none; background: black; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
        .close:hover { background: #333; }
        footer { background-color: #e88235; padding: 10px; text-align: center; }

        /* ===== Nav (kopija iz index) ===== */
        .Menu { position: relative; }
        #menu-toggle { display: none; }
        .Menu label { display: block; padding: 15px; background: #1e293b; color: white; cursor: pointer; }
        .Menu label:hover { background: #334155; }
        .SubMenu { display: none; position: absolute; top: 100%; left: 0; width: 200px; background: #1e293b; border: 2px solid white; list-style: none; padding: 0; margin: 0; z-index: 100; }
        #menu-toggle:checked ~ .SubMenu { display: block; }
        .SubMenu li { padding: 10px; }
        .SubMenu li:hover { background: #334155; }
        .SubMenu li a { color: white; text-decoration: none; }

        /* ===== Ocjenjivanje ===== */
        .poruka { padding: 10px 16px; border-radius: 6px; margin: 12px 20px; font-weight: bold; }
        .poruka.uspjeh     { background: #d4edda; border: 1px solid #28a745; color: #155724; }
        .poruka.upozorenje { background: #fff3cd; border: 1px solid #ffc107; color: #856404; }
        .ocjena-bar { padding: 6px 10px 2px; font-size: .9rem; color: #555; text-align: center; }
        .zvjezdice-input { display: flex; justify-content: center; gap: 4px; flex-direction: row-reverse; }
        .zvjezdice-input input[type="radio"] { display: none; }
        .zvjezdice-input label { font-size: 1.5rem; color: #ddd; cursor: pointer; transition: color .15s; }
        .zvjezdice-input input:checked ~ label,
        .zvjezdice-input label:hover,
        .zvjezdice-input label:hover ~ label { color: #f5a623; }
        .ocjeni-forma { padding: 0 10px 10px; text-align: center; width: 100%; box-sizing: border-box; }
        .ocjeni-forma button { margin-top: 6px; padding: 4px 14px; background: #4f46e5; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: .85rem; }
        .ocjeni-forma button:hover { background: #4338ca; }
        .login-note { font-size: .78rem; color: #888; margin: 4px 0 8px; text-align: center; }
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
            <?php if ($prijavljen): ?>
                <li><a href="logout.php">Odjava (<?= htmlspecialchars($_SESSION['korisnicko_ime']) ?>)</a></li>
            <?php else: ?>
                <li><a href="login.php">Prijava</a></li>
                <li><a href="register.php">Registracija</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<section class="galerija">
    <h1>Galerija slika</h1>

    <?php if ($poruka): ?>
        <div class="poruka <?= $poruka_tip ?>"><?= $poruka ?></div>
    <?php endif; ?>

    <?php if (!$prijavljen): ?>
        <div class="poruka upozorenje">
            🔒 <a href="login.php">Prijavi se</a> kako bi mogao/la ocjenjivati slike.
        </div>
    <?php endif; ?>

    <div class="gallery-grid">
    <?php foreach ($slike as $slika):
        $id      = $slika['id'];
        $prosjek = $slika['prosjek'] ?? 0;
        $broj    = (int)$slika['broj_ocjena'];
        $moja    = (int)($slika['moja_ocjena'] ?? 0);
    ?>
        <figure class="galerija_slika">
            <a href="#modal<?= $id ?>">
                <img src="https://unsplash.it/300/200?image=<?= $id ?>"
                     alt="<?= htmlspecialchars($slika['opis']) ?>">
            </a>
            <figcaption><?= htmlspecialchars($slika['opis']) ?></figcaption>

            <div class="ocjena-bar">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                    <span style="color:<?= $s <= round($prosjek) ? '#f5a623' : '#ddd' ?>;font-size:1.1rem;">★</span>
                <?php endfor; ?>
                <?php if ($broj > 0): ?>
                    <?= number_format($prosjek,1) ?>/5
                    <small>(<?= $broj ?> <?= $broj===1?'ocjena':($broj<5?'ocjene':'ocjena') ?>)</small>
                <?php else: ?>
                    <small>Nije još ocijenjeno</small>
                <?php endif; ?>
            </div>

            <?php if ($prijavljen): ?>
            <form class="ocjeni-forma" method="POST" action="gallery.php">
                <input type="hidden" name="id_slike" value="<?= $id ?>">
                <div class="zvjezdice-input">
                    <?php for ($s = 5; $s >= 1; $s--): ?>
                        <input type="radio" id="zvj_<?= $id ?>_<?= $s ?>"
                               name="ocjena" value="<?= $s ?>"
                               <?= $moja===$s?'checked':'' ?>>
                        <label for="zvj_<?= $id ?>_<?= $s ?>" title="<?= $s ?> zvjezdica">★</label>
                    <?php endfor; ?>
                </div>
                <button type="submit"><?= $moja > 0 ? 'Promijeni ocjenu' : 'Ocijeni' ?></button>
                <?php if ($moja > 0): ?>
                    <div style="font-size:.78rem;color:#555;margin-top:2px;">Tvoja ocjena: <?= $moja ?>/5</div>
                <?php endif; ?>
            </form>
            <?php else: ?>
                <p class="login-note"><a href="login.php">Prijavi se</a> za ocjenjivanje</p>
            <?php endif; ?>
        </figure>

        <div id="modal<?= $id ?>" class="modal">
            <div class="modal-content">
                <a href="#" class="close">&times;</a>
                <img src="https://unsplash.it/900/600?image=<?= $id ?>"
                     alt="<?= htmlspecialchars($slika['opis']) ?>">
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</section>

<footer><p>© 2026. Web Programiranje.</p></footer>
</body>
</html>
