<?php

class mySQL extends connDB {

    function __construct() {
        $this->pdo($this->servername,$this->username,$this->password);
    }
    
    function __destruct() {
        //close connection
        $this->conn = null; 
    }

    private $nameDB = 'ValidationDB';

    

    public function runQuery($query) {
        $res = array();
        try {       
            $sqlQuery = $this->conn->query($query);
            $res = array_merge(...$sqlQuery->fetchAll(PDO::FETCH_NUM));
        } catch (PDOException $e) {
            die("DB ERROR: ". $e->getMessage());
        } catch (Exception $e) {
            die("ERROR: ". $e->getMessage());
        } finally {
            $this->verboseOutput .= "Tables with '$pattern' in the name: ". sizeof($res) ." <br>";
            return $res;
        }  
    }

    /* ----------------------------
        Table manipulations
    ---------------------------- */ 

    /*
    * Function: getDBTables
    * Parameters: 
    *       $pattern  --  str-type. Empty by default.
    * Returns: 
    *       array(str)
    * Description:
    *     Shows all databases present on the server that are 
    *     labeled accordingly with the input pattern
    */
    public function getDBTables($pattern = '') {
        $res = array();
        try {       
            $sqlQuery = $this->conn->query("SHOW TABLES LIKE '$pattern' ");
            $res = array_merge(...$sqlQuery->fetchAll(PDO::FETCH_NUM));
        } catch (PDOException $e) {
            die("DB ERROR: ". $e->getMessage());
        } catch (Exception $e) {
            die("ERROR: ". $e->getMessage());
        } finally {
            $this->verboseOutput .= "Tables with '$pattern' in the name: ". sizeof($res) ." <br>";
            return $res;
        }  
    }

    /*
    * Function: getTabLabels
    * Parameters: 
    *       $tabName -- table name
    * Returns: 
    *       array(str)
    * Description:
    *     Gets all columns labels for a given table 
    *     in a given database.
    */
    public function getTabLabels($tabName,$nameDB) {
        $res = array();
        try { 
            if ($this->nameCheck($tabName) && $this->nameCheck($nameDB)) {      
                $sqlQuery = $this->conn->query("SELECT COLUMN_NAME
                                            FROM INFORMATION_SCHEMA.COLUMNS
                                            WHERE table_name = '$tabName'
                                            AND `table_schema` = '$nameDB'");
                $res = array_merge(...$sqlQuery->fetchAll(PDO::FETCH_NUM));
            } 
        } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
        } catch (Exception $e) {
            die("ERROR: ". $e->getMessage());
        } finally {
            $this->verboseOutput .= "Table columns labels<br>";
            return $res;
        }
    
    }

    /*
    * Function: getTabRows
    *
    * Parameters: 
    *       $tabName -- table name
    *
    * Description:
    *     Gets all rows from table
    */
    public function getTabRows($tabName, $cols = array()) {
        if ($this->nameCheck($tabName)) {
            try {    
                if ( empty($cols) ) {
                    $query = "SELECT * FROM $tabName"; 
                }  
                else {
                    $query = 'SELECT ' . implode(", ",$cols) . " FROM $tabName";
                }
                $query .= " ORDER BY " . $tabName . "_ID";
                
                $sqlQuery = $this->conn->query($query);
                $rows = $sqlQuery->fetchAll(PDO::FETCH_NUM);
                $this->verboseOutput .= "Table columns labels<br>";
            
                return $rows;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }

    

    /*
    * Function: getClassLinks
    *
    * Parameters: 
    *       $tabName -- table name
    *
    * Description:
    *     Gets all columns labels for the table
    */
    public function getClassLinks($className,$settings) {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = $this->conn->query(
                    "SELECT DISTINCT property$className.property, device$className.device,  
                    acc$settings.acc, stage$settings.stage 
                    FROM $className"."_VALIDATION 
                    JOIN property$className 
                    JOIN device$className 
                    JOIN $className
                    JOIN acc$settings
                    JOIN stage$settings
                    JOIN $settings
                ON acc$settings.acc$settings"."_ID = $settings.acc$settings"."_ID
                AND stage$settings.stage$settings"."_ID = $settings.stage$settings"."_ID 
                AND property$className.property$className"."_ID = $className.property$className"."_ID 
                AND device$className.device$className"."_ID = $className.device$className"."_ID 
                AND $className.$className"."_ID = $className"."_"."$settings.$className"."_ID 
                AND $settings.$settings"."_ID = $className"."_$settings.$settings"."_ID");
                $colLabels = $sqlQuery->fetchAll(PDO::FETCH_NUM);
                $this->verboseOutput .= "Table columns labels<br>";
            
                return $colLabels;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }
     /*
    * Function: getClassMachineLinks
    *
    * Parameters: 
    *       $tabName -- table name
    *
    * Description:
    *     Gets all columns labels for the table
    */
    public function getClassMachineLinks($className, $acc='', $stage='') {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = 
                    "SELECT DISTINCT accMACHINE.acc, stageMACHINE.stage,
                    device$className.device, property$className.property
                    FROM $className"."_MACHINE 
                    JOIN property$className 
                    JOIN device$className 
                    JOIN $className
                    JOIN accMACHINE
                    JOIN stageMACHINE
                    JOIN MACHINE
                ON accMACHINE.accMACHINE_ID = MACHINE.accMACHINE_ID
                AND stageMACHINE.stageMACHINE_ID = MACHINE.stageMACHINE_ID 
                AND property$className.property$className"."_ID = $className.property$className"."_ID 
                AND device$className.device$className"."_ID = $className.device$className"."_ID 
                AND $className.$className"."_ID = $className"."_"."MACHINE.$className"."_ID 
                AND MACHINE.MACHINE_ID = $className"."_MACHINE.MACHINE_ID";

                $input = array();

                if ($acc !== '') {
                    $sqlQuery .= " AND accMACHINE.acc = :acc";
                    $input[':acc'] = $acc;
                }
                if ($stage !== '') {
                    $sqlQuery .= " AND stageMACHINE.stage = :stage";
                    $input[':stage'] = $stage;
                }
        

                $stm = $this->conn->prepare($sqlQuery);
               
                $stm->execute($input);
                
                $colLabels = $stm->fetchAll(PDO::FETCH_NUM);
                $this->verboseOutput .= "Table columns labels<br>";
            
                return $colLabels;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }

    public function getClassDev($className, $acc, $stage) {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = 
                    "SELECT DISTINCT device$className.device
                    FROM $className"."_MACHINE  
                    JOIN device$className 
                    JOIN $className
                    JOIN accMACHINE
                    JOIN stageMACHINE
                    JOIN MACHINE
                    ON accMACHINE.accMACHINE_ID = MACHINE.accMACHINE_ID
                    AND stageMACHINE.stageMACHINE_ID = MACHINE.stageMACHINE_ID 
                    AND device$className.device$className"."_ID = $className.device$className"."_ID 
                    AND $className.$className"."_ID = $className"."_"."MACHINE.$className"."_ID 
                    AND MACHINE.MACHINE_ID = $className"."_MACHINE.MACHINE_ID
                    AND accMACHINE.acc = :acc
                    AND stageMACHINE.stage = :stage";

                $stm = $this->conn->prepare($sqlQuery);               
                $stm->execute(array(':acc'=>$acc,':stage'=>$stage));
                
                $colLabels = $stm->fetchAll(PDO::FETCH_NUM);
                $this->verboseOutput .= "Table columns labels<br>";
            
                return array_merge(...$colLabels);
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }
    
     /*
    * Function: getClassMachineLinks
    *
    * Parameters: 
    *       $tabName -- table name
    *
    * Description:
    *     Gets all columns labels for the table
    */
    public function getClassPropPerDev($className,$dev,$acc,$stage) {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = 
                    "SELECT DISTINCT property$className.property
                    FROM $className"."_MACHINE 
                    JOIN property$className 
                    JOIN device$className 
                    JOIN $className
                    JOIN accMACHINE
                    JOIN stageMACHINE
                    JOIN MACHINE
                ON accMACHINE.accMACHINE_ID = MACHINE.accMACHINE_ID
                AND stageMACHINE.stageMACHINE_ID = MACHINE.stageMACHINE_ID 
                AND property$className.property$className"."_ID = $className.property$className"."_ID 
                AND device$className.device$className"."_ID = $className.device$className"."_ID 
                AND $className.$className"."_ID = $className"."_"."MACHINE.$className"."_ID 
                AND MACHINE.MACHINE_ID = $className"."_MACHINE.MACHINE_ID
                AND accMACHINE.acc = :acc
                AND stageMACHINE.stage = :stage
                AND device$className.device = :dev";

                $stm = $this->conn->prepare($sqlQuery);
                $stm->execute(array(':acc' => $acc,':stage' => $stage, ':dev' => $dev));
                
                $colLabels = $stm->fetchAll(PDO::FETCH_NUM);
                $this->verboseOutput .= "Table columns labels<br>";
            
                return $colLabels;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }

     /*
    * Function: getClassSetup
    *
    * Parameters: 
    *       $tabName -- table name
    *
    * Description:
    *     Gets all columns labels for the table
    */
    public function getClassSetup($className,$verID) {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = $this->conn->query(
                    "SELECT DISTINCT accMACHINE.acc, stageMACHINE.stage,
                    device$className.device, property$className.property,  
                    $className"."_MACHINE_SETUP.value, 
                    $className"."_MACHINE_SETUP.error
                    FROM $className"."_MACHINE_SETUP 
                    JOIN $className"."_MACHINE
                    JOIN MACHINE
                    JOIN accMACHINE
                    JOIN stageMACHINE
                    JOIN $className
                    JOIN property$className 
                    JOIN device$className 
                    JOIN VERSION
                ON $className"."_MACHINE_SETUP.$className"."_MACHINE_ID = $className"."_MACHINE.$className"."_MACHINE_ID
                AND $className"."_MACHINE_SETUP.VERSION_ID = VERSION.VERSION_ID
                AND $className"."_MACHINE.MACHINE_ID  = MACHINE.MACHINE_ID 
                AND $className"."_"."MACHINE.$className"."_ID = $className.$className"."_ID 
                AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID 
                AND MACHINE.stageMACHINE_ID = stageMACHINE.stageMACHINE_ID 
                AND $className.property$className"."_ID = property$className.property$className"."_ID
                AND $className.device$className"."_ID = device$className.device$className"."_ID 
                AND VERSION.VERSION_ID = $verID 
                ");
                $colLabels = $sqlQuery->fetchAll(PDO::FETCH_NUM);
                //$this->verboseOutput .= "Table columns labels<br>";
            
                return $colLabels;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }

      /*
    * Function: getClassSetup
    *
    * Parameters: 
    *       $tabName -- table name
    *
    * Description:
    *     Gets all columns labels for the table
    */
    public function getClassSetupPerDev($className,$dev,$acc,$stage,$verID) {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = 
                    "SELECT DISTINCT device$className.device, property$className.property,  
                    $className"."_MACHINE_SETUP.value, 
                    $className"."_MACHINE_SETUP.error
                    FROM $className"."_MACHINE_SETUP 
                    JOIN $className"."_MACHINE
                    JOIN MACHINE
                    JOIN accMACHINE
                    JOIN stageMACHINE
                    JOIN $className
                    JOIN property$className 
                    JOIN device$className 
                    JOIN VERSION
                ON $className"."_MACHINE_SETUP.$className"."_MACHINE_ID = $className"."_MACHINE.$className"."_MACHINE_ID
                AND $className"."_MACHINE_SETUP.VERSION_ID = VERSION.VERSION_ID
                AND $className"."_MACHINE.MACHINE_ID  = MACHINE.MACHINE_ID 
                AND $className"."_"."MACHINE.$className"."_ID = $className.$className"."_ID 
                AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID 
                AND MACHINE.stageMACHINE_ID = stageMACHINE.stageMACHINE_ID 
                AND $className.property$className"."_ID = property$className.property$className"."_ID
                AND $className.device$className"."_ID = device$className.device$className"."_ID 
                AND VERSION.VERSION_ID = :verID 
                AND accMACHINE.acc = :acc
                AND stageMACHINE.stage = :stage
                AND device$className.device = :dev
                ";
                $stm = $this->conn->prepare($sqlQuery);
                $stm->execute(array(':verID' => $verID,':acc' => $acc,':stage' => $stage, ':dev' => $dev));
                $colLabels = $stm->fetchAll(PDO::FETCH_NUM);
                //$this->verboseOutput .= "Table columns labels<br>";
            
                return $colLabels;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }


    public function getValidationSetupPerDev($className,$dev,$acc,$stage,$verSetupID,$verValidationID) {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = 
                    "SELECT DISTINCT device$className.device, property$className.property,
                    $className"."_MACHINE_VALIDATION.value,  
                    $className"."_MACHINE_SETUP.value, 
                    $className"."_MACHINE_SETUP.error
                    FROM $className"."_MACHINE_SETUP 
                    JOIN $className"."_MACHINE_VALIDATION 
                    JOIN $className"."_MACHINE
                    JOIN MACHINE
                    JOIN accMACHINE
                    JOIN stageMACHINE
                    JOIN $className
                    JOIN property$className 
                    JOIN device$className 
                    JOIN VERSION
                ON $className"."_MACHINE_SETUP.$className"."_MACHINE_ID = $className"."_MACHINE.$className"."_MACHINE_ID
                AND $className"."_MACHINE_VALIDATION.$className"."_MACHINE_ID = $className"."_MACHINE.$className"."_MACHINE_ID
                AND $className"."_MACHINE_SETUP.VERSION_ID = :verSetupID 
                AND $className"."_MACHINE_VALIDATION.VERSION_ID = :verValidationID 
                AND $className"."_MACHINE.MACHINE_ID  = MACHINE.MACHINE_ID 
                AND $className"."_"."MACHINE.$className"."_ID = $className.$className"."_ID 
                AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID 
                AND MACHINE.stageMACHINE_ID = stageMACHINE.stageMACHINE_ID 
                AND $className.property$className"."_ID = property$className.property$className"."_ID
                AND $className.device$className"."_ID = device$className.device$className"."_ID 
                AND accMACHINE.acc = :acc
                AND stageMACHINE.stage = :stage
                AND device$className.device = :dev
                ";
                $stm = $this->conn->prepare($sqlQuery);
                $stm->execute(array(':verSetupID' => $verSetupID,
                                    ':verValidationID' => $verValidationID,
                                    ':acc' => $acc,
                                    ':stage' => $stage, 
                                    ':dev' => $dev));
                $colLabels = $stm->fetchAll(PDO::FETCH_NUM);
                //$this->verboseOutput .= "Table columns labels<br>";
            
                return $colLabels;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }


    public function getValidationSetupPerProp($className,$prop,$dev,$acc,$stage,$verSetupID,$verValidationID) {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = 
                    "SELECT DISTINCT $className"."_MACHINE_VALIDATION.value
                    FROM $className"."_MACHINE_SETUP 
                    JOIN $className"."_MACHINE_VALIDATION 
                    JOIN $className"."_MACHINE
                    JOIN MACHINE
                    JOIN accMACHINE
                    JOIN stageMACHINE
                    JOIN $className
                    JOIN property$className 
                    JOIN device$className 
                    JOIN VERSION
                ON $className"."_MACHINE_SETUP.$className"."_MACHINE_ID = $className"."_MACHINE.$className"."_MACHINE_ID
                AND $className"."_MACHINE_VALIDATION.$className"."_MACHINE_ID = $className"."_MACHINE.$className"."_MACHINE_ID
                AND $className"."_MACHINE_SETUP.VERSION_ID = :verSetupID 
                AND $className"."_MACHINE_VALIDATION.VERSION_ID = :verValidationID 
                AND $className"."_MACHINE.MACHINE_ID  = MACHINE.MACHINE_ID 
                AND $className"."_"."MACHINE.$className"."_ID = $className.$className"."_ID 
                AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID 
                AND MACHINE.stageMACHINE_ID = stageMACHINE.stageMACHINE_ID 
                AND $className.property$className"."_ID = property$className.property$className"."_ID
                AND $className.device$className"."_ID = device$className.device$className"."_ID 
                AND accMACHINE.acc = :acc
                AND stageMACHINE.stage = :stage
                AND device$className.device = :dev
                AND property$className.property = :prop
                ";
                $stm = $this->conn->prepare($sqlQuery);
                $stm->execute(array(':verSetupID' => $verSetupID,
                                    ':verValidationID' => $verValidationID,
                                    ':acc' => $acc,
                                    ':stage' => $stage, 
                                    ':dev' => $dev,
                                    ':prop' => $prop));
                $colLabels = array_merge(...$stm->fetchAll(PDO::FETCH_NUM));
                //$this->verboseOutput .= "Table columns labels<br>";
            
                return $colLabels;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }


    public function getClassValidationPerDev($className,$dev,$acc,$stage,$verID) {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = 
                    "SELECT DISTINCT device$className.device, property$className.property,  
                    $className"."_MACHINE_VALIDATION.value
                    FROM $className"."_MACHINE_VALIDATION 
                    JOIN $className"."_MACHINE
                    JOIN MACHINE
                    JOIN accMACHINE
                    JOIN stageMACHINE
                    JOIN $className
                    JOIN property$className 
                    JOIN device$className 
                    JOIN VERSION
                ON $className"."_MACHINE_VALIDATION.$className"."_MACHINE_ID = $className"."_MACHINE.$className"."_MACHINE_ID
                AND $className"."_MACHINE_VALIDATION.VERSION_ID = VERSION.VERSION_ID
                AND $className"."_MACHINE.MACHINE_ID  = MACHINE.MACHINE_ID 
                AND $className"."_"."MACHINE.$className"."_ID = $className.$className"."_ID 
                AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID 
                AND MACHINE.stageMACHINE_ID = stageMACHINE.stageMACHINE_ID 
                AND $className.property$className"."_ID = property$className.property$className"."_ID
                AND $className.device$className"."_ID = device$className.device$className"."_ID 
                AND VERSION.VERSION_ID = :verID 
                AND accMACHINE.acc = :acc
                AND stageMACHINE.stage = :stage
                AND device$className.device = :dev
                ";
                $stm = $this->conn->prepare($sqlQuery);
                $stm->execute(array(':verID' => $verID,':acc' => $acc,':stage' => $stage, ':dev' => $dev));
                $colLabels = $stm->fetchAll(PDO::FETCH_NUM);
                //$this->verboseOutput .= "Table columns labels<br>";
            
                return $colLabels;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }

          /*
    * Function: getClassSetup
    *
    * Parameters: 
    *       $tabName -- table name
    *
    * Description:
    *     Gets all columns labels for the table
    */
    public function getClassSetupAttr($className,$attr,$acc,$stage,$verID) {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = 
                    "SELECT DISTINCT $attr$className.$attr
                    FROM $className"."_MACHINE_SETUP 
                    JOIN $className"."_MACHINE
                    JOIN MACHINE
                    JOIN accMACHINE
                    JOIN stageMACHINE
                    JOIN $className
                    JOIN property$className 
                    JOIN device$className 
                    JOIN VERSION
                ON $className"."_MACHINE_SETUP.$className"."_MACHINE_ID = $className"."_MACHINE.$className"."_MACHINE_ID
                AND $className"."_MACHINE_SETUP.VERSION_ID = VERSION.VERSION_ID
                AND $className"."_MACHINE.MACHINE_ID  = MACHINE.MACHINE_ID 
                AND $className"."_"."MACHINE.$className"."_ID = $className.$className"."_ID 
                AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID 
                AND MACHINE.stageMACHINE_ID = stageMACHINE.stageMACHINE_ID 
                AND $className.property$className"."_ID = property$className.property$className"."_ID
                AND $className.device$className"."_ID = device$className.device$className"."_ID 
                AND VERSION.VERSION_ID = :verID 
                AND accMACHINE.acc = :acc
                AND stageMACHINE.stage = :stage
                ";
                $stm = $this->conn->prepare($sqlQuery);
                $stm->execute(array(':verID' => $verID,':acc' => $acc,':stage' => $stage));
                $colLabels = $stm->fetchAll(PDO::FETCH_NUM);
                //$this->verboseOutput .= "Table columns labels<br>";
            
                return $colLabels;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }

      /*
    * Function: getClassSetup
    *
    * Parameters: 
    *       $tabName -- table name
    *
    * Description:
    *     Gets all columns labels for the table
    */
    public function getClassSetupProp($className,$dev,$acc,$stage,$verID) {
        if ($this->nameCheck($className) ) {
            try {       
                $sqlQuery = 
                    "SELECT DISTINCT property$className.property
                    FROM $className"."_MACHINE_SETUP 
                    JOIN $className"."_MACHINE
                    JOIN MACHINE
                    JOIN accMACHINE
                    JOIN stageMACHINE
                    JOIN $className
                    JOIN property$className 
                    JOIN device$className 
                    JOIN VERSION
                ON $className"."_MACHINE_SETUP.$className"."_MACHINE_ID = $className"."_MACHINE.$className"."_MACHINE_ID
                AND $className"."_MACHINE_SETUP.VERSION_ID = VERSION.VERSION_ID
                AND $className"."_MACHINE.MACHINE_ID  = MACHINE.MACHINE_ID 
                AND $className"."_"."MACHINE.$className"."_ID = $className.$className"."_ID 
                AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID 
                AND MACHINE.stageMACHINE_ID = stageMACHINE.stageMACHINE_ID 
                AND $className.property$className"."_ID = property$className.property$className"."_ID
                AND $className.device$className"."_ID = device$className.device$className"."_ID 
                AND VERSION.VERSION_ID = :verID 
                AND accMACHINE.acc = :acc
                AND stageMACHINE.stage = :stage
                AND device$className.device = :dev
                ";
                $stm = $this->conn->prepare($sqlQuery);
                $stm->execute(array(':verID' => $verID,':acc' => $acc,':stage' => $stage, ':dev' => $dev));
                $colLabels = array_merge(...$stm->fetchAll(PDO::FETCH_NUM));
                //$this->verboseOutput .= "Table columns labels<br>";
            
                return $colLabels;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
    }


     /*
    * Function: existsAttr
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *         value - str. Name of possible attribute in table
    *
    * Description:
    *     returns the number of entries matching with 'value'
    */
    function existsAttr($class, $attribute, $value) {
        $tableName = $attribute . $class;
        if ( $this->nameCheck($tableName) )
        {
            //print_r($this->conn->query("SELECT EXISTS(SELECT * FROM `propertyBBB` WHERE propertyBBB.property = 'test') ")->fetchAll(PDO::FETCH_NUM));
            $query = "SELECT EXISTS(SELECT * FROM `$tableName` WHERE $tableName.$attribute = :val)";
            try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':val' => $value));
                return $stm->fetch(PDO::FETCH_NUM)[0];
            } catch (PDOException $e) {
                echo "The $attribute value $value can not be repeated!<br>";
            }
        }
    }

    function existsValidation($class, $versionID) {
        if ( $this->nameCheck($class) )
        {
            $tableName = $class . "_MACHINE_VALIDATION";
            //print_r($this->conn->query("SELECT EXISTS(SELECT * FROM `propertyBBB` WHERE propertyBBB.property = 'test') ")->fetchAll(PDO::FETCH_NUM));
            $query = "SELECT EXISTS(SELECT * FROM `$tableName` WHERE $tableName.VERSION_ID = :val)";
            try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':val' => $versionID));
                return $stm->fetch(PDO::FETCH_NUM)[0];
            } catch (PDOException $e) {
                echo "The $attribute value $value can not be repeated!<br>";
            }
        }
    }

    /*
    * Function: existsClassLink
    *
    * Parameters: 
    *       $tabName -- table name
    *
    * Description:
    *     Gets all columns labels for the table
    */
    protected function existsClassLink($class,$subClasses,$link) {  
        $subClass1 = $subClasses[0];
        $subClass2 = $subClasses[1];
        if ( $this->nameCheck($class) && $this->nameCheck($subClass1) && $this->nameCheck($subClass2) )
        {
            $query = "SELECT EXISTS(SELECT * FROM `$class` WHERE $class.$subClass1"."_ID = :val1 AND $class.$subClass2"."_ID = :val2)";
            try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':val1' => $link[0],
                                    ':val2' => $link[1]));
                return $stm->fetch(PDO::FETCH_NUM)[0];
            } catch (PDOException $e) {

            }
        }
            
    }
    
    /*
    * Function: dependencyAtt
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *         value - str. Name of possible attribute in table
    *
    * Description:
    *     returns bool. True/False if exists dependence/no dependence
    */
    protected function dependencyAtt($class, $attribute, $value) {
        $tableName = $attribute . $class;
        if ( $this->nameCheck($tableName) )
        {
            $query = "SELECT * FROM $tableName 
                      JOIN $class 
                      ON $tableName.$attribute = :val
                      AND $tableName.$tableName"."_ID = $class.$tableName"."_ID";
            try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':val' => $value));
                return (bool)$stm->fetch(PDO::FETCH_NUM);
            } catch (PDOException $e) {
                echo "The $attribute value $value can not be repeated!<br>";
            }
        }
    }

    /*
    * Function: getAttID
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *         value - str. Name of possible attribute in table
    *
    * Description:
    *     returns bool. True/False if exists dependence/no dependence
    */
    function getAttrID($attr,$class,$value) {
        $tabName = $attr . $class;
        if ( $this->nameCheck($tabName) )
        {
            $query = "SELECT $tabName.$tabName"."_ID FROM `$tabName` WHERE $tabName.$attr = :val";
            try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':val' => $value));
                return $stm->fetch(PDO::FETCH_NUM)[0];
            } catch (PDOException $e) {
                echo "The $attr value $value can not be repeated!<br>";
            }
        }
    }


    function getTypeFromProp($class,$propID) {
        $query = "SELECT property$class"."_DATA.typeDATA_ID FROM `property$class"."_DATA` 
                    WHERE  property$class"."_DATA.property$class"."_ID = :val";
        try {
            $stm = $this->conn->prepare($query);
            $stm->execute(array(':val' => $propID));
            $id = $stm->fetch(PDO::FETCH_NUM)[0];
            $query = "SELECT typeDATA.type FROM `typeDATA` 
                    WHERE  typeDATA.typeDATA_ID = :val";
            try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':val' => $id));
                return $stm->fetch(PDO::FETCH_NUM)[0];
            } catch (PDOException $e) {
                echo "The $id  not be found!<br>";
            }
            
        } catch (PDOException $e) {
            echo "The $class property $class can not be found!<br>";
        }
                    
    }
    function getAttrFromID($attr,$class,$value) {
        $tabName = $attr . $class;
        if ( $this->nameCheck($tabName) )
        {
            $attrNames = array();
            foreach ($value as $val) {
                $query = "SELECT $tabName.$attr FROM `$tabName` WHERE $tabName.$tabName"."_ID = :val";
                try {
                    $stm = $this->conn->prepare($query);
                    $stm->execute(array(':val' => $val));
                    array_push($attrNames,$stm->fetch(PDO::FETCH_NUM));
                } catch (PDOException $e) {
                    echo "The $attr value $value can not be repeated!<br>";
                }
            }
            return $attrNames;
        }
    }

    /*
    * Function: getAttID
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *         value - str. Name of possible attribute in table
    *
    * Description:
    *     returns bool. True/False if exists dependence/no dependence
    */
    function getVerID($stamp) {
        
        $query = "SELECT VERSION.VERSION_ID FROM `VERSION` WHERE VERSION.stamp = :val";
        try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':val' => $stamp));
                return $stm->fetch(PDO::FETCH_NUM)[0];
        } catch (PDOException $e) {
                
        }
    
    }

    /*
    * Function: getAttID
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *         value - str. Name of possible attribute in table
    *
    * Description:
    *     returns bool. True/False if exists dependence/no dependence
    */
    function getVerComment($stamp) {
        
        $query = "SELECT VERSION.comment FROM `VERSION` WHERE VERSION.stamp = :val";
        try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':val' => $stamp));
                return $stm->fetch(PDO::FETCH_NUM)[0];
        } catch (PDOException $e) {
                
        }
    
    }

    function getVerStamp($id) {
        
        $query = "SELECT VERSION.stamp FROM `VERSION` WHERE VERSION.VERSION_ID = :id";
        try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':id' => $id));
                return $stm->fetch(PDO::FETCH_NUM)[0];
        } catch (PDOException $e) {
                
        }
    
    }

    function getVerLabel($id) {
        
        $query = "SELECT VERSION.label FROM `VERSION` WHERE VERSION.VERSION_ID = :id";
        try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':id' => $id));
                return $stm->fetch(PDO::FETCH_NUM)[0];
        } catch (PDOException $e) {
                
        }
    
    }



    /*
    * Function: getClassID
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *         value - array. Name of possible attribute in table
    *
    * Description:
    *     returns bool. True/False if exists dependence/no dependence
    */
    function getClassID($attr,$class,$value) {
        $attr1 =  $attr[0] . '_ID';
        $attr2 =  $attr[1] . '_ID';
        if ( $this->nameCheck($class) && $this->nameCheck($attr1) && $this->nameCheck($attr2))
        {
            $query = "SELECT $class"."_ID FROM `$class` WHERE $class.$attr1 = :val1 AND $class.$attr2 = :val2";
            try {
                $stm = $this->conn->prepare($query);
                $stm->execute(array(':val1' => $value[0],
                                    ':val2' => $value[1]));
                return $stm->fetch(PDO::FETCH_NUM)[0];
            } catch (PDOException $e) {
                //echo "The $attr value $value can not be repeated!<br>";
            }
        }
    }

    /*
    * Function: getClassID
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *         value - array. Name of possible attribute in table
    *
    * Description:
    *     returns bool. True/False if exists dependence/no dependence
    */
  /*  function getSetupVer($acc) {

        
        $setupList = $this->getDBTables('%\_SETUP');
        $classSetup = $setupList[0];
        $classMachine = str_replace('_SETUP','',$classSetup);

       
        $sql = "SELECT DISTINCT VERSION.label
        FROM VERSION
        JOIN $classSetup
        JOIN $classMachine
        JOIN MACHINE
        JOIN accMACHINE
        ON VERSION.VERSION_ID = $classSetup.VERSION_ID
        AND $classSetup.$classMachine"."_ID = $classMachine.$classMachine"."_ID
        AND $classMachine.MACHINE_ID = MACHINE.MACHINE_ID
        AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID
        AND accMACHINE.acc = :acc";
 
        //Prepare our SQL statement,
        $statement = $this->conn->prepare($sql);
 
        //Execute the statement.
        $statement->execute(array(':acc'=>$acc));
 
        //Fetch the rows from our statement.
        
        return array_merge(...$statement->fetchAll(PDO::FETCH_NUM));
        
        //return $rows;
    }
*/
     /*
    * Function: getClassID
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *         value - array. Name of possible attribute in table
    *
    * Description:
    *     returns bool. True/False if exists dependence/no dependence
    */
    function getSetupComment($acc) {

        $setupList = $this->getDBTables('%\_SETUP');
        $classSetup = $setupList[0];
        $classMachine = str_replace('_SETUP','',$classSetup);

       
        $sql = "SELECT DISTINCT VERSION.comment
        FROM VERSION
        JOIN $classSetup
        JOIN $classMachine
        JOIN MACHINE
        JOIN accMACHINE
        ON VERSION.VERSION_ID = $classSetup.VERSION_ID
        AND $classSetup.$classMachine"."_ID = $classMachine.$classMachine"."_ID
        AND $classMachine.MACHINE_ID = MACHINE.MACHINE_ID
        AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID
        AND accMACHINE.acc = :acc";
 
        //Prepare our SQL statement,
        $statement = $this->conn->prepare($sql);
 
        //Execute the statement.
        $statement->execute(array(':acc'=>$acc));
 
        //Fetch the rows from our statement.
        
        return array_merge(...$statement->fetchAll(PDO::FETCH_NUM));
        
        //return $rows;
    }
   
       /*
    * Function: getClassID
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *         value - array. Name of possible attribute in table
    *
    * Description:
    *     returns bool. True/False if exists dependence/no dependence
    */
    function getAttrVer($acc,$attr) {

        if ( $this->nameCheck($attr) )
        {
        $setupList = $this->getDBTables('%\_SETUP');
        $classSetup = $setupList[0];
        $classMachine = str_replace('_SETUP','',$classSetup);

       
        $sql = "SELECT DISTINCT VERSION.$attr
        FROM VERSION
        JOIN $classSetup
        JOIN $classMachine
        JOIN MACHINE
        JOIN accMACHINE
        ON VERSION.VERSION_ID = $classSetup.VERSION_ID
        AND $classSetup.$classMachine"."_ID = $classMachine.$classMachine"."_ID
        AND $classMachine.MACHINE_ID = MACHINE.MACHINE_ID
        AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID
        AND accMACHINE.acc = :acc
        ";
 
        //Prepare our SQL statement,
        $statement = $this->conn->prepare($sql);
 
        //Execute the statement.
        $statement->execute(array(':acc'=>$acc));
 
        //Fetch the rows from our statement.
        return array_reverse(array_merge(...$statement->fetchAll(PDO::FETCH_NUM)));
        }
        
        //return $rows;
    }

    function getValidationVerIDs($acc) {
        $verIDs = array();
        $validationList = $this->getDBTables('%\_VALIDATION');
        
        foreach($validationList as $classValidation) {
            $classMachine = str_replace('_VALIDATION','',$classValidation);

            $sql = "SELECT DISTINCT VERSION.VERSION_ID
                    FROM VERSION
                    JOIN $classValidation
                    JOIN $classMachine
                    JOIN MACHINE
                    JOIN accMACHINE
                    ON VERSION.VERSION_ID = $classValidation.VERSION_ID
                    AND $classValidation.$classMachine"."_ID = $classMachine.$classMachine"."_ID
                    AND $classMachine.MACHINE_ID = MACHINE.MACHINE_ID
                    AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID
                    AND accMACHINE.acc = :acc
                    ";
            
            $statement = $this->conn->prepare($sql);
            $statement->execute(array(':acc'=>$acc));

            $verIDs = array_merge($verIDs,array_merge(...$statement->fetchAll(PDO::FETCH_NUM)));
              
        }
        $uniqueVerIDs = array_unique($verIDs);
        rsort($uniqueVerIDs);
        return $uniqueVerIDs;
    }

    function getSetupMatch($id) {
            $sql = "SELECT VERSION.Matching FROM VERSION where VERSION.VERSION_ID = :id";
            //Prepare our SQL statement,
            $statement = $this->conn->prepare($sql);
            //Execute the statement.
            $statement->execute(array(':id'=>$id));
            //Fetch the rows from our statement.
            return array_merge(...$statement->fetchAll(PDO::FETCH_NUM))[0];
    }

    function getSetupVerIDs($acc) {
        $verIDs = array();
        $setupList = $this->getDBTables('%\_SETUP');
        
        foreach($setupList as $classSetup) {
            $classMachine = str_replace('_SETUP','',$classSetup);

            $sql = "SELECT DISTINCT VERSION.VERSION_ID
                    FROM VERSION
                    JOIN $classSetup
                    JOIN $classMachine
                    JOIN MACHINE
                    JOIN accMACHINE
                    ON VERSION.VERSION_ID = $classSetup.VERSION_ID
                    AND $classSetup.$classMachine"."_ID = $classMachine.$classMachine"."_ID
                    AND $classMachine.MACHINE_ID = MACHINE.MACHINE_ID
                    AND MACHINE.accMACHINE_ID = accMACHINE.accMACHINE_ID
                    AND accMACHINE.acc = :acc
                    ";
            
            $statement = $this->conn->prepare($sql);
            $statement->execute(array(':acc'=>$acc));

            $verIDs = array_merge($verIDs,array_merge(...$statement->fetchAll(PDO::FETCH_NUM)));
              
        }
        $uniqueVerIDs = array_unique($verIDs);
        rsort($uniqueVerIDs);
        return $uniqueVerIDs;
    }

    function getSetupAttrByID($attr,$IDs) {
        $attrList = array();
        if ( $this->nameCheck($attr) )
        {
            foreach($IDs as $id) {
                $sql = "SELECT VERSION.$attr FROM VERSION WHERE VERSION.VERSION_ID = :id";
                $statement = $this->conn->prepare($sql);
                $statement->execute(array(':id'=>$id));
                array_push($attrList,array_merge(...$statement->fetchAll(PDO::FETCH_NUM)));
            }
        }
        return array_merge(...$attrList);
    }

    function getVersionAttrByID($attr,$IDs) {
        $attrList = array();
        if ( $this->nameCheck($attr) )
        {
            foreach($IDs as $id) {
                $sql = "SELECT VERSION.$attr FROM VERSION WHERE VERSION.VERSION_ID = :id";
                $statement = $this->conn->prepare($sql);
                $statement->execute(array(':id'=>$id));
                array_push($attrList,array_merge(...$statement->fetchAll(PDO::FETCH_NUM)));
            }
        }
        return array_merge(...$attrList);
    }

    function getVer($acc,$attr) {
        if ( $this->nameCheck($attr) )
        {
            $sql = "SELECT DISTINCT VERSION.$attr FROM VERSION";
            //Prepare our SQL statement,
            $statement = $this->conn->prepare($sql);
            //Execute the statement.
            $statement->execute(array(':acc'=>$acc));
            //Fetch the rows from our statement.
            return array_reverse(array_merge(...$statement->fetchAll(PDO::FETCH_NUM)));
        }
    }

    function getVer2($acc) {
        $sql = "SELECT DISTINCT VERSION.$attr FROM VERSION";
        $statement = $this->conn->prepare($sql);
        $statement->execute(array(':acc'=>$acc));
        return array_reverse(array_merge(...$statement->fetchAll(PDO::FETCH_NUM)));
    }


    /*
    * Function: createVersion
    *
    * Parameters: 
    *     
    *     
    *
    * Description:
    *     Cretates a table called 'VERSION' with columns:
    *                   stamp -- DATETIME NULL (if attribute === 'ver')
    *                 comment -- TINYTEXT NULL (if attribute === 'ver')
    */
    function createVer() {
        try {
            $this->conn->query("SELECT 1 FROM `VERSION` LIMIT 1");
            $this->verboseOutput .= "Table VERSION already exists<br>";
        } catch (PDOException $e) {
            $queryTab = "CREATE TABLE `VERSION` (
                    `VERSION_ID` INT NOT NULL AUTO_INCREMENT,
                    `stamp` DATETIME NULL,
                    `label` VARCHAR(45),
                    `comment` TEXT NULL, 
                    `Matching` int NULL, 
                    PRIMARY KEY (`VERSION_ID`),
                    UNIQUE INDEX `VERSION_ID_UNIQUE` (`VERSION_ID` ASC)
                    ) ENGINE = InnoDB;";
        
            $this->conn->query($queryTab);
            $this->verboseOutput .=  "Table VERSION was created<br>";
        }

}


     /*
    * Function: insertVer
    *
    * Parameters: 
    *         stamp - str. Name of the class  
    *       comment - str. Name of the attribute
    *
    * Description:
    *     Adds new entry 
    */
    public function insertVer($stamp,$label,$comment,$matching) {
    
        $query = "INSERT INTO `VERSION` (`stamp`,`label`,`comment`,`Matching`) VALUES (:stamp,:label,:comment,:matching);";
        try {
            $stm = $this->conn->prepare($query);
            $stm->execute(array(':stamp' => $stamp,
                                ':label' => $label,
                                ':comment' => $comment,
                                ':matching' => $matching));
        } catch (PDOException $e) {
           
        }

    }



     /*
    * Function: insertVer
    *
    * Parameters: 
    *         stamp - str. Name of the class  
    *       comment - str. Name of the attribute
    *
    * Description:
    *     Adds new entry 
    */
    public function updateVer($id,$stamp,$label,$comment,$matching) {
        try {
            $query = "UPDATE `VERSION` SET `stamp`=:stamp,`label`=:label,`comment`=:comment, `matching`=:matching
                        WHERE VERSION.VERSION_ID =:id;";
            $stm = $this->conn->prepare($query);
            $stm->execute(array(':id' => $id,
                                ':stamp' => $stamp,
                                ':label' => $label,
                                ':comment' => $comment,
                                ':matching' => $matching));
        } catch (PDOException $e) {
           
        }

    }
 
    /*
   
    function insertClassValidation($class, $classInfo) {    
    
        $insertQuery = "";
        $tab = explode('_',$class)[0];


        foreach ($classInfo["_info"] as $match)
        {
            $query1 = "SELECT `accVALIDATION_ID` FROM `accVALIDATION` WHERE `acc` = '$match[0]'";
            $entry1 = $this->conn->query($query1)->fetchColumn();

            $query2 = "SELECT `stageVALIDATION_ID` FROM `stageVALIDATION` WHERE `stage` = '$match[1]'";
            $entry2 = $this->conn->query($query2)->fetchColumn();

            $query3 = "SELECT `verVALIDATION_ID` FROM `verVALIDATION` WHERE `ver` = '$match[2]'";
            $entry3 = $this->conn->query($query3)->fetchColumn();

            $query4 = "SELECT `VALIDATION_ID` FROM `VALIDATION` 
            WHERE `accVALIDATION_ID` = '$entry1' 
            AND `stageVALIDATION_ID` = '$entry2' 
            AND `verVALIDATION_ID` = '$entry3'";

            $entry4 = $this->conn->query($query4)->fetchColumn();

            $query5 = "SELECT `device$tab"."_ID` FROM `device$tab` WHERE `device` = '$match[3]'";
            $entry5 = $this->conn->query($query5)->fetchColumn();
            $query6 = "SELECT `property$tab"."_ID` FROM `property$tab` WHERE `property` = '$match[4]'";
            $entry6 = $this->conn->query($query6)->fetchColumn();

            $query7 = "SELECT `$tab"."_ID` FROM `$tab` 
            WHERE `device$tab"."_ID` = '$entry5' AND `property$tab"."_ID` = '$entry6'";
            $entry7 = $this->conn->query($query7)->fetchColumn();

            if (gettype($match[5]) == 'array') {
                $value = implode(" </br> ",$match[5]);
            } else if (gettype($match[5]) == 'boolean'){
                $value = $match[5] ? 'True' : 'False'; 
            } else {
                $value = $match[5];
            }

            //$insertQuery = $insertQuery . 
            $insertQuery = "INSERT INTO `$class` (`VALIDATION_ID`, `$tab"."_ID`, `value`,`error`)
            VALUES (:valID,:tabID,:val,:error);";
            //Prepare our SQL statement,
            $stm = $this->conn->prepare($insertQuery);
            //Execute the statement.
            $stm->execute(array(
            ':valID'=>$entry4,
            ':tabID'=>$entry7,
            ':rang'=>$match[6],
            ':error'=>$match[7]
        ));

        }           
       echo "Entries inserted <br>";
    }
    */

       /*
    * Function: insertClassSetup
    *
    * Parameters: 
    *     class - Name of the class  
    *     settings - Name of the class 
    *
    * Description:
    *     Cretates a table called 'class_settings' with columns:
    *     'class_settings'_ID -- PRIMARY KEY INT NOT NULL
    *              'class'_ID -- FOREIGN KEY INT NOT NULL
    *           'settings'_ID -- FOREIGN KEY INT NOT NULL
    *                 'value' -- VARCHAR(255) NULL
    *                 'range' -- VARCHAR(45) NULL 
    *                 'error' -- VARCHAR(45) NULL 
    */
    public function insertClassSetup($classMachine, $values) {
        $tableName = $classMachine . "_SETUP";
        if ( $this->nameCheck($tableName) )
        {
            $classMachine_ID = $classMachine . '_ID';
            foreach ($values as $value)
            {
                    $query = "INSERT INTO `$tableName` ($classMachine_ID, `VERSION_ID`, `value`, `error`) 
                            VALUES (:classID,:verID,:val,:error);";
                    try {
                        $stm = $this->conn->prepare($query);
                        $stm->execute(array(':classID' => $value[0],
                                            ':verID' => $value[1],
                                            ':val' => $value[2],
                                            ':error' => $value[3]));
                    } catch (PDOException $e) {
                        echo "The $attribute value $value can not be repeated!<br>";
                    } 
            }
        }
    }

    public function insertClassValidation($classMachine, $values) {
        $tableName = $classMachine . "_VALIDATION";
        if ( $this->nameCheck($tableName) )
        {
            $classMachine_ID = $classMachine . '_ID';
            foreach ($values as $value)
            {
                    $query = "INSERT INTO `$tableName` ($classMachine_ID, `VERSION_ID`, `value`) 
                            VALUES (:classID,:verID,:val);";
                    try {
                        $stm = $this->conn->prepare($query);
                        $stm->execute(array(':classID' => $value[0],
                                            ':verID' => $value[1],
                                            ':val' => $value[2]));
                    } catch (PDOException $e) {
                        echo "The d!<br>";
                    } 
            }
        }
    }

    public function updateClassValidation($classMachine, $values) {
        $tableName = $classMachine . "_VALIDATION";
        if ( $this->nameCheck($tableName) )
        {
            $classMachine_ID = $classMachine . '_ID';
            foreach ($values as $value)
            {
                    $query = "UPDATE `$tableName` 
                            SET `value` = :val
                            WHERE $classMachine_ID = :classID
                            AND $tableName.VERSION_ID = :verID";
                    try {
                        $stm = $this->conn->prepare($query);
                        $stm->execute(array(':classID' => $value[0],
                                            ':verID' => $value[1],
                                            ':val' => $value[2]));
                    } catch (PDOException $e) {
                        echo "The d!<br>";
                    } 
            }
        }
    }

    function buildClasses($file) {

        $devClassInfo = json_decode(file_get_contents($file), true);

        foreach (array_keys($devClassInfo) as $class) {
            $foreignTables = array();
            foreach (array_keys($devClassInfo[$class]) as $attribute) {
                if ($attribute !== "_info") {
                    $this->createAttr($class, $attribute);// create table attribute
                    $values = $devClassInfo[$class][$attribute];
                    $this->insertAttr($class, $attribute, $values);// insert attributes

                    array_push($foreignTables, $attribute . $class);
                }
            }
            $this->createClass($class, $foreignTables);
            if ($class != 'VALIDATION') {
                $this->insertClass($class, $devClassInfo[$class]);
                $this->createClassValidation($class);
            }
            
        }
    }

    function buildValidation($file) {
        $dataDB = json_decode(file_get_contents($file), true);

        
        //----------------------------- New validation
        
       // $keys = array_keys($ver);
        $insertQuery = "INSERT INTO `verVALIDATION` (ver, stamp, comment) 
                    VALUES (:ver,:stamp,:comment);";

        //Prepare our SQL statement,
        $statement = $this->conn->prepare($insertQuery);
 
        //Execute the statement.
         $statement->execute(array(
            ':ver'=>$dataDB["verVALIDATION"]['ver'],
            ':stamp'=>$dataDB["verVALIDATION"]['stamp'],
            ':comment'=>$dataDB["verVALIDATION"]['comment']
        ));

        //$this->conn->query($insertQuery);

        $this->insertClass("VALIDATION", $dataDB["VALIDATION"]);

        //------------------------------ update classes
        foreach (array_keys($dataDB) as $classVal) {
            echo $classVal."</br>";
            if ($classVal !== "verVALIDATION" && $classVal !== "VALIDATION") {
                $this->insertClassValidation($classVal, $dataDB[$classVal]);
            }
            echo $classVal."</br>";
        
        }
    
    }


    function getValTab($class) {
        //Our SQL statement, which will select a list of tables from the current MySQL database.
        $sql = "SELECT verVALIDATION.ver, accVALIDATION.acc, stageVALIDATION.stage, 
        device$class.device, property$class.property, 
        $class"."_VALIDATION.value, $class"."_VALIDATION.error
        FROM $class"."_VALIDATION 
        JOIN property$class 
        JOIN device$class 
        JOIN $class
        JOIN verVALIDATION
        JOIN accVALIDATION
        JOIN stageVALIDATION
        JOIN VALIDATION
        ON verVALIDATION.verVALIDATION_ID = VALIDATION.verVALIDATION_ID
        AND accVALIDATION.accVALIDATION_ID = VALIDATION.accVALIDATION_ID
        AND stageVALIDATION.stageVALIDATION_ID = VALIDATION.stageVALIDATION_ID
        AND property$class.property$class"."_ID = $class.property$class"."_ID 
        AND device$class.device$class"."_ID = $class.device$class"."_ID
        AND $class.$class"."_ID = $class"."_VALIDATION.$class"."_ID
        AND VALIDATION.VALIDATION_ID = $class"."_VALIDATION.VALIDATION_ID";
 
        //Prepare our SQL statement,
        $statement = $this->conn->prepare($sql);
        //Execute the statement.
        $statement->execute();
 
        //Fetch the rows from our statement.
        $rows = $statement->fetchAll(PDO::FETCH_NUM);

        return $rows;
    }

    function getAttributes($ver,$stage,$class,$classDev) {
    //
        $sql = "SELECT property$class.property, 
        $class"."_VALIDATION.value, $class"."_VALIDATION.error
        FROM $class"."_VALIDATION 
        JOIN property$class 
        JOIN device$class 
        JOIN $class
        JOIN verVALIDATION
        JOIN accVALIDATION
        JOIN stageVALIDATION
        JOIN VALIDATION
        ON verVALIDATION.verVALIDATION_ID = VALIDATION.verVALIDATION_ID
        AND accVALIDATION.accVALIDATION_ID = VALIDATION.accVALIDATION_ID
        AND stageVALIDATION.stageVALIDATION_ID = VALIDATION.stageVALIDATION_ID
        AND property$class.property$class"."_ID = $class.property$class"."_ID 
        AND device$class.device$class"."_ID = $class.device$class"."_ID
        AND $class.$class"."_ID = $class"."_VALIDATION.$class"."_ID
        AND VALIDATION.VALIDATION_ID = $class"."_VALIDATION.VALIDATION_ID
        AND stageVALIDATION.stage = :stage
        AND device$class.device = :classDev
        AND verVALIDATION.ver = :ver";
 
        //Prepare our SQL statement,
        $statement = $this->conn->prepare($sql);
      
 
        //Execute the statement.
        $statement->execute(array(
            ':stage' => $stage,
            ':classDev' => $classDev,
            ':ver' => $ver
        ));
 
        //Fetch the rows from our statement.
        $rows = $statement->fetchAll(PDO::FETCH_NUM);

        return $rows;
    }
    function getAllDevicesAttributes($ver) {
        $allDevicesAttributes = array();

        $stages = array('NONE','COLD','HOT');
        $this->getAllClassesDevices();
        $classes = array_keys($this->allClassesDevicesList);
        foreach ($stages as $stage) {
            foreach ($classes as $class) {
                $classDevs = $this->allClassesDevicesList[$class];
                foreach ($classDevs as $dev) {
                    $row = $this->getAttributes($ver,$stage,$class,$dev);
                    if (sizeof($row)>0) {
                        $allDevicesAttributes[$stage][$class][$dev] = $row;
                    }
                }               
            }       
        }
       
        return  $allDevicesAttributes;


    }

    function getValidations($acc) {
        //
        $sql = "SELECT DISTINCT verVALIDATION.ver, verVALIDATION.stamp, verVALIDATION.comment 
        FROM VALIDATION JOIN verVALIDATION JOIN accVALIDATION
        ON verVALIDATION.verVALIDATION_ID = VALIDATION.verVALIDATION_ID
        AND accVALIDATION.accVALIDATION_ID = VALIDATION.accVALIDATION_ID
        AND accVALIDATION.acc = :acc";
 
        //Prepare our SQL statement,
        $statement = $this->conn->prepare($sql);
 
        //Execute the statement.
        $statement->execute(array(':acc'=>$acc));
 
        //Fetch the rows from our statement.
        $this->validationsTab = $statement->fetchAll(PDO::FETCH_NUM);

        $sql = "SELECT DISTINCT verVALIDATION.ver
        FROM VALIDATION JOIN verVALIDATION JOIN accVALIDATION
        ON verVALIDATION.verVALIDATION_ID = VALIDATION.verVALIDATION_ID
        AND accVALIDATION.accVALIDATION_ID = VALIDATION.accVALIDATION_ID
        AND accVALIDATION.acc = :acc";
 
        //Prepare our SQL statement,
        $statement = $this->conn->prepare($sql);
 
        //Execute the statement.
        $statement->execute(array(':acc'=>$acc));
 
        //Fetch the rows from our statement.
        $this->validationsList = array_merge(...$statement->fetchAll(PDO::FETCH_NUM));
        
        //return $rows;
    }

    function getClassesNames() {
        $sql = "SHOW TABLES LIKE '%\_VALIDATION'";
        $stm = $this->conn->prepare($sql);
        $stm->execute();
        $this->classesNamesList = str_replace("_VALIDATION", "",array_merge(...$stm->fetchAll(PDO::FETCH_NUM)));;
    }

    

    function getClassDevices($class) {
        $devC = "device".$class;
        $sql = "SELECT `device` FROM $devC";
        $stm = $this->conn->prepare($sql);
        $stm->execute();
        $this->classDevicesList = str_replace("_VALIDATION", "",array_merge(...$stm->fetchAll(PDO::FETCH_NUM)));;
    }

    function getAllClassesDevices() {
        /* gets all the devices names per class and stores it in $allClassesDevicesList */

        $this->allClassesDevicesList = array();
        $this->getClassesNames();
        foreach ($this->classesNamesList as $class) {
            $this->getClassDevices($class);
            $this->allClassesDevicesList[$class] = $this->classDevicesList;
        }
    }

    public $validationsList;
    public $validationsTab;
    public $tablesList;
    public $classesNamesList;
    public $classDevicesList;
    public $allClassesDevicesList;

}



/*
    function insertAttr($class, $attribute, $values) {
        $insertQuery = "";
        $tableName = $attribute . $class;
        $count = 0;

        foreach ($values as $value)
        {
            $insertQuery = "INSERT INTO `$tableName` ($attribute) VALUES ('$value');";
            try {
                $this->conn->query($insertQuery);
                $count++;
            } catch (PDOException $e) {
                echo "The $attribute value $value can not be repeated!<br>";
            }
        
        }
        echo "Entries inserted: $count<br>";
    }
    
    function insertClass($class, $classInfo) {    
    
        $insertQuery = "";

        $classArray = array();
        $keys = array_keys($classInfo);

    

        foreach ($classInfo["_info"] as $match)
        {
        
            if ($class === 'VALIDATION') {
                $query1 = "SELECT `accVALIDATION_ID` FROM `accVALIDATION` WHERE `acc` = '$match[0]'";
                $entry1 = $this->conn->query($query1)->fetchColumn();
                $query2 = "SELECT `stageVALIDATION_ID` FROM `stageVALIDATION` WHERE `stage` = '$match[1]'";
                $entry2 = $this->conn->query($query2)->fetchColumn();
                $query3 = "SELECT `verVALIDATION_ID` FROM `verVALIDATION` WHERE `ver` = '$match[2]'";
                $entry3 = $this->conn->query($query3)->fetchColumn();
          
                $insertQuery = $insertQuery . 
                "INSERT INTO `$class` (`accVALIDATION_ID`,`stageVALIDATION_ID`,`verVALIDATION_ID`) 
                VALUES ($entry1,$entry2,$entry3);";
            } else {
                $query1 = "SELECT `$keys[0]$class"."_ID` FROM `$keys[0]$class` WHERE `$keys[0]` = '$match[1]'";
                $entry1 = $this->conn->query($query1)->fetchColumn();
                $query2 = "SELECT `$keys[1]$class"."_ID` FROM `$keys[1]$class` WHERE `$keys[1]` = '$match[0]'";
                $entry2 = $this->conn->query($query2)->fetchColumn();
            
                $insertQuery = $insertQuery . 
                "INSERT INTO `$class` (`$keys[0]$class"."_ID`, `$keys[1]$class"."_ID`) VALUES ($entry1,$entry2);";
            }

        }   
        $this->conn->query($insertQuery); 

    
        echo "Entries inserted <br>";
    }
    */