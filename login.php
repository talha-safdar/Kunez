<?php
if(!isset($_SESSION)) // if session is not set
{
    session_start(); // start session
}
$_SESSION['removeLoginButton'] = true;
require_once('views/login.phtml'); // access to index.php onc