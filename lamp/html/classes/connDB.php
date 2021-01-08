<?php

/* Class: connDB
*
*  A class that connects to server and loads data base
*/
class connDB {

    public function __construct() {
        $this->pdo($servername,$username,$password);

    }

    public function __destruct() {
        //close connection
        $this->conn = null; 
    }


    protected function pdo($servername,$username,$password) {
        /* -------------------------
        Server Connection
        ------------------------- */ 
        $this->conn = new PDO("mysql:host=$servername; port=3306; ", $username, $password);
        // set the PDO error mode to exception
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->verboseOutput .= "Connection to " . $servername . " successfull<br>";
    }

    
    public function setVerbose($verbose) {
        $this->verbose = $verbose? true : false;
    }

    
    function queryDB($nameDB) {   
        if ($this->nameCheck($nameDB)) {    
            try {      
                $sqlQuery = $this->conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA 
                                            WHERE SCHEMA_NAME = '$nameDB';");
                $existsDatabase = (bool) $sqlQuery->fetchColumn();
            
                if ($existsDatabase) {
                    $this->verboseOutput .= "Database " . htmlentities($nameDB) . " exists<br>";
                    return true;
                } else {
                    $this->verboseOutput .= "Database " . htmlentities($nameDB) . " does not exists<br>";
                    return false;
                }
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            } 
        }    
    }

    function createDB($nameDB) {
        if ($this->nameCheck($nameDB)) {
            try {
                if (!$this->queryDB($nameDB)) {
                    $this->conn->query("CREATE DATABASE $nameDB DEFAULT CHARACTER SET utf8;");
                    $this->verboseOutput .= "Created " . htmlentities($nameDB) . " database<br>";
                }
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }

     /*
    * Function: deleteDB
    *
    * Parameters: 
    *     nameDB - str  
    *
    * Description:
    *     Check is the table has dependencies. If not the table is droped.
    */
    function deleteDB($nameDB) {
        if ($this->nameCheck($nameDB)) {
            try {
                $this->conn->query("DROP DATABASE $nameDB;");
                $this->verboseOutput .= "Database " . htmlentities($nameDB) . " was deleted<br>";
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            } 
        }  
    }

    public function useDB($nameDB) {
        if ($this->nameCheck($nameDB)) {
            try {
                $this->conn->query("USE $nameDB;");
                $this->verboseOutput .= "Database " . htmlentities($nameDB) . " in use<br>";
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            } 
        }
    }

     /*
    * Function: getDBs
    *
    * Parameters: 
    *
    * Description:
    *     Shows all databases present on the server
    */
    public function getDBs() {
        try {      
            $sqlQuery = $this->conn->query("SHOW DATABASES");
            $DBs = array_merge(...$sqlQuery->fetchAll(PDO::FETCH_NUM));
        
            if (!empty($DBs)) {
                $this->verboseOutput .= "There are databases<br>";
            } else {
                $this->verboseOutput .= "There are no databases<br>";
            }
            return $DBs;
        } catch (PDOException $e) {
            die("DB ERROR: ". $e->getMessage());
        } 
    }


   
    

    public function printVerbose() {
        echo $this->verboseOutput;
        $this->verboseOutput = '';
    }

    protected function nameCheck($name) {
        $nameValidity = preg_match("/^[a-zA-Z0-9-_]+$/", $name);
        if (!$nameValidity) {
            $this->verboseOutput .= "The name " . htmlentities($name) . " in not valid<br>";
        }
        return $nameValidity;
    }
    protected $servername = "mysql-server";
    protected $username = "root";
    protected $password = "secret";
    protected $verbose = false;
    protected $verboseOutput = '';

}
