<?php
// includes/auth.php
// -------------------------------------------------------
//  Pomoćne funkcije za autentifikaciju i sesije
// -------------------------------------------------------

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------
//  Provjeri je li korisnik prijavljen
//  Ako nije – preusmjeri na login.php
// -------------------------------------------------------
function zahtjevajPrijavu() {
    if (!isset($_SESSION['korisnik_id'])) {
        header("Location: /public/login.php");
        exit;
    }
}

// -------------------------------------------------------
//  Provjeri je li korisnik admin
//  Ako nije – preusmjeri na index ili prikaži grešku
// -------------------------------------------------------
function zahtjevajAdmina() {
    zahtjevajPrijavu();
    if ($_SESSION['uloga'] !== 'admin') {
        header("Location: /public/index.php");
        exit;
    }
}

// -------------------------------------------------------
//  Je li korisnik trenutno prijavljen? (bool)
// -------------------------------------------------------
function jePrijavljen() {
    return isset($_SESSION['korisnik_id']);
}

// -------------------------------------------------------
//  Je li prijavljen korisnik admin? (bool)
// -------------------------------------------------------
function jeAdmin() {
    return jePrijavljen() && $_SESSION['uloga'] === 'admin';
}

// -------------------------------------------------------
//  Postavi sesiju nakon uspješne prijave
// -------------------------------------------------------
function postaviSesiju($korisnik) {
    $_SESSION['korisnik_id']       = $korisnik['id'];
    $_SESSION['korisnicko_ime']    = $korisnik['korisnicko_ime'];
    $_SESSION['uloga']             = $korisnik['uloga'];
}

// -------------------------------------------------------
//  Odjava – uništi sesiju
// -------------------------------------------------------
function odjava() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }
    session_destroy();
    header("Location: /public/login.php");
    exit;
}
