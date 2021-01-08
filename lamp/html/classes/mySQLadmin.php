<?php

/* Class: mySQLadmin
*
* A class that 
*/

class mySQLadmin  extends mySQL {

    public function __construct() {
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '__construct'.$numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }

    }
    public function __construct0() {
        $this->setVerbose(true);
        $this->pdo($this->servername,$this->username,$this->password);
    }
    public function __construct2($nameDB,$verbose) {
        $this->setVerbose($verbose);
        $this->pdo($this->servername,$this->username,$this->password);
        $this->createDB($nameDB);
        $this->useDB($nameDB);
    }

    function __destruct() {
        //close connection
        //$this->conn = null; 
    }

   
    
    /*
    * Function: createAttr
    *
    * Parameters: 
    *     class - Name of the class  
    *     attribute - Name of the attribute in the class
    *
    * Description:
    *     Cretates a table called 'attribute+class' with columns:
    *    'attribute+class'_ID -- PRIMARY KEY INT NOT NULL UNIQUE
    *               attribute -- VARCHAR(45) NULL UNIQUE
    */
    function createAttr($class, $attribute) {
        $tableName = $attribute . $class;
        if ($this->nameCheck($tableName))
        {
            try {
                $this->conn->query("SELECT 1 FROM $tableName LIMIT 1");
                $this->verboseOutput .= "Table $tableName already exists<br>";
            } catch (PDOException $e) {
                $id = $tableName . "_ID";
                $idUnique = $id . "_UNIQUE";
                $attributeUnique = $attribute . "_UNIQUE";

                $queryTab = "CREATE TABLE `$tableName` (
                        `$id` INT NOT NULL AUTO_INCREMENT,
                        `$attribute` VARCHAR(45) NULL,";
                $queryTab = $queryTab .      
                        "PRIMARY KEY (`$id`),
                        UNIQUE INDEX `$idUnique` (`$id` ASC),
                        UNIQUE INDEX `$attributeUnique` (`$attribute` ASC) 
                        ) ENGINE = InnoDB;";
            
                $this->conn->query($queryTab);
                $this->verboseOutput .=  "Table $tableName was created<br>";
            }
        } 
    }

    

    /*
    * Function: createClass
    *
    * Parameters: 
    *     class - Name of the class  
    *     foreignTables - array of names of the tables linked to this class
    *
    * Description:
    *     Cretates a table called 'class' with columns:
    *              'class'_ID -- PRIMARY KEY INT NOT NULL
    *   'foreignTables[i]'_ID -- FOREIGN KEY INT NOT NULL
    */
    function createClass($class, $foreignTables) {
        if ($this->nameCheck($class)) {
            try {
                $this->conn->query("SELECT 1 FROM $class LIMIT 1");
                $this->verboseOutput .= "Table $class already exists<br>";
            } catch (PDOException $e) {
                $id = $class . "_ID";

                $queryTab = "CREATE TABLE `$class` (";
                foreach ($foreignTables as $table) {
                    $tableId = $table . "_ID";
                    //$tableIdUnique = $tableId . "_UNIQUE";
                    $fkId = "fk_" . $class . "_$table";

                    $queryTab = $queryTab . "`$tableId` INT NOT NULL,";
                    $queryTab = $queryTab . "CONSTRAINT `$fkId` FOREIGN KEY (`$tableId`)
                                    REFERENCES `$table` (`$tableId`)
                                    ON DELETE CASCADE ON UPDATE CASCADE,";
                } 
                $queryTab = $queryTab . " `$id` INT NOT NULL AUTO_INCREMENT,";
                $queryTab = $queryTab . "PRIMARY KEY (`$id`)) ENGINE = InnoDB";
                $this->conn->query($queryTab);
                $this->verboseOutput .=  "Table $class was created<br>";
            }
        }
    }

    /*
    * Function: createClassSetup
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
    function createClassSetup($class) {
        if ($this->nameCheck($class)) {
            try {
                $tabName = $class . "_SETUP";
                $existsTab = $this->conn->query("SELECT 1 FROM `$tabName` LIMIT 1");
                $this->verboseOutput .= "Table $tabName already exists<br>";
            } catch (PDOException $e) {
                $id = $tabName . "_ID";
                $fcId = $class . "_ID";
                $fkClass = "fk_" . $class . "_SETUP";
                $fkVer = "fk_" . $class . "_VERSION_SETUP";

                $queryTab = "CREATE TABLE `$tabName` (
                            `$id` INT NOT NULL AUTO_INCREMENT,
                            `VERSION_ID` INT NOT NULL, 
                            `$class"."_ID` INT NOT NULL,
                            `value` VARCHAR(255) NULL,
                            `error` VARCHAR(45) NULL,
                            PRIMARY KEY (`$id`), 
                            CONSTRAINT `$fkClass` FOREIGN KEY (`$fcId`) REFERENCES `$class` (`$fcId`)
                            ON DELETE NO ACTION ON UPDATE NO ACTION,
                            CONSTRAINT `$fkVer` FOREIGN KEY (`VERSION_ID`) REFERENCES `VERSION` (`VERSION_ID`)
                            ON DELETE CASCADE ON UPDATE CASCADE)ENGINE = InnoDB;";

                $this->conn->query($queryTab);
                $this->verboseOutput .= "Creating table $tabName<br>";
            }
        }
    }

    /*
    * Function: createClassValidation
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
    *                 'SETUP_VERSION_ID' -- INT NOT NULL 
    */
    function createClassValidation($class) {
        if ($this->nameCheck($class)) {
            try {
                $tabName = $class . "_VALIDATION";
                $existsTab = $this->conn->query("SELECT 1 FROM `$tabName` LIMIT 1");
                $this->verboseOutput .= "Table $tabName already exists<br>";
            } catch (PDOException $e) {
                $id = $tabName . "_ID";
                $fcId = $class . "_ID";
                $fkClass = "fk_" . $class . "_VALIDATION";
                $fkVer = "fk_" . $class . "_VERSION_VALIDATION";

                $queryTab = "CREATE TABLE `$tabName` (
                            `$id` INT NOT NULL AUTO_INCREMENT,
                            `VERSION_ID` INT NOT NULL, 
                            `$class"."_ID` INT NOT NULL,
                            `value` VARCHAR(255) NULL,
                            PRIMARY KEY (`$id`), 
                            CONSTRAINT `$fkClass` FOREIGN KEY (`$fcId`) REFERENCES `$class` (`$fcId`)
                            ON DELETE NO ACTION ON UPDATE NO ACTION,
                            CONSTRAINT `$fkVer` FOREIGN KEY (`VERSION_ID`) REFERENCES `VERSION` (`VERSION_ID`)
                            ON DELETE CASCADE ON UPDATE CASCADE)ENGINE = InnoDB;";

                $this->conn->query($queryTab);
                $this->verboseOutput .= "Creating table $tabName<br>";
            }
        }
    }

   


    /*
    * Function: deleteTable
    *
    * Parameters: 
    *     tabName - Name of the table  
    *
    * Description:
    *     Check is the table has dependencies. If not the table is droped.
    */
    function deleteTable($tabName) {
        if ( $this->nameCheck($tabName) ) {
            try {
                $stm = $this->conn->query("SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME,
                                            REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                                            FROM
                                            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                                            WHERE 
                                            REFERENCED_TABLE_NAME = '$tabName';");         
                $dependency = $stm->fetchAll(PDO::FETCH_NUM);
                if (empty($dependency)) {
                    $this->conn->query("DROP TABLE `$tabName`;");
                    $this->verboseOutput .= "Table $tabName was deleted<br>";
                } else {
                    $this->verboseOutput .= "Table $tabName cannot be deleted due to dependencies:<br>";
                    foreach ($dependency as $dep) {
                        $tab = $dep[0];//First entry on the dependency path 
                        $this->verboseOutput .= "$tab<br>";
                    }    
                }
                
            } catch (PDOException $e) {
                $this->verboseOutput .= "Table $tabName does not exist<br>";
            }
        }
    
    }

   


    /*
    * Function: insertAttr
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *        values - str array. New names for attribute column
    *
    * Description:
    *     Adds new names in the attribute+class table
    */
    public function insertAttr($class, $attribute, $values) {
        $tableName = $attribute . $class;
        if ( $this->nameCheck($tableName) )
        {
            foreach ($values as $value)
            {
                if ( $this->existsAttr($class, $attribute, $value) == 0 ) {
                    $query = "INSERT INTO `$tableName` ($attribute) VALUES (:val);";
                    try {
                        $stm = $this->conn->prepare($query);
                        $stm->execute(array(':val' => $value));
                    } catch (PDOException $e) {
                        echo "The $attribute value $value can not be repeated!<br>";
                    }
                } else {
                    echo "The $attribute value $value can not be repeated!<br>";
                }
                
            }
        }
    }


    /*
    * Function: deleteAttr
    *
    * Parameters: 
    *         class - str. Name of the class  
    *     attribute - str. Name of the attribute
    *        values - str array. New names for attribute column
    *
    * Description:
    *     Check is the table has dependencies. If not the table is droped.
    */
    function deleteAttr($class, $attribute, $values) {
        $tableName = $attribute . $class;
        if ( $this->nameCheck($tableName) ) {
            foreach ($values as $value) {
                try {   
                    if ( !$this->dependencyAtt($class, $attribute, $value) ) {
                        $query = "DELETE FROM `$tableName` WHERE `$tableName`.`$attribute` = :val";
                        $stm = $this->conn->prepare($query);
                        $stm->execute(array(':val' => $value));
                        $this->verboseOutput .= "Attribute " . htmlentities($value) . " was deleted<br>";
                    } else {
                        $this->verboseOutput .= "Table $tabName cannot be deleted due to dependencies:<br>";
                        foreach ($dependency as $dep) {
                            $tab = $dep[0];//First entry on the dependency path 
                            $this->verboseOutput .= "$tab<br>";
                        }    
                    }
                
                } catch (PDOException $e) {
                    $this->verboseOutput .= "Table $tabName does not exist<br>";
                }
            }
        }
    
    }

    /*
    * Function: insertClassLinks
    *
    * Parameters: 
    *         class - str. Name of the class  
    *    subClasses - 2D str array. Name of sub classes
    *         links - array of 2D array. 
    *
    * Description:
    *     Each 2D array has the ID of the subclasses to be likned in order. Inserts row to table 'class'
    */
    function insertClassLinks($class,$subClasses,$links) {  
        $subClass1 = $subClasses[0];
        $subClass2 = $subClasses[1];
        if ( $this->nameCheck($class) && $this->nameCheck($subClass1) && $this->nameCheck($subClass2) )
        {
            foreach ($links as $link) {
                if ( !$this->existsClassLink($class,$subClasses,$link) ) {
                    $query = "INSERT INTO $class ($subClass1"."_ID,$subClass2"."_ID) VALUES (:val1,:val2);";
                    try {
                        $stm = $this->conn->prepare($query);
                        $stm->execute(array(':val1' => $link[0],
                                    ':val2' => $link[1]));
                    //return $stm->fetch(PDO::FETCH_NUM);
                    } catch (PDOException $e) {
                        
                    }
                }
            
            }
        }
    }


}
