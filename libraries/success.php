<?php
if(!isset($_SESSION)) // if session is not set
{
    session_start(); // start session
}

/**
 * function alert to generate an alert message.
 * it takes three parameters.
 * $function: name of variable from where is called and to where is destined.
 * $information: alert message
 * $classValue: the class value has a bootstrap command
 * idea copied from online resource
 */
function success($function ='', $information ='', $classValue = 'success')
{
    if(!empty($function)) // if function is not empty
    {
        if(empty($_SESSION[$function]) && !empty($information)) // if global variable is empty and $information is not empty
        {
            $_SESSION[$function.'_class'] = $classValue; // assign classValue to global variable
            $_SESSION[$function] = $information; // assign $information to global variable
        }
        else if(empty($information) && !empty($_SESSION[$function])) // if $information is empty and $function is not empty
        {
            // if $_SESSION[$function.'_class'] is not empty then $_SESSION[$function.'_class'] else $classValue
            $classValue = !empty($_SESSION[$function.'_class']) ? $_SESSION[$function.'_class'] : $classValue;
            echo '<div class="'.$classValue.'" >'.$_SESSION[$function].'</div>'; // display the message in an HTML format
            unset($_SESSION[$function]); // unset session
            unset($_SESSION[$function.'_class']); // unset session
        }
    }
}