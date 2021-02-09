<?php
ob_start();
session_start();

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.html.php');
    return;
}

include __DIR__ . '/../includes/autoloader.php';





$adminDB = new mySQLadmin;
$forms = new forms();
$tab = new prettyTab();
$load = new load();

$labelDB = 'DB';
$labelCL = 'CLASS';
$labelPROP = 'PROPERTY';
$labelDEV = 'DEVICE';
$labelSTAGE = 'STAGE';
$labelACC = 'ACC';


function addNewLinks($labelCL,$newLinks,$adminDB) {
    $linkCL = array();
        $rows = array("property$labelCL","device$labelCL");
        foreach ($newLinks as $link) {
            array_push($linkCL, array($adminDB->getAttrID('property',$labelCL,$link[0]),
                                        $adminDB->getAttrID('device',$labelCL,$link[1])));
            }
            $adminDB->insertClassLinks($labelCL,array("property$labelCL","device$labelCL"),
                                         $linkCL);
            
        $linkCLMAC = array();
        $rowsMAC = array("stageMACHINE","accMACHINE");
        foreach ($newLinks as $link) {
            $idProp = $adminDB->getAttrID('property',$labelCL,$link[0]);
            $idDev = $adminDB->getAttrID('device',$labelCL,$link[1]);
            $idStage = $adminDB->getAttrID('stage','MACHINE',$link[2]);
            $idACC = $adminDB->getAttrID('acc','MACHINE',$link[3]);
            array_push($linkCLMAC, array($adminDB->getClassID($rows,$labelCL,array($idProp,$idDev)),
                                        $adminDB->getClassID($rowsMAC,'MACHINE',array($idStage,$idACC))));
        }


        $adminDB->insertClassLinks($labelCL."_MACHINE",
                                    array($labelCL,'MACHINE'),$linkCLMAC);
                                    
}

function addDataTypeInfo($labelCL,$prop,$type,$adminDB) {
    $linkCL = array();
        for ($i=0; $i<sizeof($prop); $i++) {
            array_push($linkCL, array($adminDB->getAttrID('property',$labelCL,$prop[$i]),
                                        $adminDB->getAttrID('type','DATA',$type[$i])));
            }
            $adminDB->insertClassLinks("property$labelCL" . "_DATA",array("property$labelCL","typeDATA"),
                                         $linkCL);
                                    
}


/*
    New / Delete form for Database section
*/
if  ( isset($_POST["new$labelDB"]) ) {
    unset($_SESSION[$labelDB]);
    unset($_SESSION[$labelCL]);
    unset($_SESSION["del$labelDB"]);
    unset($_SESSION["new$labelCL"]);
    unset($_SESSION["del$labelCL"]);
    $_SESSION["new$labelDB"] = $_COOKIE["newLabel"];
    header("Location: admin.html.php");
    return;
} else if ( isset($_POST["del$labelDB"]) ) {
    unset($_SESSION[$labelCL]);
    unset($_SESSION["new$labelDB"]);
    unset($_SESSION["new$labelCL"]);
    unset($_SESSION["del$labelCL"]);
    $_SESSION["del$labelDB"] = $_POST["del$labelDB"];
    header("Location: admin.html.php");
    return;
}

/*
    New / Delete form for Class section
*/
if ( isset($_FILES['backbone']['tmp_name']) ) {
    $_SESSION['backbone'] =  json_decode(file_get_contents($_FILES['backbone']['tmp_name']),true);
    header("Location: admin.html.php");
    return;
} else if  ( isset($_POST["new$labelCL"]) ) {
    unset($_SESSION[$labelCL]);
    unset($_SESSION["del$labelCL"]);
    $_SESSION["new$labelCL"] = $_COOKIE["newLabel"];;
    header("Location: admin.html.php");
    return;
} else if ( isset($_POST["del$labelCL"]) ) {
    //unset($_SESSION[$labelCL]);
    unset($_SESSION["new$labelCL"]);
    $_SESSION["del$labelCL"] = $_POST["del$labelCL"];
    header("Location: admin.html.php");
    return;
}


if ( isset($_POST["new$labelPROP"]) ) {
    $_SESSION["new$labelPROP"] =$_POST["new$labelPROP"];
    $_SESSION["new$labelPROP"."Type"] = $_COOKIE['propertyType'];
    header("Location: admin.html.php");
    return;
} else if ( isset($_POST["new$labelDEV"]) ) {
    $_SESSION["new$labelDEV"] = $_POST["new$labelDEV"];
    header("Location: admin.html.php");
    return;
}

if ( isset($_POST["del$labelPROP"]) ) {
    $_SESSION["del$labelPROP"] =  array_keys($_POST[$labelPROP]);
    header("Location: admin.html.php");
    return;
} else if ( isset($_POST["del$labelDEV"]) ) {
    $_SESSION["del$labelDEV"] =  array_keys($_POST[$labelDEV]);
    header("Location: admin.html.php");
    return;
}

if ( isset($_FILES['backboneLinks']['tmp_name']) ) {
    $_SESSION['backboneLinks'] =  json_decode(file_get_contents($_FILES['backboneLinks']['tmp_name']),true);
    header("Location: admin.html.php");
    return;
} else if ( isset($_POST['newLinks']) ) {
    if ( isset($_POST[$labelPROP]) && isset($_POST[$labelDEV]) && 
         isset($_POST[$labelSTAGE]) && isset($_POST[$labelACC]) ) {
            $_SESSION['newLinks'] = array();
            $_SESSION['newLinksCL'] = array();
             foreach (array_keys($_POST[$labelPROP]) as $prop) {
                foreach (array_keys($_POST[$labelDEV]) as $dev) {
                    array_push($_SESSION['newLinksCL'], array($prop,$dev));
                    foreach (array_keys($_POST[$labelSTAGE]) as $stage) {
                        foreach (array_keys($_POST[$labelACC]) as $acc) {
                            array_push($_SESSION['newLinks'], 
                                array($prop,$dev,$stage,$acc));
                        }
                        
                    }
                }
             }
         }
    header("Location: admin.html.php");
    return;
}

if ( isset($_POST[$labelCL]) ) {
    $_SESSION[$labelCL] = $_POST[$labelCL];
    unset($_SESSION["new$labelCL"]);
    unset($_SESSION["del$labelCL"]);
    header("Location: admin.html.php");
    return;

 } 

if ( isset($_POST[$labelDB]) ) {
    unset($_SESSION["new$labelDB"]);
    unset($_SESSION["del$labelDB"]);
    unset($_SESSION["new$labelCL"]);
    unset($_SESSION["del$labelCL"]);
    unset($_SESSION['class']);
    $_SESSION[$labelDB] = $_POST[$labelDB];
    header("Location: admin.html.php");
    return;

} 
    
$formClass = '';
$printSetup = '';

//if ($_POST['tab']) {
    //print_r($_POST);
//}

if ( isset($_SESSION["new$labelDB"]) ) {
    /* CREATE DATABASE */
    $adminDB->createDB($_SESSION["new$labelDB"]);

    /* LOAD DATABASE */
    $adminDB->useDB($_SESSION["new$labelDB"]);

    /* CREATE 'MACHINE' and 'VERSION' CLASSES */
    $adminDB->createVer();
    //$comment = "This is the first comment.\n Bla Bla Bla";
    //$label = "R1@400mA - W.33";
    //$stamp = date("Y/m/d H:i:s");
    //$adminDB->insertVer($stamp,$label,$comment);

    $adminDB->createAttr("DATA", "type");
    $adminDB->insertAttr("DATA", "type", 
            array('checked','bool','float','int','str','file'));

    
  //  $adminDB->createAttr("BPM", "name");
  //  foreach(array('R1','R3') as $ring) {
  //      $bpmList = json_decode(file_get_contents("../mySQL/bpm_".$ring.".json"),true);
  //      $adminDB->insertAttr("BPMs", "name",$bpmList['DIA']);
  //  } 
    
    $adminDB->createAttr("MACHINE", "acc");
    $adminDB->insertAttr("MACHINE", "acc", array('R1','R3'));
    $adminDB->createAttr("MACHINE", "stage");
    $adminDB->insertAttr("MACHINE", "stage", array('NONE','COLD','HOT'));
    $adminDB->createClass("MACHINE", Array("accMACHINE","stageMACHINE"));
    $links = array(array(1,1),array(1,2),array(1,3),array(2,1),array(2,2),array(2,3));
    $adminDB->insertClassLinks('MACHINE',array('accMACHINE','stageMACHINE'),$links);


    //-------- Create GOST class setup

    $adminDB->createAttr("GHOST", "property");
    $adminDB->insertAttr("GHOST", "property", array('prop'));
    $adminDB->createAttr("GHOST", "device");
    $adminDB->insertAttr("GHOST", "device", array('dev'));

    $adminDB->createClass("GHOST", array("propertyGHOST","deviceGHOST"));
    $adminDB->insertClassLinks('GHOST',array('propertyGHOST','deviceGHOST'),array(array(1,1)));

    
    $adminDB->createClass("GHOST_MAC", array("GHOST","MACHINE"));
    $adminDB->insertClassLinks('GHOST_MAC',array("GHOST","MACHINE"),array(array(1,1),array(1,4)));

    $adminDB->createClassSetup("GHOST_MAC");
    $adminDB->createClassValidation("GHOST_MAC");
    

    $_SESSION[$labelDB] = $_SESSION["new$labelDB"];
    unset($_SESSION["new$labelDB"]);
    
} else if ( isset($_SESSION["del$labelDB"]) && isset($_SESSION[$labelDB]) ) {
    $adminDB->deleteDB($_SESSION[$labelDB]);
    unset($_SESSION["del$labelDB"]);
    unset($_SESSION[$labelDB]);
} else if ( isset($_SESSION["rename$labelDB"]) ) {

}

if ( isset($_SESSION[$labelDB]) ) {
    /* LOAD DATABASE */
    $adminDB->useDB($_SESSION[$labelDB]);

    /* UPDATE CLASSES */
    if  ( isset($_SESSION["new$labelCL"]) ) {
        $className = $_SESSION["new$labelCL"];
        $adminDB->createAttr($className, "device");
        $adminDB->createAttr($className, "property");

        //$adminDB->createAttr($className."_NO_TYPE", "property");
        $adminDB->createClass("property$className"."_DATA", array("typeDATA","property$className"));

        $adminDB->createClass($className, array("device$className","property$className"));
        $adminDB->createClass($className."_MACHINE", array($className,"MACHINE"));

        $adminDB->createClassSetup($className."_MACHINE");
        $adminDB->createClassValidation($className."_MACHINE");

        unset($_SESSION["new$labelCL"]);
    } else if  ( isset($_SESSION["del$labelCL"]) && isset($_SESSION[$labelCL])  ) {
        $adminDB->deleteTable($_SESSION[$labelCL].'_MACHINE_SETUP');
        $adminDB->deleteTable($_SESSION[$labelCL].'_MACHINE_VALIDATION');
        $adminDB->deleteTable($_SESSION[$labelCL].'_MACHINE');
        $adminDB->deleteTable($_SESSION[$labelCL]);
        $adminDB->deleteTable('device'.$_SESSION[$labelCL]);
        $adminDB->deleteTable('property'.$_SESSION[$labelCL]);

        unset($_SESSION["del$labelCL"]);
        unset($_SESSION[$labelCL]);
    } else if  ( isset($_SESSION["rename$labelCL"]) ) {

    } 

    if (isset($_SESSION['backbone'])) {
        $classes = array_keys($_SESSION['backbone']);
        

        foreach($classes as $class) {
                $adminDB->createAttr($class, "device");
                $adminDB->insertAttr($class,"device", $_SESSION['backbone'][$class]['device']);
                $adminDB->createAttr($class, "property");
                $adminDB->insertAttr($class,"property", $_SESSION['backbone'][$class]['property']['name']);

           /*     if (isset($_SESSION['backbone'][$class]['devList'])) {
                    foreach (array_keys($_SESSION['backbone'][$class]['devList']) as $acc) {
                        $adminDB->createAttr("_".$class."_".$acc, "devList");
                        $adminDB->insertAttr("_".$class."_".$acc, "devList",
                                            $_SESSION['backbone'][$class]['devList'][$acc]);
                    }
                    
                }
                */

                $adminDB->createClass("property$class"."_DATA", array("typeDATA","property$class"));
                addDataTypeInfo($class,
                $_SESSION['backbone'][$class]['property']['name'],
                $_SESSION['backbone'][$class]['property']['type'],$adminDB);

                $adminDB->createClass($class, array("device$class","property$class"));
                $adminDB->createClass($class."_MACHINE", array($class,"MACHINE"));

                $adminDB->createClassSetup($class."_MACHINE");
                $adminDB->createclassValidation($class."_MACHINE");

                addNewLinks($class,$_SESSION['backbone'][$class]['links'],$adminDB);

                //$adminDB->insertClassSetup($class."_MACHINE", array(array(1,1,'10','0|100','2|5')));
                $_SESSION[$labelCL] = $class; 
        }
        
        unset($_SESSION['backbone']);
    }

    /* GET CLASSES LIST */
    $classList = str_replace("_MACHINE", "",$adminDB->getDBTables('%\_MACHINE'));

    /* CLASSES FORM */
    $choosenCL = isset($_SESSION[$labelCL]) ? $_SESSION[$labelCL] : '';
    $formClass = $forms->adminSCD($labelCL,$classList,$choosenCL);
    //$formClass .= '<span> <b> LOAD BACKBONE</b></span></br></br>';
    $formClass .= '<form action=""  method="post" enctype="multipart/form-data">';
    $formClass .= "<input type='file' id='file' onchange='this.form.submit()' name='backbone' />
    <label for = 'file'>Load BackBone</label>";
    $formClass .= '</form>';

    //$verList = $adminDB->getSetupVer('R1');
    //print_r($verList);
    //$choosenVer = '';
    //$labelVER = 'VERSION';
    //$formVersion = $forms->checkCRUD($labelVER,$verList,$choosenVer);

    

    if ( isset($_SESSION[$labelCL]) ) {
    if ( isset($_SESSION["new$labelPROP"]) ) {
        $adminDB->insertAttr($_SESSION[$labelCL],"property", array($_SESSION["new$labelPROP"]));
        $linkCL = array(array($adminDB->getAttrID('property',$_SESSION[$labelCL],$_SESSION["new$labelPROP"]),
                        $adminDB->getAttrID('type','DATA',$_SESSION["new$labelPROP"."Type"])));
        $adminDB->insertClassLinks("property".$_SESSION[$labelCL] . "_DATA",array("property".$_SESSION[$labelCL],"typeDATA"),
                                         $linkCL);
        unset($_SESSION["new$labelPROP"]);
        unset($_SESSION["new$labelPROP"."Type"]);
    } else if ( isset($_SESSION["new$labelDEV"]) ) {
        $adminDB->insertAttr($_SESSION[$labelCL],"device", array($_SESSION["new$labelDEV"]));
        unset($_SESSION["new$labelDEV"]);
    }

    if ( isset($_SESSION["del$labelPROP"]) ) {
        $adminDB->deleteAttr($_SESSION[$labelCL], "property", $_SESSION["del$labelPROP"]);
        unset($_SESSION["del$labelPROP"]);
    } else if ( isset($_SESSION["del$labelDEV"]) ) {
        $adminDB->deleteAttr($_SESSION[$labelCL], "device", $_SESSION["del$labelDEV"]);
        unset($_SESSION["del$labelDEV"]);
    }

    if ( isset($_SESSION['backboneLinks']) ) {
        addNewLinks($_SESSION[$labelCL],$_SESSION['backboneLinks']['links'],$adminDB);  
        unset($_SESSION['backboneLinks']);
    } else if ( isset($_SESSION['newLinks']) ) {
        //print_r($_SESSION['newLinks']);
        addNewLinks($_SESSION[$labelCL],$_SESSION['newLinks'],$adminDB);
        
        unset( $_SESSION['newLinks']);
    }

    
            #$propList = str_replace("_", " ",
            #        array_merge(...$adminDB->getTabRows( 'property'.$_SESSION[$labelCL], array('property') )));
            //$propTypesID = array_merge(...$adminDB->getTabRows( 'property'.$_SESSION[$labelCL].'_DATA', array('typeDATA_ID') ));

            $dataID = $adminDB->getTabRows( 'property'.$_SESSION[$labelCL].'_DATA', array("property$_SESSION[$labelCL]"."_ID",'typeDATA_ID') );            
            $propListID = array();
            $propTypesID = array();
            foreach ($dataID as $id) {
                array_push($propListID,$id[0]);
                array_push($propTypesID,$id[1]);
            }


            $propTypes = array_merge(...$adminDB->getAttrFromID('type','DATA',$propTypesID));
            $propList = array_merge(...$adminDB->getAttrFromID('property',$_SESSION[$labelCL],$propListID));
            
            $devList = 
                    array_merge(...$adminDB->getTabRows( 'device'.$_SESSION[$labelCL], array('device') ));
            $stageList = 
                    array_merge(...$adminDB->getTabRows( 'stageMACHINE', array('stage') ));
            $accList = 
                    array_merge(...$adminDB->getTabRows( 'accMACHINE', array('acc') ));
            
            
            
           
            $printSetup = "<div style='float: left;width:100%;' >";
            $printSetup .= '<form method="post">';
            $printSetup .= "<div style='float: left; width:25%;' >";
            $printSetup .= $forms->checkMultipleBox($devList,array(),"DEVICE");
            $printSetup .= '</div>';
            $printSetup .= "<div style='float: left; width:25%;' >";
            $printSetup .= $forms->checkMultipleBoxCOMMENT($propList,$propTypes,array(),"PROPERTY");
            $printSetup .= '</div>';
            $printSetup .= "<div style='float: left;width:15%;' >";
            $printSetup .= $forms->checkMultipleBoxSmall($stageList,array(),"STAGE");
            $printSetup .= '</div>';
            $printSetup .= "<div style='float: left; width:15%;' >";
            $printSetup .= $forms->checkMultipleBoxSmall($accList,array(),"ACC");
            $printSetup .= '</div>';
            $printSetup .= "<div style='float: left; width:20%;' >";
            $printSetup .= '<span> <b> CREATE LINKS</b></span></br></br>';
            $printSetup .= '<input type="submit" id ="submit"  name="newLinks" value="Add Manually">
            <label for="submit" >Add Manually</label>';
            $printSetup .= '</form>';
            $printSetup .= '<form action="" method="post" enctype="multipart/form-data">';
            $printSetup .= '<input type="file" id = "file2" onchange="this.form.submit()" name="backboneLinks"  
            /> <label for="file2" >Load BackBone</label>';
            $printSetup .= '</form>';
            $printSetup .= '</div>';
            $printSetup .= '</div>';
            
            

            $printSetup .= "<div style='width:100%;' >";
            $printSetup .= "<div style='float: left; width:22%;' >";
            $printSetup .= $forms->buttonRDA("DEVICE",array());
            $printSetup .= '</div>';
            $printSetup .= "<div style='float: left; width:22%;' >";
            $printSetup .= $forms->buttonRDA2("PROPERTY",array());
            $printSetup .= '</div>';
            $printSetup .= "<div style='float: left; width:16%;' > </div>";
            $printSetup .= "<div style='float: left; width:16%;' > </div>";
            $printSetup .= "<div style='float: left; width:24%;' > </div>";
            $printSetup .= '</div>';

            $printSetup .= '<div style="padding:5px;margin-top:20em;background-color:#02a7fa;"></div>';

            
    
            $tab->setLabels(array('ACC','STAGE','DEVICE','PROPERTY'));
            $tab->setRows($adminDB->getClassMachineLinks($_SESSION[$labelCL]));
            $printSetup .= $tab->printTabActions('','');

           // $tab->setLabels(array('ACC','STAGE','DEVICE','PROPERTY','value','range','error'));
           // $tab->setRows($adminDB->getClassSetup($_SESSION[$labelCL],1));
            //$tab->setRows($adminDB->getClassMachineLinks($_SESSION[$labelCL]));
           // $printSetup .= $tab->printEditableTab(array(false,false,false,false,true,true,true));

}


} 

 /* GET DATABASES LIST */
$listDB = $adminDB->getDBs();

/* DATABASE FORM */
$choosenDB = isset($_SESSION[$labelDB]) ? $_SESSION[$labelDB] : '';
$formDB = $forms->adminSCD('DB',$listDB,$choosenDB);





?>

<script type="text/javascript" src="../JS/confirmWindow.js"></script>

<?php include('./headerAdmin.html.php');?>






    <div class = "leftLayout" >  
          
        
            <div class = "inner" >
                <?php
                echo $formDB;
                echo "</br></br>";
                echo $formClass;
                //echo "</br></br>";
                //echo $formVersion;
                ?>  
            </div> 

             
    </div>

    <div class = "rightLayout">
       
        <div class = "inner"> 
      
            <?php 
                echo($printSetup);
            ?>

        </div>  

        
    </div>
        


<br>
            

<?php include('./footer.html.php');?>
