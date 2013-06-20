<?php
/*
 * Cru Doctrine
 * Logout
 * Campus Crusade for Christ
 */

require('CAS.php');
require('config.inc.php');
phpCAS::client(CAS_VERSION_2_0, 'signin.dodomail.net', 443, '/cas', false /* set to TRUE if the app does not handle its own session */);
session_start();

if(isset($_SESSION['email'])){
    session_unset();
    session_destroy();  
}

phpCAS::logoutWithRedirectService(REDIRECT_URL);
?>
