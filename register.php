<?php
if(!isset($_SESSION)) // if session is not set
{
    session_start(); // start session
}
$view = new stdClass(); // stdClass object instantiated
require_once('models/Control.php'); // access to UsersDataSet.php class once
$call = new Control(); // UsersDataSet object instantiated
//$view->pageTitle = 'TextMe - Registration'; // page title
//$_SESSION['title'] = $view->pageTitle; // use variable 'title' to name the tab
$view->call = $call->checkIfHuman(); // call method from UsersDataSet
require_once('views/register.phtml'); // access to index.php onc