<?php
if(!isset($_SESSION)) // if session is not set
{
    session_start(); // start session
}
require_once('Control.php');

class Core extends Control
{
    public function __construct()
    {
        parent::__construct();

        if (isset($_POST['register']))
        {
            parent::register();
        }
        else if (isset($_POST['login']))
        {
            parent::login();
        }
        else if (isset($_GET['exit']))
        {
            parent::logout();
        }
        else if (isset($_POST['first_name']))
        {
            parent::editFirstName();
        }
        else if (isset($_POST['last_name']))
        {
            parent::editLastName();
        }
        else if (isset($_POST['email_address']))
        {
            parent::editEmailAddress();
        }
        else if (isset($_POST['password']))
        {
            parent::editPassword();
        }
        else if (isset($_POST['initialDiscountSubmitted']))
        {
            parent::initialDiscount();
        }
    }
}
$in = new Core;
exit();