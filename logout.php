<?php
/*
 * Cru Doctrine
 * Logout
 * Campus Crusade for Christ
 */


session_start();

if(isset($_SESSION['email'])){
    session_unset();
    session_destroy(); 
}


header( "Location: /" );

?>