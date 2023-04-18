<?php
if(!isset($_SESSION)) // if session is not set
{
    session_start(); // start session
}
if (isset($_SESSION['role']) && $_SESSION['role'] == 0)
{
    require_once('views/userHome.phtml');
}
else
{
    header('location: index.php');
}
