<?php
// includes/db.php
// -------------------------------------------------------
//  Konekcija na MySQL bazu podataka (PDO + prepared statements)
// -------------------------------------------------------

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Promijeni ako koristiš drugog usera
define('DB_PASS', '');           // Promijeni na svoju lozinku
define('DB_NAME', 'lv4_baza');
define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST
     . ";dbname=" . DB_NAME
     . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // baci iznimku na grešku
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // vrati asocijativni niz
    PDO::ATTR_EMULATE_PREPARES   => false,                   // prave prepared statements
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // U produkciji NE prikazuj detalje greške korisniku!
    error_log("DB greška: " . $e->getMessage());
    die("Nije moguće spojiti se na bazu. Pokušajte ponovno.");
}
