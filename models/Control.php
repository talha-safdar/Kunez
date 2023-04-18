<?php
if(!isset($_SESSION)) // if session is not started
{
    session_start(); // start session
}
include_once($_SERVER['DOCUMENT_ROOT'] . '/libraries/alert.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/libraries/success.php');
require_once('DataToBase.php');

Class Control extends DataToBase
{
    public function __construct()
    {
        parent::__construct(); // call superclass' constructor
    }

    public function register()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $credentials = [
            'bot' => trim($_POST['bot']),
            'first_name' => $_SESSION['first_name_reg'] = trim($_POST['first_name']),
            'last_name' => $_SESSION['last_name_reg'] = trim($_POST['last_name']),
            'username' => $_SESSION['username_reg'] = trim($_POST['username']),
            'email_address' => $_SESSION['email_address_reg'] = trim($_POST['email_address']),
            'password' => trim($_POST['password']),
            'passwordRepeat' => trim($_POST['passwordRepeat']),
        ];
        if (isset($_POST['bot']) && $_POST['bot'] == '') // check if invisible textbook is empty
        {
            if (empty($credentials['first_name']) || empty($credentials['last_name'])  || empty($credentials['username']) || empty($credentials['email_address']) || empty($credentials['password']))
            {
                alert("register", "Please fill out all the boxes"); // call method alert()
                header("location: ../register.php#register"); // redirect to register page
                exit(); // exit the script
            }
            if (!preg_match("/^[a-zA-Z\s]*$/", $credentials['first_name'])) // if full name is not in correct format
            {
                alert("register", "Invalid first name"); // call method alert()
                header("location: ../register.php#register"); // redirect to register page
                exit(); // exit the script
            }
            if (!preg_match("/^[a-zA-Z\s]*$/", $credentials['last_name'])) // if username contains whitespaces
            {
                alert("register", "Invalid surname"); // call method alert()
                header("location: ../register.php#register"); // redirect to register page
                exit(); // exit the script
            }
            if (!filter_var($credentials['email_address'], FILTER_VALIDATE_EMAIL)) // if email is not valid
            {
                alert("register", "Invalid email"); // call method alert()
                header("location: ../register.php#register"); // redirect to register page
                exit(); // exit the script
            }
            if (parent::checkIfUsernameTaken($credentials['username'])) // if username already exists
            {
                alert("register", "username already taken"); // call method alert()
                header("location: ../register.php#register"); // redirect to register page
                exit(); // exit the script
            }
            if (parent::checkIfEmailTaken($credentials['email_address'])) // if email already exists
            {
                alert("register", "email already taken"); // call method alert()
                header("location: ../register.php#register"); // redirect to register page
                exit(); // exit the script
            }
            if (strlen($credentials['password']) < 6) // if password is less than 6 characters
            {
                alert("register", "Password must be at least 6 characters"); // call method alert()
                header("location: ../register.php#register"); // redirect to register page
                exit(); // exit the script
            }
            if (strcmp($credentials['password'], $credentials['passwordRepeat']) !== 0) // if password is less than 6 characters
            {
                alert("register", "Passwords do not match"); // call method alert()
                header("location: ../register.php#register"); // redirect to register page
                exit(); // exit the script
            }

            $credentials['password'] = password_hash($credentials['password'], PASSWORD_DEFAULT); // encrypt password
            if($_POST['humanCheck'] != $_SESSION['verificationCode']) // check if verification code matches
            {
                alert("checkFailed", "Human verification failed"); // call method alert()
                header("location: ../register.php#register"); // redirect to register page
                exit(); // exit the script
            }
            if (parent::registerUser($credentials)) // check if user has been successfully registered
            {
                $_SESSION['email_address_log'] = $_SESSION['email_address_reg'];
                $this->unsetRegisterSessions();
                success("success", "Success! You can now login"); // call method alert()
                header("location: ../login.php#login"); // redirect to register page
                exit(); // exit the script
            }
            else
            {
                header("location: ../index.php"); // redirect to register page
                die("Something went wrong");  // exit the script with a message
            }

        }
        else
        {
            header("location: ../index.php"); // redirect to register page
            die("Spamming is not allowed");  // exit the script with a message
        }
    }

    private function unsetRegisterSessions()
    {
        unset($_SESSION['first_name_reg']);
        unset($_SESSION['last_name_reg']);
        unset($_SESSION['username_reg']);
        unset($_SESSION['email_address_reg']);
    }

    public function login()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING); // sanitise all $_POST inputs
        $_SESSION['userLoggingIn'] = $_POST['emailAddress']; // assign email
        $credentials = [
            'emailAddress' => trim($_POST['emailAddress']),
            'password' => trim($_POST['password'])
        ]; // create local array of $_POST inputs
        if (empty($credentials['emailAddress']) || empty($credentials['password'])) // if email or password input is empty
        {
            alert("login", "Please fill out all the boxes"); // call method alert()
            header("location: ../login.php"); // redirect to register page
            exit(); // exit the script
        }
        if(filter_var($credentials['emailAddress'], FILTER_VALIDATE_EMAIL)) // if email is valid
        {
            if (parent::checkIfEmailTaken($credentials['emailAddress'])) // if user is registered in the database
            {

                $loggedInUser = $this->checkCredentials($credentials['emailAddress'], $credentials['password']); // assign values
                if ($loggedInUser) // if it contains all the values
                {
                    $this->generateUserSession($loggedInUser); // create session for the user
                    header("location: ../userHome.php"); // redirect to homepage
                }
                else
                {
                    alert("login", "Password Incorrect"); // call method alert()
                    header("location: ../login.php#login"); // redirect to login page
                    exit(); // exit the script
                }
            }
            else
            {
                alert("login", "Email incorrect"); // call method alert()
                header("location: ../login.php#login"); // redirect to login page
                exit(); // exit the script
            }

        }
        else
        {
            alert("login", "Email validation failed"); // call method alert()
            header("location: ../login.php#login"); // redirect to login page
            exit(); // exit the script
        }
    }

    public function checkCredentials($email, $password)
    {
        $row = parent::checkIfEmailTaken($email); // call superclass' method to verify email

        if ($row == false) // if no email found
        {
            return false;
        }
        $hashedPassword = $row->password; // assign password
        if (password_verify($password, $hashedPassword)) // if password matches
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    public function generateUserSession($loggedInUser)
    {
        unset($_SESSION['removeLoginButton']);
        unset($_SESSION['email_address_log']);
        $_SESSION['user_ID'] = $loggedInUser->user_ID; // assign user ID to global variable
        $_SESSION['first_name'] = $loggedInUser->first_name; // assign user ID to global variable
        $_SESSION['last_name'] = $loggedInUser->last_name; // assign user ID to global variable
        $_SESSION['username'] = $loggedInUser->username; // assign username to global variable
        $_SESSION['email_address'] = $loggedInUser->email_address; // assign email to global variable
        $_SESSION['password'] = $loggedInUser->password; // assign email to global variable
        $_SESSION['role'] = $loggedInUser->role; // assign email to global variable
        header("location: ../loggedInPage.php"); // redirect to homepage
    }

    // logout
    // $_SESSION['verificationCode']

    public function logout()
    {
        session_unset();
        session_destroy(); // destroy all the data from the session
        header("location: ../index.php"); // redirect to guest homepage
    }

    public function checkIfHuman()
    {
        $size = 6; // size of the text verification
        $charSet = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; // all possible characters in the text
        $charSize = strlen($charSet); // measure the length of the charSet
        $randText = "";
        for ($i = 0; $i < $size; $i++) // for loop to add a random digit from the charSet
        {
            $randText .= $charSet[rand(0,$charSize - 1)]; // add a random digit within the length of the string
        }
        return $_SESSION['verificationCode'] = $randText; // return and assign the randomly generated text to global variable
    }

    // from below UNIFY them
    public function editFirstName()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($_POST['first_name']))
        {
            if (isset($_POST['first_name_box']) && $_POST['first_name_box'] != '')
            {
                $trimmed = trim($_POST['first_name_box']);
                if (!preg_match("/^[a-zA-Z\s]*$/", $trimmed)) // if full name is not in correct format
                {
                    alert("edit", "Invalid first name"); // call method alert()
                    header("location: ../userHome.php#edit"); // redirect to register page
                    exit(); // exit the script
                }
                if (parent::dataChangeFirstName($trimmed))
                {
                    $_SESSION['first_name'] = $trimmed;
                    // success message?
                    success("edit", "The first name has been changed!"); // call method alert()
                    header("location: ../userHome.php#edit"); // redirect to register page
                    exit(); // exit the script
                }
                else
                {
                    alert("edit", "could not get the data"); // call method alert()
                    header("location: ../userHome.php#edit"); // redirect to register page
                    exit(); // exit the script
                }
            }
            else
            {
                alert("edit", "The box cannot be empty"); // call method alert()
                header("location: ../userHome.php#edit"); // redirect to register page
                exit(); // exit the script
            }
        }
        else
        {
            alert("edit", "Something went wrong"); // call method alert()
            header("location: ../userHome.php#edit"); // redirect to register page
            exit(); // exit the script
        }

    }

    public function editLastName()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($_POST['last_name']))
        {
            if (isset($_POST['last_name_box']) && $_POST['last_name_box'] != '')
            {
                $trimmed = trim($_POST['last_name_box']);
                if (!preg_match("/^[a-zA-Z\s]*$/", $trimmed)) // if full name is not in correct format
                {
                    alert("edit", "Invalid last name"); // call method alert()
                    header("location: ../userHome.php#edit"); // redirect to register page
                    exit(); // exit the script
                }
                if (parent::dataChangeLastName($trimmed))
                {
                    $_SESSION['last_name'] = $trimmed;
                    success("edit", "The last name has been changed!"); // call method alert()
                    header('location: ../userHome.php#edit');
                }
                else
                {
                    alert("edit", "could not get the data"); // call method alert()
                    header("location: ../userHome.php#edit"); // redirect to register page
                    exit(); // exit the script
                }
            }
            else
            {
                alert("edit", "The box cannot be empty"); // call method alert()
                header("location: ../userHome.php#edit"); // redirect to register page
                exit(); // exit the script
            }

        }
        else
        {
            alert("edit", "Something went wrong"); // call method alert()
            header("location: ../userHome.php#edit"); // redirect to register page
            exit(); // exit the script
        }
    }

    public function editEmailAddress()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($_POST['email_address']))
        {
            if (isset($_POST['email_address_box']) && $_POST['email_address_box'] != '')
            {
                $trimmed = trim($_POST['email_address_box']);
                if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) // if email is not valid
                {
                    alert("edit", "The email is in incorrect format"); // call method alert()
                    header("location: ../userHome.php#edit"); // redirect to register page
                    exit(); // exit the script
                }
                if (parent::checkIfEmailTaken($trimmed)) // if email already exists
                {
                    alert("edit", "email already taken"); // call method alert()
                    header("location: ../userHome.php#edit"); // redirect to register page
                    exit(); // exit the script
                }
                if (parent::dataChangeEmailAddress($trimmed))
                {
                    $_SESSION['email_address'] = $trimmed;
                    success("edit", "The email address has been changed!"); // call method alert()
                    header('location: ../userHome.php#edit');
                }
                else
                {
                    alert("edit", "could not get the data"); // call method alert()
                    header("location: ../userHome.php#edit"); // redirect to register page
                    exit(); // exit the script
                }
            }
            else
            {
                alert("edit", "The box cannot be empty"); // call method alert()
                header("location: ../userHome.php#edit"); // redirect to register page
                exit(); // exit the script
            }
        }
        else
        {
            alert("edit", "Something went wrong"); // call method alert()
            header("location: ../userHome.php#edit"); // redirect to register page
            exit(); // exit the script
        }
    }

    public function editPassword()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($_POST['password']))
        {
            if (isset($_POST['password_box']) && $_POST['password_box'] != '')
            {
                $trimmed = trim($_POST['password_box']);
                if (strlen($trimmed) < 6) // if password is less than 6 characters
                {
                    alert("edit", "Password must be at least 6 characters"); // call method alert()
                    header("location: ../userHome.php#edit"); // redirect to register page
                    exit(); // exit the script
                }
                $password_box = password_hash($trimmed, PASSWORD_DEFAULT);
                if (parent::dataChangePassword($password_box))
                {
                    success("edit", "The password has been changed!"); // call method alert()
                    header('location: ../userHome.php#edit');
                }
                else
                {
                    alert("edit", "could not get the data"); // call method alert()
                    header("location: ../userHome.php#edit"); // redirect to register page
                    exit(); // exit the script
                }
            }
            else
            {
                alert("edit", "The box cannot be empty"); // call method alert()
                header("location: ../userHome.php#edit"); // redirect to register page
                exit(); // exit the script
            }
        }
        else
        {
            alert("edit", "Something went wrong"); // call method alert()
            header("location: ../userHome.php#edit"); // redirect to register page
            exit(); // exit the script
        }
    }

    public function initialDiscount()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if(isset($_POST['initialDiscountSubmitted']))
        {
            if(isset($_POST['initialDiscount']))
            {
                if(parent::dataApplyDiscount())
                {
                    $_SESSION['ignorefirstCheck'] = true;
                    header("location: ../products.php#discount");
                }
                else
                {
                    alert("discount", "Server error: code-x_001"); // call method alert()
                }
            }
            else
            {
                alert("discount", "box not checked"); // call method alert()
            }
            exit();
        }
        else
        {
            alert("discount", "Something went wrong"); // call method alert()
            header("location: ../products.php#discount"); // redirect to register page
            exit(); // exit the script
        }
    }

    public function checkIfRedeemed()
    {
        if(parent::dataCheckIfRedeemed())
        {
            $_SESSION['hideDiscountForm'] = true;
        }
        else
        {
            $_SESSION['hideDiscountForm'] = false;
        }
    }
}