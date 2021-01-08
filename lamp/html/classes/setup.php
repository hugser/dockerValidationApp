<?php

class setup {

   

    public function __construct($input) {
        $this->mySQL = $input['mySQL'];
        $this->tab = $input['table'];
        $this->forms = $input['forms'];
        $this->labelSetup = $input['Setup label'];
        $this->labelCL = $input['Class label'];
        $this->stages = $input['stages'];
        $this->classList = $input['Classes List'];
    }
   
    function __destruct() {
       
    }

    public function SESSION_newSetupDev() {
        if (isset($_POST['DEVICES'])) {
            $class = $_SESSION['setup'][$_GET['accSetup']][$this->labelCL];
            
            foreach($this->stages as $stage) {
                if ( isset($_POST[$class][$stage]) ) {
                    $dev = $_POST[$class][$stage];
                    unset($_SESSION['setup'][$_GET['accSetup']]['OPEN']);
                    $_SESSION['setup'][$_GET['accSetup']]['OPEN'][$class][$stage] = $dev;
        
                    if (!isset($_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev])) {
                        $idVer = $this->mySQL->getVerID($_SESSION['setup'][$_GET['accSetup']][$this->labelSetup]);
                        $_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev] =  
                            $this->mySQL->getClassSetupProp($class, $dev, $_GET['accSetup'], $stage, $idVer);
                    }
            
                    header("Location: setup.html.php?accSetup=".$_GET['accSetup']."&accStage=".$_GET['accStage']);
                    return;
                }
            }
        }
    }

    public function loadJSON() {
        if ( isset($_FILES["load$this->labelCL"]['tmp_name']) ) {
            $data = json_decode(file_get_contents($_FILES["load$this->labelCL"]['tmp_name']),true);
        
            foreach (array_keys($data) as $class) {
                unset($_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class]);
                foreach ($data[$class]['data'] as $settings) {
                    $prop = $settings[0];
                    $dev = $settings[1];
                    $stage = $settings[2];
                    $acc = $settings[3];
                    $val = $settings[4];
                    $error = (sizeof($settings)==6)? $settings[5]: '';
        
                    if ($acc == $_GET['accSetup']) {
                        $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"] = $val;
                        $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@error"] = $error;
                         
                        if (!isset($_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev])) {
                            $_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev] = array();
                        }
                        array_push($_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev],$prop);
                    }
        
                }
            }
        
            header("Location: setup.html.php?accSetup=".$_GET['accSetup']. "&accStage=".$_GET['accStage']);
            return;
        }
        
    }

    public function SESSION_newSetupProp() {
        if (isset($_POST['PROPERTIES'])) {
            $class = $_SESSION['setup'][$_GET['accSetup']][$this->labelCL];
        
            $stage = array_keys($_POST['PROPERTIES'][$class])[0];
            $dev = array_keys($_POST['PROPERTIES'][$class][$stage])[0];
        
            $propVec = array();
            foreach (array_keys($_POST[$class][$stage][$dev]) as $prop) {
                array_push($propVec,$prop);
            }
            $_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev] = $propVec;
        
            header("Location: setup.html.php?accSetup=".$_GET['accSetup']."&accStage=".$_GET['accStage']);
            return;
           
        }
    }

    public function SESSION_updateStage() {
        foreach($this->stages as $stage) {

            if ( isset($_POST["Update_$stage"]) ) {
                $devices = array_merge(...$this->mySQL->getTabRows( 'device'.$_SESSION['setup'][$_GET['accSetup']][$this->labelCL], array('device') ));
                foreach($devices as $dev) {
                    $properties = array_merge(...$this->mySQL->getClassPropPerDev($_SESSION['setup'][$_GET['accSetup']][$this->labelCL],$dev,$_GET['accSetup'],$stage));
                    foreach($properties as $prop) {
                        if ( isset($_POST[$stage ."@". $dev . "@" . $prop . "@value"]) ) {
                            if ($_POST[$stage ."@". $dev . "@" . $prop . "@value"] !== '')
                            {
                                $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"] = $_POST[$stage ."@". $dev . "@" . $prop . "@value"];
                                $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@error"] = 
                                (isset($_POST[$stage ."@". $dev . "@" . $prop . "@error"])) ? 
                                implode('|',$_POST[$stage ."@". $dev . "@" . $prop . "@error"]) : '';
                            }
                        }
                        $_SESSION["type_$stage"][$dev][$prop] = isset($_POST["type_$stage"][$dev][$prop]) ? 
                                                                $_POST["type_$stage"][$dev][$prop] :
                                                                '';
                    }
                    
                }
        
                header("Location: setup.html.php?accSetup=".$_GET['accSetup']."&accStage=".$_GET['accStage']);
                return;
            }
        }
   
    }

    public function SESSION_updateComment() {
        if (isset($_POST['comment']) && $_POST['comment'] !=='') {
            $class = array_keys($_SESSION['setup'][$_GET['accSetup']]['OPEN'])[0];
            $stage = array_keys($_SESSION['setup'][$_GET['accSetup']]['OPEN'][$class])[0];
            $dev = $_SESSION['setup'][$_GET['accSetup']]['OPEN'][$class][$stage];
            $_SESSION['setup'][$_GET['accSetup']]['comment'][$class][$stage][$dev] = $_POST['comment'];
        
            header("Location: setup.html.php?accSetup=".$_GET['accSetup']."&accStage=".$_GET['accStage']);
            return;
        }
        
    }


    public function newSetup() {
        if (isset($_SESSION['setup'][$_GET['accSetup']]['newSETUP'])) {
            $arrayComment['Updated from']="New empty setup";
            $arrayComment['Updated by']=$_SESSION['user'];

            $comment = json_encode($arrayComment);
            $label = $_COOKIE['newLabel'];
            $stamp = date("Y/m/d H:i:s");
            $this->mySQL->insertVer($stamp,$label,$comment,0);
            $idVer = $this->mySQL->getVerID($stamp);
        
            //-------- Create GOST class setup
        
            $idACC = $this->mySQL->getAttrID('acc',"MACHINE",$_GET['accSetup']);
            $idMACHINE = $this->mySQL->getClassID(array("accMACHINE","stageMACHINE"),"MACHINE",array($idACC,1));
            $idClassMACHINE = $this->mySQL->getClassID(array("GHOST","MACHINE"),"GHOST_MAC",array(1,$idMACHINE));
            $this->mySQL->insertClassSetup("GHOST_MAC", array(array($idClassMACHINE,$idVer,'','')));
        
            unset($_SESSION['setup'][$_GET['accSetup']]);    
            unset($_GET['accStage']);
        }
    }

    public function updateSetup() {
        if ( isset($_SESSION['setup'][$_GET['accSetup']]['updateSETUP'])) {

            $arrayComment = isset($_SESSION['setup'][$_GET['accSetup']]['comment'])? 
                    $_SESSION['setup'][$_GET['accSetup']]['comment']:
                    array();
            $arrayComment['Updated from']=$_SESSION['setup'][$_GET['accSetup']][$this->labelSetup];
            $arrayComment['Updated by']=$_SESSION['user'];

            $comment = json_encode($arrayComment);
    

            $oldVerID = $this->mySQL->getVerID($_SESSION['setup'][$_GET['accSetup']][$this->labelSetup]);
            $label = ($_COOKIE['newLabel']!='')? $_COOKIE['newLabel']: $this->mySQL->getVerLabel($oldVerID);
    
            $stamp = date("Y/m/d H:i:s");
    
            $idACC = $this->mySQL->getAttrID('acc',"MACHINE",$_GET['accSetup']);

        foreach ($this->classList as $class) {
            $update = array();
            $devices = array_merge(...$this->mySQL->getTabRows( 'device'.$class, array('device') ));
        
            foreach ($devices as $dev) {
                $idDev = $this->mySQL->getAttrID('device',$class,$dev);
                foreach ($this->stages as $stage) {
                    $idStage = $this->mySQL->getAttrID('stage',"MACHINE",$stage);

                    $properties = 
                    array_merge(...$this->mySQL->getClassPropPerDev($class,$dev,$_GET['accSetup'],$stage));
                    $originalSetup = $this->mySQL->getClassSetupPerDev($class,$dev,$_GET['accSetup'],$stage,
                    $this->mySQL->getVerID($_SESSION['setup'][$_GET['accSetup']][$this->labelSetup]));
                
                    foreach ($properties as $prop) {
                        
                        if ( isset($_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"]) 
                        && isset($_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev]) 
                        && in_array($prop,$_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev]))
                        {
                            if( !defined('CREATE_VER_EXECUTED') ){
                                if (!empty($originalSetup)) { // insert a new entry in VERSION and get ID
                                    $this->mySQL->insertVer($stamp,$label,$comment,0);
                                    $idVer = $this->mySQL->getVerID($stamp);
                                } else { // Update VERSION entry and set crurrent stamp to the new update stamp 
                                    $idVer = $this->mySQL->getVerID($_SESSION['setup'][$_GET['accSetup']][$this->labelSetup]);
                                    $this->mySQL->updateVer($idVer,$stamp,$label,$comment,0);
                                    $_SESSION['setup'][$_GET['accSetup']][$this->labelSetup] = $stamp;
                                }
                            
                            
                                define('CREATE_VER_EXECUTED', TRUE);
                            }
                        
                            $idProp = $this->mySQL->getAttrID('property',$class,$prop);
                            $idClass = $this->mySQL->getClassID(array("property$class","device$class"),$class,array($idProp,$idDev));

                        
                            $idMACHINE = $this->mySQL->getClassID(array("accMACHINE","stageMACHINE"),"MACHINE",array($idACC,$idStage));

                            $idClassMACHINE = $this->mySQL->getClassID(array("$class","MACHINE"),$class."_MACHINE",array($idClass,$idMACHINE));

                            $val = $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"];
                            $error = $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@error"];
                        
                            array_push($update,array($idClassMACHINE,$idVer,$val,$error));
                        
                        } 
                        else {  
                            foreach($originalSetup as $setup) {
                            
                            if ($setup[1]===$prop && $setup[0]===$dev && 
                            (!isset($_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev]) || 
                            (isset($_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev]) 
                            && in_array($prop,$_SESSION['setup'][$_GET['accSetup']]['newSetup'][$class][$stage][$dev])) ) )
                            { 
                                if( !defined('CREATE_VER_EXECUTED') ){
                                    $this->mySQL->insertVer($stamp,$label,$comment,0);
                                    $idVer = $this->mySQL->getVerID($stamp);
                                    
                                    define('CREATE_VER_EXECUTED', TRUE);
                                }

                                $idProp = $this->mySQL->getAttrID('property',$class,$prop);
                                $idClass = $this->mySQL->getClassID(array("property$class","device$class"),$class,array($idProp,$idDev));

                        
                                $idMACHINE = $this->mySQL->getClassID(array("accMACHINE","stageMACHINE"),"MACHINE",array($idACC,$idStage));

                                $idClassMACHINE = $this->mySQL->getClassID(array("$class","MACHINE"),$class."_MACHINE",array($idClass,$idMACHINE));

                                array_push($update,array($idClassMACHINE,$idVer,$setup[2],$setup[3]));                                
                            }           
                        }
                        
                    }
        
                }
            }
    }
        $this->mySQL->insertClassSetup($class."_MACHINE", $update);
        
        
    }
    
    
    unset($_SESSION['setup'][$_GET['accSetup']]);
    unset($_GET['accStage']);
    unset($CREATE_VER_EXECUTED);


}
    }

    public function createClassForm(&$layoutInfo,&$tabArray,&$printDevProps,$choosenCL) {
        
        $devices = array_merge(...$this->mySQL->getTabRows( "device$choosenCL", array('device') ));
        
        foreach($this->stages as $stage) {
            $CREATE_STAGE_LABEL = True;
            $printSetup = '';
            $hasProperty = false;
            $printSetup .= "<div style='float: left;width:100%;' >";

            $rows = array();
            $types = array();

            

            //---------- Devices checkbox
            $devicesList = $this->mySQL->getClassDev($choosenCL,$_GET['accSetup'], $stage);
            if (!empty($devicesList)) {
                array_push($tabArray,$stage);
                
                $deviceChoosen = (isset($_SESSION['setup'][$_GET['accSetup']]['OPEN'][$choosenCL][$stage])) ? 
                                $_SESSION['setup'][$_GET['accSetup']]['OPEN'][$choosenCL][$stage]:
                                '';
                
               
                $printSetup .= "<div class='setupDevs' >";                
                $printSetup .= "<div>";
                $printSetup .= "<form method='post'>";
                $printSetup .= $this->forms->checkbox3($devicesList, $deviceChoosen, "DEVICES",
                                array($choosenCL,$stage));
                $printSetup .= "</form>";
                $printSetup .= '</div>';
                

                //---------- Properties Multicheckbox
            if ($deviceChoosen !== '') {
                $dev = $deviceChoosen;
                $hasProperty = true;
                $setupList =  array_merge(...$this->mySQL->getClassPropPerDev($_SESSION['setup'][$_GET['accSetup']][$this->labelCL],
                $dev,
                $_GET['accSetup'],$stage));
                
                $originalSetup = $this->mySQL->getClassSetupPerDev($_SESSION['setup'][$_GET['accSetup']][$this->labelCL],
                                                            $dev,
                                                            $_GET['accSetup'],$stage,
                                                            $this->mySQL->getVerID($_SESSION['setup'][$_GET['accSetup']][$this->labelSetup]));
                
                $newSetupList = $_SESSION['setup'][$_GET['accSetup']]['newSetup'][$choosenCL][$stage][$dev];
                
            
                foreach($newSetupList as $prop) {
                    $EXISTS_DEV_PROP = False;
                    if (!in_array($prop,$setupList) || isset($_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"])) {
                        if ( isset($_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"]) ) {
                            array_push($rows,array($prop,
                            $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"],
                            $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@error"]));
                        } else {
                            array_push($rows,array($prop));
                        }
                        $attrID = $this->mySQL->getAttrID('property',$choosenCL,$prop);
                        array_push($types,$this->mySQL->getTypeFromProp($choosenCL,$attrID));
                    }
                    else {
                        foreach($originalSetup as $setup) {
                            if ($dev == $setup[0] && $prop == $setup[1]) {
                                array_push($rows,array($setup[1],$setup[2],$setup[3]));
                                $attrID = $this->mySQL->getAttrID('property',$choosenCL,$prop);
                                array_push($types,$this->mySQL->getTypeFromProp($choosenCL,$attrID));
                                $EXISTS_DEV_PROP = True;
                            }
                        }
                        if (!$EXISTS_DEV_PROP) {
                            array_push($rows,array($prop));
                            $attrID = $this->mySQL->getAttrID('property',$choosenCL,$prop);
                            array_push($types,$this->mySQL->getTypeFromProp($choosenCL,$attrID));
                        }
                        
                    }
                    
                }

                $printSetup .= "<div>";
                $printSetup .= "<form method='post'>";
                $printSetup .= $this->forms->checkMultipleBox4($setupList,
                                                        $newSetupList,
                                                        "PROPERTIES",array($choosenCL,$stage,$dev));
                $printSetup .= "</form>";
                $printSetup .= '</div>';
                

          
            $comment = (isset($_SESSION['setup'][$_GET['accSetup']]['comment'][$choosenCL][$stage][$dev]))?
                        $_SESSION['setup'][$_GET['accSetup']]['comment'][$choosenCL][$stage][$dev]: '';
            }

            }
            
            

            $printSetup .= "</div>"; 

            if (!empty($rows)) {
                $this->tab->setLabels(array('PROPERTY','value','error'));
                $this->tab->setRows2($rows,$types);
                
                $printSetup .=  "<div class='setupTab' >".
                        $this->tab->printEditTab(array(false,true,true),$stage,$deviceChoosen,true,$comment);
                $printSetup .=  "</div>"; 
                }

           $printSetup .= "</div>";
            
            $layoutInfo[$stage] .= $printSetup;
            $printDevProps .=  $printSetup . "</br>";

            
            foreach ($devicesList as $device) {
                $rowsT = array();

                $dev = $device;
                
                $originalSetup = $this->mySQL->getClassSetupPerDev($_SESSION['setup'][$_GET['accSetup']][$this->labelCL],
                                                            $dev,
                                                            $_GET['accSetup'],$stage,
                                                            $this->mySQL->getVerID($_SESSION['setup'][$_GET['accSetup']][$this->labelSetup]));
                
                $newSetupList = (isset($_SESSION['setup'][$_GET['accSetup']]['newSetup'][$choosenCL][$stage][$dev]))?
                $_SESSION['setup'][$_GET['accSetup']]['newSetup'][$choosenCL][$stage][$dev]: array();
                
                

                foreach($originalSetup as $setup) {
                    if (isset($_SESSION['setup'][$_GET['accSetup']][$stage ."@". $setup[0] . "@" . $setup[1] . "@value"]) ) {
                        array_push($rowsT,array($setup[1],
                        $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $setup[0] . "@" . $setup[1] . "@value"],
                        $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $setup[0] . "@" . $setup[1] . "@error"]));
                    } else if (in_array($setup[1],$newSetupList) || !isset($_SESSION['setup'][$_GET['accSetup']]['newSetup'][$choosenCL][$stage][$dev])) {
                        array_push($rowsT,array($setup[1],$setup[2],$setup[3]));
                        $attrID = $this->mySQL->getAttrID('property',$choosenCL,$setup[1]);
                    }
                    $newSetupList = array_diff($newSetupList,array($setup[1]));
                }

                foreach($newSetupList as $prop) {
                    if (isset($_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"]) ) {
                     array_push($rowsT,array($prop,
                        $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"],
                        $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@error"]));
                    }
                }
                

                if (!empty($rowsT)) {
                    if( $CREATE_STAGE_LABEL ){
                        $label = htmlentities($stage);
                        $layoutInfo['OVERVIEW'] .= "<div class='overview'>
                        <div class='overview_$label'>$label</div></br>";
                        $CREATE_STAGE_LABEL = False;
                    }

                    $this->tab->setLabels(array('PROPERTY','value','error'));
                    $this->tab->setRows($rowsT);
                    
                
                   $layoutInfo['OVERVIEW'] .= $this->tab->printEditTab(array(false,false,false),'NOupdate',$device);
                }
          
               
            }
            
            if (!$CREATE_STAGE_LABEL) $layoutInfo['OVERVIEW'] .= "</div>";
            
        }

        unset($_SESSION['setup'][$_GET['accSetup']]['delrows']);
    }

    public function createSetupLayout(&$layoutInfo) {
        $layoutInfo['OVERVIEW'] = '';
        $comment = json_decode($this->mySQL->getVerComment($_SESSION['setup'][$_GET['accSetup']][$this->labelSetup]),true);
       
        $updatedFrom = $comment['Updated from'];
        unset($comment['Updated from']);
        $updatedBy = $comment['Updated by'];
        unset($comment['Updated by']);

       
        
        $printComment = '';
        $printComment .= "<div class='summary'>";
        $printComment .= "<div><b>Updated:</b> &ensp;  <b>on</b> ".$_SESSION['setup'][$_GET['accSetup']][$this->labelSetup]
        ." &ensp;  <b>from</b> $updatedFrom &ensp; <b>by</b> $updatedBy</div><br>";
        if (is_array($comment)) {
            $printComment .= "<div><b>Comments:</b> </div><br>";
            foreach (array_keys($comment) as $class) { 
                foreach (array_keys($comment[$class]) as $stage) {
                    foreach (array_keys($comment[$class][$stage]) as $dev) {
                        $printComment .= "<div style='width:35%; float: left; text-align: right; padding-right:2%;'>
                                        $class  &ensp; -- &ensp; $stage  &ensp; -- &ensp; $dev:</div>";
                        $printComment .= "<div style='width:65%; float: left;'>". $comment[$class][$stage][$dev] ."</div><br>";
                    }
                } 
                   
            }
        }
        $printComment .= "</div>";
       

        
            foreach($this->stages as $stage) {
                $buttons = '';
                $overview = '';
                //array_push($tabArray,$stage); 
                $label = htmlentities($stage);
                            
               // $layoutInfo['OVERVIEW'] .= "<div class='overviewSetup'>
               //             <div class='overview_$label'>$label</div></br>";
                
                

                //$filepath= "../uploads/images/fig.png"; 
                //$layoutInfo['OVERVIEW'] .=  "<img src='$filepath' class='image'>";

                $STAGE_LABEL = true;
                foreach ($this->classList as $choosenCL) {
                    
                    $CLASS_LABEL = true;
                $devicesList = $this->mySQL->getClassDev($choosenCL,$_GET['accSetup'], $stage);

                
                
                foreach ($devicesList as $dev) {
                    
                    $originalSetup = $this->mySQL->getClassSetupPerDev($choosenCL,
                                            $dev,
                                            $_GET['accSetup'],$stage,
                                            $this->mySQL->getVerID($_SESSION['setup'][$_GET['accSetup']][$this->labelSetup]));
                    $rowsSetup = array();
                    foreach($originalSetup as $setup) {
                        $dev = $setup[0];
                        $prop = $setup[1];
                        $val = $setup[2];
                        $error = $setup[3];
                        if (isset($_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"])) {
                            $val = $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@value"];
                            $error = $_SESSION['setup'][$_GET['accSetup']][$stage ."@". $dev . "@" . $prop . "@error"];
                        } 
                        array_push($rowsSetup,array($prop,$val,$error));
                        
                    }
                    
                    if (!empty($rowsSetup)) {
                        
                        if ($CLASS_LABEL) {
                            $buttons .= '<button class="classesSetup" id="button_'. $stage . $choosenCL 
                                        . '" onclick="hideDiv(\''.$stage.$choosenCL.'\')">'.
                            "<label > $choosenCL </label>".
                            '</button>';
                            $overview .= "<div class='hideDiv' id='$stage$choosenCL' > 
                            <div class='a'> $choosenCL </div></br></br>";
                            //$layoutInfo['OVERVIEW'] .= "<div class='hideDiv' id='$stage$choosenCL' >";

                            /*
                            $layoutInfo['OVERVIEW'] .= "<div class='class'>".
                            '<button id="classes" onclick="hideDiv(\''.$stage.$choosenCL.'\')">'.
                            "<label > $choosenCL </label>".
                            '</button>'.
                            "</div></br>". 
                            "<div class='hideDiv' id='$stage$choosenCL' >";
                            */
                       /*     $layoutInfo[$stage] .= "<div class='overviewAll'>".
                            '<button id="classes" onclick="hideDiv(\''.$stage.$choosenCL.'\')">'.
                            "<label > $choosenCL </label>".
                            '</button>'.
                            "</div>". 
                            "<div class='hideDiv' id='$stage$choosenCL' >";
                            */

                            $CLASS_LABEL = false;
                        }
                        $layoutInfo[$stage] .= "<div  class='overviewAllperClass'>";

                        $this->tab->setLabels(array('PROPERTY','value','error'));
                        $this->tab->setRows($rowsSetup);
                        
                        //$layoutInfo['OVERVIEW'] .= $this->tab->printEditTab2($dev);
                        $overview .= $this->tab->printEditTab2($dev);

                        //$layoutInfo[$stage] .= $this->tab->printEditTab2($dev);
                        if (isset($comment[$choosenCL][$stage][$dev])) {
                           // $layoutInfo['OVERVIEW'] .= "<div class='comment'> <b>Comment: </b>".
                           //                         $comment[$choosenCL][$stage][$dev]."</div></br>";
                            $overview .= "<div class='comment'> <b>Comment: </b>".
                                                    $comment[$choosenCL][$stage][$dev]."</div>";
                            //$layoutInfo[$stage] .= "<div class='comment'> <b>Comment: </b>".
                            //$comment[$choosenCL][$stage][$dev]."</div></br>";
                        }
                        
                        //$layoutInfo[$stage] .= "</div>";
                       
                    } else {
                       
                    }
                    
                
                }
                //$layoutInfo['Setup'] .= "</br>";
                if (!$CLASS_LABEL) {
                    //$layoutInfo['OVERVIEW'] .= "</div></br>";
                    $overview .= "</div>";
                    //$layoutInfo[$stage] .= "</div></br>";
                    
                }
                
            }
            //$layoutInfo['OVERVIEW'] .= '</div>';

            $layoutInfo['OVERVIEW'] .= "<div class='overviewSetup'>
            <div class='overview_$label'> $label </div> 
            <div class='summary'>
            <div class='overview_buttons'>$buttons" . " </div></div>" . $overview .  '</div>';
            
        }
        $layoutInfo['OVERVIEW'] = $printComment . $layoutInfo['OVERVIEW'];
    }


}