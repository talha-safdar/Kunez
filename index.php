<?php
if(!isset($_SESSION)) // if session is not set
{
    session_start(); // start session
}
unset($_SESSION['removeLoginButton']);
if(isset($_SESSION['role']) && $_SESSION['role'] == 0)
{
    header('location: userHome.php');
}
else
{
    require_once ('views/index.phtml');
}

