<?php
// public/logout.php
// -------------------------------------------------------
//  Odjava – uništi sesiju i preusmjeri na login
// -------------------------------------------------------

require_once '../includes/auth.php';

odjava(); // definirana u auth.php – briše sesiju i preusmjerava
