<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$prijavljen  = jePrijavljen();
$poruka      = '';
$poruka_tip  = '';

if ($prijavljen && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcija      = $_POST['akcija']    ?? '';
    $id_pjesme   = (int)($_POST['id_pjesme'] ?? 0);
    $korisnik_id = $_SESSION['korisnik_id'];

    $stmt = $pdo->prepare("SELECT id FROM playliste WHERE id_korisnika = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$korisnik_id]);
    $pl = $stmt->fetch();

    if (!$pl) {
        $stmt = $pdo->prepare("INSERT INTO playliste (id_korisnika, naziv) VALUES (?, 'Moja playlista')");
        $stmt->execute([$korisnik_id]);
        $id_playliste = $pdo->lastInsertId();
    } else {
        $id_playliste = $pl['id'];
    }

    if ($akcija === 'dodaj' && $id_pjesme > 0) {
        $stmt = $pdo->prepare("SELECT 1 FROM playlista_pjesme WHERE id_playliste = ? AND id_pjesme = ?");
        $stmt->execute([$id_playliste, $id_pjesme]);
        if ($stmt->fetch()) {
            $poruka = "⚠️ Ova pjesma je već u tvojoj playlisti!";
            $poruka_tip = 'upozorenje';
        } else {
            $stmt = $pdo->prepare("INSERT INTO playlista_pjesme (id_playliste, id_pjesme) VALUES (?, ?)");
            $stmt->execute([$id_playliste, $id_pjesme]);
            $poruka = "✅ Pjesma dodana u playlistu!";
            $poruka_tip = 'uspjeh';
        }
    }

    if ($akcija === 'ukloni' && $id_pjesme > 0) {
        $stmt = $pdo->prepare("DELETE FROM playlista_pjesme WHERE id_playliste = ? AND id_pjesme = ?");
        $stmt->execute([$id_playliste, $id_pjesme]);
        $poruka = "🗑️ Pjesma uklonjena iz playliste.";
        $poruka_tip = 'greska';
    }

    if ($akcija === 'nova') {
        $naziv = htmlspecialchars(trim($_POST['naziv_playliste'] ?? 'Moja playlista'), ENT_QUOTES);
        if (empty($naziv)) $naziv = 'Moja playlista';
        $stmt = $pdo->prepare("INSERT INTO playliste (id_korisnika, naziv) VALUES (?, ?)");
        $stmt->execute([$korisnik_id, $naziv]);
        $poruka = "✅ Nova playlista \"$naziv\" kreirana!";
        $poruka_tip = 'uspjeh';
    }
}

$zanr         = trim($_GET['zanr']         ?? '');
$raspolozenje = trim($_GET['raspolozenje'] ?? '');
$bpm_min      = (int)($_GET['bpm_min']     ?? 0);
$bpm_max      = (int)($_GET['bpm_max']     ?? 0);
$god_min      = (int)($_GET['god_min']     ?? 0);
$god_max      = (int)($_GET['god_max']     ?? 0);
$sortiranje   = $_GET['sort']              ?? 'id';

$dozvoljeni_sort = ['id','naslov','izvodac','zanr','bpm','godina','popularnost'];
if (!in_array($sortiranje, $dozvoljeni_sort)) $sortiranje = 'id';

$uvjeti = []; $params = [];
if ($zanr !== '')         { $uvjeti[] = "zanr = ?";         $params[] = $zanr; }
if ($raspolozenje !== '') { $uvjeti[] = "raspolozenje = ?"; $params[] = $raspolozenje; }
if ($bpm_min > 0)         { $uvjeti[] = "bpm >= ?";         $params[] = $bpm_min; }
if ($bpm_max > 0)         { $uvjeti[] = "bpm <= ?";         $params[] = $bpm_max; }
if ($god_min > 0)         { $uvjeti[] = "godina >= ?";      $params[] = $god_min; }
if ($god_max > 0)         { $uvjeti[] = "godina <= ?";      $params[] = $god_max; }

$where  = count($uvjeti) ? "WHERE " . implode(" AND ", $uvjeti) : "";
$stmt   = $pdo->prepare("SELECT * FROM pjesme $where ORDER BY $sortiranje ASC");
$stmt->execute($params);
$pjesme = $stmt->fetchAll();

$zanrovi      = $pdo->query("SELECT DISTINCT zanr         FROM pjesme ORDER BY zanr")->fetchAll(PDO::FETCH_COLUMN);
$raspolozenja = $pdo->query("SELECT DISTINCT raspolozenje FROM pjesme ORDER BY raspolozenje")->fetchAll(PDO::FETCH_COLUMN);

// ---- PLAYLISTA ----
$playlista_pjesme  = [];
$aktivna_playlista = null;

if ($prijavljen) {
    $stmt = $pdo->prepare("SELECT * FROM playliste WHERE id_korisnika = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$_SESSION['korisnik_id']]);
    $aktivna_playlista = $stmt->fetch();

    if ($aktivna_playlista) {
        $stmt = $pdo->prepare("
            SELECT p.* FROM pjesme p
            JOIN playlista_pjesme pp ON pp.id_pjesme = p.id
            WHERE pp.id_playliste = ?
            ORDER BY pp.dodano_at ASC
        ");
        $stmt->execute([$aktivna_playlista['id']]);
        $playlista_pjesme = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Playlist aplikacija</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; background-color: #f5f7fa; color: #333; }
        header { background: linear-gradient(135deg, #4f46e5, #3b82f6); color: white; padding: 20px; text-align: center; }
        .Menu { position: relative; }
        #menu-toggle { display: none; }
        .Menu label { display: block; padding: 15px; background: #1e293b; color: white; cursor: pointer; }
        .Menu label:hover { background: #334155; }
        .SubMenu { display: none; position: absolute; top: 100%; left: 0; width: 200px; background: #1e293b; border: 2px solid white; list-style: none; padding: 0; margin: 0; z-index: 100; }
        #menu-toggle:checked ~ .SubMenu { display: block; }
        .SubMenu li { padding: 10px; }
        .SubMenu li:hover { background: #334155; }
        .SubMenu li a { color: white; text-decoration: none; }
        #div2 { display: flex; justify-content: center; padding: 20px; }
        #div3 { width: 95%; max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        h2 { margin-top: 30px; }
        input, select { margin: 5px; padding: 6px; border-radius: 6px; border: 1px solid #ccc; }
        button { padding: 8px 14px; border: none; border-radius: 8px; background: #4f46e5; color: white; cursor: pointer; transition: 0.3s; }
        button:hover { background: #4338ca; }
        table { width: 100%; margin: 10px 0; border-collapse: collapse; font-size: 14px; border-radius: 10px; overflow: hidden; }
        thead { background: #4f46e5; color: white; }
        th, td { padding: 10px; text-align: center; }
        td { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        tbody tr:nth-child(even) { background: #f1f5f9; }
        tbody tr:hover { background: #e0e7ff; }
        footer { background: #1e293b; color: white; text-align: center; padding: 15px; margin-top: 30px; }

        .poruka { padding: 10px 16px; border-radius: 6px; margin: 12px 0; font-weight: bold; }
        .poruka.uspjeh     { background: #d4edda; border: 1px solid #28a745; color: #155724; }
        .poruka.upozorenje { background: #fff3cd; border: 1px solid #ffc107; color: #856404; }
        .poruka.greska     { background: #f8d7da; border: 1px solid #dc3545; color: #721c24; }
        #filteri { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; margin-bottom: 16px; }
        #filteri label { font-weight: bold; display: block; margin-bottom: 2px; font-size: .85rem; }
        .filter-group { display: flex; flex-direction: column; }
        .btn { padding: 7px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: .9rem; text-decoration: none; display: inline-block; }
        .btn-primary { background: #4f46e5; color: #fff; } .btn-primary:hover { background: #4338ca; }
        .btn-danger  { background: #dc3545; color: #fff; } .btn-danger:hover  { background: #a71d2a; }
        .btn-success { background: #28a745; color: #fff; } .btn-success:hover { background: #1e7e34; }
        .btn-reset   { background: #6c757d; color: #fff; } .btn-reset:hover   { background: #545b62; }
        #playlista-section { margin-top: 32px; }
        .playlist-tablica th { background: #4f46e5; color: #fff; }
        .nova-form { display: flex; gap: 8px; margin-top: 16px; align-items: center; }
        .nova-form input { padding: 7px; border: 1px solid #ccc; border-radius: 4px; flex: 1; margin: 0; }
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

<div id="div2"><div id="div3">

    <?php if ($poruka): ?>
        <div class="poruka <?= $poruka_tip ?>"><?= $poruka ?></div>
    <?php endif; ?>

    <!-- FILTERI -->
    <h2>Filteri</h2>
    <form method="GET" action="index.php" id="filteri">
        <div class="filter-group">
            <label>Žanr</label>
            <select name="zanr">
                <option value="">Svi</option>
                <?php foreach ($zanrovi as $z): ?>
                    <option value="<?= htmlspecialchars($z) ?>" <?= $z===$zanr?'selected':'' ?>><?= htmlspecialchars($z) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Raspoloženje</label>
            <select name="raspolozenje">
                <option value="">Sva</option>
                <?php foreach ($raspolozenja as $r): ?>
                    <option value="<?= htmlspecialchars($r) ?>" <?= $r===$raspolozenje?'selected':'' ?>><?= htmlspecialchars($r) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group"><label>BPM od</label><input type="number" name="bpm_min" min="0" max="300" value="<?= $bpm_min?:'' ?>" placeholder="100"></div>
        <div class="filter-group"><label>BPM do</label><input type="number" name="bpm_max" min="0" max="300" value="<?= $bpm_max?:'' ?>" placeholder="180"></div>
        <div class="filter-group"><label>Godina od</label><input type="number" name="god_min" min="1900" max="2030" value="<?= $god_min?:'' ?>" placeholder="1990"></div>
        <div class="filter-group"><label>Godina do</label><input type="number" name="god_max" min="1900" max="2030" value="<?= $god_max?:'' ?>" placeholder="2023"></div>
        <div class="filter-group">
            <label>Sortiraj po</label>
            <select name="sort">
                <option value="id"          <?= $sortiranje==='id'?'selected':'' ?>>ID</option>
                <option value="naslov"      <?= $sortiranje==='naslov'?'selected':'' ?>>Naslovu</option>
                <option value="izvodac"     <?= $sortiranje==='izvodac'?'selected':'' ?>>Izvođaču</option>
                <option value="bpm"         <?= $sortiranje==='bpm'?'selected':'' ?>>BPM-u</option>
                <option value="godina"      <?= $sortiranje==='godina'?'selected':'' ?>>Godini</option>
                <option value="popularnost" <?= $sortiranje==='popularnost'?'selected':'' ?>>Popularnosti</option>
            </select>
        </div>
        <div class="filter-group"><label>&nbsp;</label><button type="submit" class="btn btn-primary">Filtriraj</button></div>
        <div class="filter-group"><label>&nbsp;</label><a href="index.php" class="btn btn-reset">Reset</a></div>
    </form>

    <!-- TABLICA PJESAMA -->
    <h2>Pjesme (<?= count($pjesme) ?>)</h2>
    <?php if (empty($pjesme)): ?>
        <p>Nema rezultata za odabrane filtere. </p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Naslov</th><th>Izvođač</th><th>Žanr</th>
                <th>BPM</th><th>Godina</th><th>Popularnost</th><th>Raspoloženje</th>
                <?php if ($prijavljen): ?><th>Akcija</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pjesme as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['naslov']) ?></td>
                <td><?= htmlspecialchars($p['izvodac']) ?></td>
                <td><?= htmlspecialchars($p['zanr']) ?></td>
                <td><?= $p['bpm'] ?></td>
                <td><?= $p['godina'] ?></td>
                <td>⭐ <?= number_format($p['popularnost'],1) ?></td>
                <td><?= htmlspecialchars($p['raspolozenje']) ?></td>
                <?php if ($prijavljen): ?>
                <td>
                    <form method="POST" action="index.php" style="display:inline">
                        <input type="hidden" name="akcija"    value="dodaj">
                        <input type="hidden" name="id_pjesme" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-success">+ Dodaj</button>
                    </form>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- PLAYLISTA -->
    <?php if ($prijavljen): ?>
    <div id="playlista-section">
        <h2>Moja playlista
            <?php if ($aktivna_playlista): ?>
                – <em><?= htmlspecialchars($aktivna_playlista['naziv']) ?></em>
                <small style="font-size:.8rem;color:#666;">(kreirana <?= date('d.m.Y.', strtotime($aktivna_playlista['created_at'])) ?>)</small>
            <?php endif; ?>
        </h2>

        <?php if (empty($playlista_pjesme)): ?>
            <p>Playlista je prazna. Dodaj pjesme iz tablice iznad.</p>
        <?php else: ?>
        <table class="playlist-tablica">
            <thead><tr><th>#</th><th>Naslov</th><th>Izvođač</th><th>Žanr</th><th>BPM</th><th>Akcija</th></tr></thead>
            <tbody>
            <?php foreach ($playlista_pjesme as $i => $p): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($p['naslov']) ?></td>
                    <td><?= htmlspecialchars($p['izvodac']) ?></td>
                    <td><?= htmlspecialchars($p['zanr']) ?></td>
                    <td><?= $p['bpm'] ?></td>
                    <td>
                        <form method="POST" action="index.php" style="display:inline">
                            <input type="hidden" name="akcija"    value="ukloni">
                            <input type="hidden" name="id_pjesme" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Ukloni ovu pjesmu?')">Ukloni</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <form method="POST" action="index.php" class="nova-form">
            <input type="hidden" name="akcija" value="nova">
            <input type="text" name="naziv_playliste" placeholder="Naziv nove playliste..." maxlength="200">
            <button type="submit" class="btn btn-primary">Kreiraj novu playlistu</button>
        </form>
        <p style="font-size:.85rem;color:#666;margin-top:6px;">💡 Nova playlista postaje aktivna – stara ostaje sačuvana u bazi.</p>
    </div>
    <?php else: ?>
        <div class="poruka upozorenje" style="margin-top:24px;">
            🔒 <a href="login.php">Prijavi se</a> ili <a href="register.php">registriraj se</a>
            kako bi mogao/la upravljati playstom.
        </div>
    <?php endif; ?>

</div></div>
<footer><p>© 2026. Web Programiranje.</p></footer>
</body>
</html>
