<?php

class mySQL {

    function __construct() {
        /* -------------------------
        Server Connection
        ------------------------- */ 
        $servername = "localhost";
        $username = "phpadmin_hugser";
        $password = "#COSMOS12FCP#";

        $this->conn = new PDO("mysql:host=$servername; port=3306; ", $username, $password);
        // set the PDO error mode to exception
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connection to " . $servername . " successfull<br>";

    }
    
    function __destruct() {
        //close connection
        $this->conn = null; 
    }

    private $nameDB = 'validationDB';

    function useDB() {// query if 'nameDB' exists and if not creates it
        $sqlQuery = $this->conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA 
                                        WHERE SCHEMA_NAME = '$this->nameDB';");
        //Fetch the columns from the returned PDOStatement
        $existsDatabase = (bool) $sqlQuery->fetchColumn();
        if ($existsDatabase) {
            //echo "Database '$this->nameDB' exists<br>";
            $this->conn->exec("USE $this->nameDB;");
        } else {
            echo "Creating '$this->nameDB'<br>";
            try {
                $this->conn->exec("CREATE DATABASE $this->nameDB DEFAULT CHARACTER SET utf8;");
                $this->conn->exec("USE $nameDB;");
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
    
        }
        
    }


    /* ----------------------------
        Table manipulations
    ---------------------------- */ 

    // ----------------- Create Generic Tables

    function createAttr($class, $attribute) {//creates tab attributes (dev, prop, STAGE, ACC)
        $tableName = $attribute . $class;
    
        try {
            $existsTab = $this->conn->query("SELECT 1 FROM " . $tableName . " LIMIT 1");
            echo "Table $tableName already exists<br>";
        } catch (PDOException $e) {
            echo "Table $tableName does not exist<br>";

            $id = $tableName . "_ID";
            $idUnique = $id . "_UNIQUE";
            $attributeUnique = $attribute . "_UNIQUE";

            $queryTab = "CREATE TABLE `$tableName` (
                    `$id` INT NOT NULL AUTO_INCREMENT,
                    `$attribute` VARCHAR(45) NULL,";
            if ($attribute === 'ver') {
                $queryTab = $queryTab . "`stamp` DATETIME NULL,
                                    `comment` TINYTEXT NULL,";
            }
            $queryTab = $queryTab .      
                    "PRIMARY KEY (`$id`),
                    UNIQUE INDEX `$idUnique` (`$id` ASC),
                    UNIQUE INDEX `$attributeUnique` (`$attribute` ASC) 
                    ) ENGINE = InnoDB;";

            $this->conn->query($queryTab); 
        }
    
    }
    function createClass($class, $foreignTables) {// creates class table 
        try {
            $existsTab = $this->conn->query("SELECT 1 FROM " . $class . " LIMIT 1");
            echo "Table $class already exists<br>";
        } catch (PDOException $e) {
            echo "Creating table $class<br>";
            $id = $class . "_ID";

            $queryTab = "CREATE TABLE `$class` (";
            foreach ($foreignTables as $table) {
                $tableId = $table . "_ID";
                $tableIdUnique = $tableId . "_UNIQUE";
                $fkId = "fk_" . $class . "_$table";

                $queryTab = $queryTab . "`$tableId` INT NOT NULL,";
                $queryTab = $queryTab . "CONSTRAINT `$fkId` FOREIGN KEY (`$tableId`)
                                    REFERENCES `$table` (`$tableId`)
                                    ON DELETE CASCADE ON UPDATE CASCADE,";
            } 
            $queryTab = $queryTab . " `$id` INT NOT NULL AUTO_INCREMENT,";
            $queryTab = $queryTab . "PRIMARY KEY (`$id`)) ENGINE = InnoDB";
            $this->conn->query($queryTab);
        }
    
    }
    function createClassValidation($class) {// creates final validation table per class 
        try {
            $tabName = $class . "_VALIDATION";
            $existsTab = $this->conn->query("SELECT 1 FROM `$tabName` LIMIT 1");
            echo "Table $tabName already exists<br>";
        } catch (PDOException $e) {
            echo "Creating table $tabName<br>";

            $id = $tabName . "_ID";
            $fcId = $class . "_ID";
            //$fcIdUnique = $fcId . "_UNIQUE";  UNIQUE INDEX `$fcIdUnique` (`$fcId` ASC),
            $fkClass1 = "fk_" . $tabName . "_$class";
            $fkClass2 = "fk_" . $tabName . "_VALIDATION";

            $queryTab = "CREATE TABLE `$tabName` ( 
                    `$id` INT NOT NULL AUTO_INCREMENT,
                    `VALIDATION_ID` INT NOT NULL,
                    `$fcId` INT NOT NULL,
                    `value` VARCHAR(255) NULL,
                    `range` VARCHAR(45) NULL,
                    `error` VARCHAR(45) NULL,
                    PRIMARY KEY (`$id`),
                    CONSTRAINT `$fkClass1`
                    FOREIGN KEY (`$fcId`)
                    REFERENCES `$class` (`$fcId`)
                    ON DELETE NO ACTION ON UPDATE NO ACTION,
                    CONSTRAINT `$fkClass2`
                    FOREIGN KEY (`VALIDATION_ID`)
                    REFERENCES `VALIDATION` (`VALIDATION_ID`)
                    ON DELETE CASCADE ON UPDATE CASCADE)
                    ENGINE = InnoDB;";

            $this->conn->query($queryTab);
        }
    
    }

    // ----------------- Delete Generic Tables

    function drop($name) {//deletes table 
        try {
            $existsTab = $this->conn->query("DROP TABLE `$name`;");
            echo "Table $name was deleted<br>";
        } catch (PDOException $e) {
            echo "Table $name does not exist<br>";
        }
    
    }
    function destroyClasses($file) {

        $devClassInfo = json_decode(file_get_contents($file), true);

        foreach (array_keys($devClassInfo) as $class) {
            if ($class !== 'VALIDATION') $this->drop($class . '_VALIDATION');
            $this->drop($class);
            foreach (array_keys($devClassInfo[$class]) as $attribute) {
                $tableName = $attribute . $class;
                $this->drop($tableName);
            }
        
        }
    }

    // ----------------- Insert elements in Tables


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
            $insertQuery = "INSERT INTO `$class` (`VALIDATION_ID`, `$tab"."_ID`, `value`,`range`,`error`)
            VALUES (:valID,:tabID,:val,:rang,:error);";
            //Prepare our SQL statement,
            $stm = $this->conn->prepare($insertQuery);
            //Execute the statement.
            $stm->execute(array(
            ':valID'=>$entry4,
            ':tabID'=>$entry7,
            ':val'=>$value,
            ':rang'=>$match[6],
            ':error'=>$match[7]
        ));

        }           
       echo "Entries inserted <br>";
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
        $class"."_VALIDATION.value, $class"."_VALIDATION.error, $class"."_VALIDATION.range 
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
        $class"."_VALIDATION.value, $class"."_VALIDATION.error, $class"."_VALIDATION.range 
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

    function getDBTables() {
        $sql = "SHOW TABLES";
        $stm = $this->conn->prepare($sql);
        $stm->execute();
        $this->tablesList = array_merge(...$stm->fetchAll(PDO::FETCH_NUM));
 
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