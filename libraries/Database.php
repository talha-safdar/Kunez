<?php
/**
 * The class Database creates a connection with the database
 * and manages the queries with the use of PDO.
 */
class Database
{
    /**
     * @var Database
     */
    public static $_dbInstance; // define a static variable of this class
    /**
     * @var PDO
     */
    protected $_dbHandle; // create a protected field variable

    /**
     * @return Database
     */
    public static function getInstance()
    {
        $username = 'safdaast_safdard'; // username
        $password = 'BQlDxbL1yDeW'; // password to access the database
//        $host = 'localhost'; // host name where database is located
        $host = '198.57.243.7:3306'; // host name where database is located
        $dbName = 'safdaast_main'; // the name of the database

        if(self::$_dbInstance === null) // check if static variable is strictly equals to null
        {
            // instantiate a variable $_dbInstance with appropriate variables to establish a connection
            self::$_dbInstance = new self($username, $password, $host, $dbName);
        }
        return self::$_dbInstance; // return the static variable
    }

    /**
     * @param $username
     * @param $password
     * @param $host
     * @param $database
     */
    public function __construct($username, $password, $host, $database)
    {
        // connect PHP with the database
        $options = array(
            PDO::ATTR_PERSISTENT         => true, // make the connection persistent
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exception if an error occurs
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // return each row as an indexed array
            PDO::ATTR_EMULATE_PREPARES   => false, // disable the emulation when statements are prepared
        );
        try
        {
            // creates the database handle with connection info
            $this->_dbHandle = new PDO("mysql:host=$host;dbname=$database", $username, $password, $options);
        }
        catch (PDOException $e) // catch any failure to connect to the database
        {
            echo $e->getMessage(); // display the error message
        }
    }

    /**
     * @return PDO
     */
    public function getdbConnection()
    {
        return $this->_dbHandle; // returns the PDO handle to be used elsewhere
    }

    public function __destruct()
    {
        $this->_dbHandle = null; // destroy the PDO handle when no longer needed
    }
}