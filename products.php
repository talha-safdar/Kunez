<?php
if(!isset($_SESSION)) // if session is not set
{
    session_start(); // start session
}
if (isset($_SESSION['role']) && $_SESSION['role'] == 0)
{
    require_once ('models/Control.php');
    $control = new Control();
    $control->checkIfRedeemed();
    require_once('views/products.phtml');
}
else if (isset($_SESSION['role']) && $_SESSION['role'] == 1)
{
    require_once('views/products.phtml');
}
else
{
    header('location: index.php');
}
