<?php
if(!isset($_SESSION)) // if session is not set
{
    session_start(); // start session
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/libraries/Database.php');

class DataToBase
{
    /**
     * @var Database|null
     */
    private $_dbInstance;
    /**
     * @var PDO
     */
    private $_dbHandle;

    public function __construct()
    {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function checkIfEmailTaken($email)
    {
        // prepare query to select row matching the parameter from users table
        $statement = $this->_dbHandle->prepare("SELECT * FROM users WHERE email_address = :email_address");
        $statement->bindParam(':email_address', $email); // bind email
        $statement->execute(); // execute query
        $row = $statement->fetch(PDO::FETCH_OBJ); // fetch row as object
        if ($statement->rowCount() > 0) // if row number is greater than 0
        {
            return $row; // return $row
        }
        else
        {
            return false;
        }
    }

    public function checkIfUsernameTaken($username)
    {
        $statement = $this->_dbHandle->prepare("SELECT * FROM users WHERE username = :username");
        $statement->bindParam(':username', $username); // bind username
        $statement->execute(); // execute query
        $row = $statement->fetch(PDO::FETCH_OBJ); // fetch row as object
        if ($statement->rowCount() > 0) // if row number is greater than 0
        {
            return $row; // return $row
        }
        else
        {
            return false;
        }
    }

    public function registerUser($credentials)
    {
        $statement = $this->_dbHandle->prepare("INSERT INTO users (first_name, last_name, username, email_address, password) VALUES (:first_name, :last_name, :username, :email_address, :password)");
        $statement->bindParam(':first_name', $credentials['first_name']); // bind email address
        $statement->bindParam(':last_name', $credentials['last_name']); // bind full name
        $statement->bindParam(':username', $credentials['username']); // bind username
        $statement->bindParam(':email_address', $credentials['email_address']); // bind username
        $statement->bindParam(':password', $credentials['password']); // bind password
        if($statement->execute()) // if execution is successful
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function dataChangeFirstName($first_name_box)
    {
        $statement = $this->_dbHandle->prepare("UPDATE users SET first_name = :nt3jnge WHERE user_ID = :lefgn8");
        $statement->bindParam(':nt3jnge', $first_name_box); // bind email address
        $statement->bindParam(':lefgn8', $_SESSION['user_ID']); // bind email address
        if($statement->execute()) // if execution is successful
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function dataChangeLastName($last_name_box)
    {
        $statement = $this->_dbHandle->prepare("UPDATE users SET last_name = :nt3jnge WHERE user_ID = :lefgn8");
        $statement->bindParam(':nt3jnge', $last_name_box); // bind email address
        $statement->bindParam(':lefgn8', $_SESSION['user_ID']); // bind email address
        if($statement->execute()) // if execution is successful
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function dataChangeEmailAddress($email_address_box)
    {
        $statement = $this->_dbHandle->prepare("UPDATE users SET email_address = :nt3jnge WHERE user_ID = :lefgn8");
        $statement->bindParam(':nt3jnge', $email_address_box); // bind email address
        $statement->bindParam(':lefgn8', $_SESSION['user_ID']); // bind email address
        if($statement->execute()) // if execution is successful
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function dataChangePassword($password_box)
    {
        $statement = $this->_dbHandle->prepare("UPDATE users SET password = :nt3jnge WHERE user_ID = :lefgn8");
        $statement->bindParam(':nt3jnge', $password_box); // bind email address
        $statement->bindParam(':lefgn8', $_SESSION['user_ID']); // bind email address
        if($statement->execute()) // if execution is successful
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function dataApplyDiscount()
    {
        $statement = $this->_dbHandle->prepare("UPDATE users SET discount = :nt3jnge WHERE user_ID = :lefgn8");
        $num = 1;
        $statement->bindParam(':nt3jnge', $num); // bind email address
        $statement->bindParam(':lefgn8', $_SESSION['user_ID']); // bind email address
        if($statement->execute()) // if execution is successful
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function dataCheckIfRedeemed()
    {
        $statement = $this->_dbHandle->prepare("SELECT discount FROM users WHERE user_ID = :lefgn8 AND discount = :mf24mn");
        $value = 1;
        $statement->bindParam(':lefgn8', $_SESSION['user_ID']); // bind email address
        $statement->bindParam(':mf24mn', $value); // bind email address
        $statement->execute();
        if($statement->rowCount() == 1) // if execution is successful
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}