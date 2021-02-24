<?php

class validation {

   
    public function __construct($input) {
        $this->mySQL = $input['mySQL'];
        $this->tab = $input['table'];
        $this->forms = $input['forms'];
        $this->labelSetup = $input['Setup label'];
        $this->labelValidation = $input['Validation label'];
        $this->labelCL = $input['Class label'];
        $this->stages = $input['stages'];
        $this->classList = $input['Classes List'];

        $this->descriptions = array();
        $this->loadDescriptions();


    }
   
    function __destruct() {
       
    }

    public function  SESSION_newValidation($labelValidation) {
            $_SESSION['validation'][$_GET['acc']]["new$labelValidation"] = $_POST["new$labelValidation"];
            header("Location: validation.html.php?acc=".$_GET['acc']. "&stage=".$_GET['stage']);
            return;
    }

    public function SESSION_setupStamp($labelSetup) {
            $_SESSION['validation'][$_GET['acc']]['setup'] = $_POST[$labelSetup];
            header("Location: validation.html.php?acc=".$_GET['acc']. "&stage=".$_GET['stage']);
            return;
    }

    public function SESSION_validationStamp($labelValidation) {
            $_SESSION['validation'][$_GET['acc']]['validation'] = $_POST[$labelValidation];
            $_SESSION['validation'][$_GET['acc']]['comment'] = json_decode($this->mySQL->getVerComment($_POST[$labelValidation]),true);
            header("Location: validation.html.php?acc=".$_GET['acc']. "&stage=".$_GET['stage']);
            return;
    }



    

    public function loadJSON() {
            $data = json_decode(file_get_contents($_FILES["loadFile"]['tmp_name']),true);
        
            foreach (array_keys($data) as $class) {
                unset($_SESSION['validation'][$_GET['acc']]['newValidation'][$class]);
                foreach ($data[$class]['data'] as $settings) {
                    $prop = $settings[0];
                    $dev = $settings[1];
                    $stage = $settings[2];
                    $acc = $settings[3];
                    $val = $settings[4];
        
                    if ($acc == $_GET['acc'] && $stage == $_POST['loadFileStage']) {
                        $_SESSION['validation'][$_GET['acc']][$stage][$class][$dev][$prop] = $val;
                    }
        
                }
            }
        
            header("Location: validation.html.php?acc=".$_GET['acc']. "&stage=".$_GET['stage']);
            return;
        
    }

    public function loadDescriptions() {
        $this->descriptions = yaml_parse_file('../BackBone/descriptions'.$_GET['acc'].'.yaml');
        return;
    
}

    public function SESSION_updateValidation($labelValidation) {
            $_SESSION['validation'][$_GET['acc']]["update$labelValidation"] = $_POST["update$labelValidation"];
            header("Location: validation.html.php?acc=".$_GET['acc']);
            return;
    }

    public function SESSION_updateValInSession() {
        $keys = array_keys($_POST);
        array_pop($keys); //remove the "Update_Validation"
        foreach($keys as $key) {
            if ($_POST[$key] !== '') {
                $labels = explode('@',$key);
                $stage = $labels[0];
                $class = $labels[1];
                $dev = $labels[2];
                $prop = $labels[3];
                $_SESSION['validation'][$_GET['acc']][$stage][$class][$dev][$prop] = 
                            (is_array($_POST[$key]))? implode(',',array_keys($_POST[$key])): $_POST[$key];
               
            }
        }
        
            header("Location: validation.html.php?acc=".$_GET['acc']. "&stage=".$_GET['stage']);
            return;
   
    }

    public function SESSION_updateComment() {
            $keys = array_keys($_POST);
            array_pop($keys); //remove the "comment
            foreach($keys as $key) {
                if ($_POST[$key] !== '') {
                    $labels = explode('@',$key);
            
                    $stage = $labels[0];
                    $class = $labels[1];
                    $dev = $labels[2];
            
                    $_SESSION['validation'][$_GET['acc']]['comment'][$class][$stage][$dev] = $_POST[$key];
                }
        
            }
            header("Location: validation.html.php?acc=".$_GET['acc']."&stage=".$_GET['stage']);
            return;
        
    }

    public function newValidation() {
            $arrayComment['Updated from']="New empty setup";
            $arrayComment['Updated by']=$_SESSION['user'];

            $comment = json_encode($arrayComment);
            $label = $_COOKIE['newLabel'];
            $stamp = date("Y/m/d H:i:s");
            $this->mySQL->insertVer($stamp,$label,$comment,0);
            $idVer = $this->mySQL->getVerID($stamp);


        
            //-------- Create GOST class setup
        
            $idACC = $this->mySQL->getAttrID('acc',"MACHINE",$_GET['acc']);
            $idMACHINE = $this->mySQL->getClassID(array("accMACHINE","stageMACHINE"),"MACHINE",array($idACC,1));
            $idClassMACHINE = $this->mySQL->getClassID(array("GHOST","MACHINE"),"GHOST_MAC",array(1,$idMACHINE));
            $this->mySQL->insertClassValidation("GHOST_MAC", array(array($idClassMACHINE,$idVer,'')));
        
            unset($_SESSION['validation'][$_GET['acc']]);    
    }

    public function validationsCheckBox($labelValidation,&$formSetup) {
        $choosenValidation = isset($_SESSION['validation'][$_GET['acc']]['validation']) ? 
        $_SESSION['validation'][$_GET['acc']]['validation'] : ''; 
        $IDs = $this->mySQL->getValidationVerIDs($_GET['acc']);
        $validationList = $this->mySQL->getVersionAttrByID("stamp",$IDs);
        $commentValList = $this->mySQL->getVersionAttrByID("label",$IDs);
        
        $formSetup = $this->forms->checkCU($labelValidation,$validationList,$commentValList,$choosenValidation);

        if ($choosenValidation !== '') {
            $idVal = $this->mySQL->getVerID($choosenValidation);
            if ($this->mySQL->getSetupMatch($idVal) == 0 ) { // no setup matched
                $formSetup = $this->forms->check($labelValidation,$validationList,$commentValList,$choosenValidation);
            }
        }
        
        
        $formSetup .= '<div style="margin-top: 7em"></div>';
    }

    public function validationsActions($labelValidation,&$formSetup) {
        $idVal = $this->mySQL->getVerID($_SESSION['validation'][$_GET['acc']]['validation']);
        if ($this->mySQL->getSetupMatch($idVal) == 0 ) { // no setup matched
            $choosenSetup = isset($_SESSION['validation'][$_GET['acc']]['setup']) ? 
            $_SESSION['validation'][$_GET['acc']]['setup'] : ''; 
            $IDs = $this->mySQL->getSetupVerIDs($_GET['acc']);
            $setupList = $this->mySQL->getVersionAttrByID("stamp",$IDs);
            $commentSetupList = $this->mySQL->getVersionAttrByID("label",$IDs);

            $formSetup .= $this->forms->checkSetup($labelValidation,$setupList,$commentSetupList,$choosenSetup);

        } else {
        //    $formSetup .= $this->forms->fileLoad($this->labelCL);
        }
    }

public function updateValidation() {
        $stamp = $_SESSION['validation'][$_GET['acc']]['validation'];
        $idVal = $this->mySQL->getVerID($stamp);
        $idSetup = (isset($_SESSION['validation'][$_GET['acc']]['setup']))? 
                    $this->mySQL->getVerID($_SESSION['validation'][$_GET['acc']]['setup']):
                    $this->mySQL->getSetupMatch($idVal);
        
       // $commentVal = json_decode($this->mySQL->getVerComment($stamp),true);

        
        $arrayComment = isset($_SESSION['validation'][$_GET['acc']]['comment'])?
                        $_SESSION['validation'][$_GET['acc']]['comment']: '';
        
        $arrayComment['Setup used:'] = $idSetup;
        $arrayComment['Validated by'] = $_SESSION['user'];

        $comment = json_encode($arrayComment);


        $label = ($_COOKIE['newLabel']!='')? $_COOKIE['newLabel']: $this->mySQL->getVerLabel($idVal);


        $idACC = $this->mySQL->getAttrID('acc',"MACHINE",$_GET['acc']);

        

    foreach ($this->classList as $class) {
        $update = array();
        $devices = array_merge(...$this->mySQL->getTabRows( 'device'.$class, array('device') ));
    
        foreach ($devices as $dev) {
            $idDev = $this->mySQL->getAttrID('device',$class,$dev);
            foreach ($this->stages as $stage) {
                $idStage = $this->mySQL->getAttrID('stage',"MACHINE",$stage);


                $originalSetup = $this->mySQL->getClassSetupPerDev($class,
                                            $dev,
                                            $_GET['acc'],$stage,
                                            $idSetup);
                
              foreach($originalSetup as $setup) {
                  $prop = $setup[1];
                  
                    
                    $idVer = $this->mySQL->getVerID($stamp);
                    $this->mySQL->updateVer($idVer,$stamp,$label,$comment,$idSetup);
                     
                    $idProp = $this->mySQL->getAttrID('property',$class,$prop);
                    $idClass = $this->mySQL->getClassID(array("property$class","device$class"),$class,array($idProp,$idDev));

                    $idMACHINE = $this->mySQL->getClassID(array("accMACHINE","stageMACHINE"),"MACHINE",array($idACC,$idStage));

                    $idClassMACHINE = $this->mySQL->getClassID(array("$class","MACHINE"),$class."_MACHINE",array($idClass,$idMACHINE));

                    
                                        

                    if ( isset($_SESSION['validation'][$_GET['acc']][$stage][$class][$dev][$prop]) )
                    {
                        $validationValue = $_SESSION['validation'][$_GET['acc']][$stage][$class][$dev][$prop];

                        array_push($update,array($idClassMACHINE,$idVer,$validationValue));
                    
                    } else {
                        $originalValidation = $this->mySQL->getValidationSetupPerProp($class,$prop,
                                        $dev,
                                        $_GET['acc'],$stage,
                                        $idSetup,$idVal);
                        $validationValue = (!empty($originalValidation))? $originalValidation[0]: '""';
                        array_push($update,array($idClassMACHINE,$idVer,$validationValue));
                    }
                            
                    }
                    
                }
    
            }
            
            if ( !empty($update) ) {
                if ( !$this->mySQL->existsValidation($class,$idVer) ) {
                    $this->mySQL->insertClassValidation($class."_MACHINE", $update);
                } else {
                    $this->mySQL->updateClassValidation($class."_MACHINE", $update);
                }
            }
            
            
        }

    
    
    
    unset($_SESSION['validation'][$_GET['acc']]);
    unset($CREATE_VAL_EXECUTED);

}


public function createValidationForm(&$layoutInfo,&$tabArray) {
    if (isset($_SESSION['validation'][$_GET['acc']]['validation'])) {
        $idVal = $this->mySQL->getVerID($_SESSION['validation'][$_GET['acc']]['validation']);
        $setupID = $this->mySQL->getSetupMatch($idVal);
        //$commentVal = json_decode($this->mySQL->getVerComment($_SESSION['validation'][$_GET['acc']]['validation']),true);
        $commentVal = isset($_SESSION['validation'][$_GET['acc']]['comment'])?
        $_SESSION['validation'][$_GET['acc']]['comment']: '';

        if ($setupID != 0 ) {
            $setupStamp = $this->mySQL->getVerStamp($setupID);
        
        } else if (isset($_SESSION['validation'][$_GET['acc']]['setup'])) {
            $setupStamp = $_SESSION['validation'][$_GET['acc']]['setup'];
            $setupID = $this->mySQL->getVerID($setupStamp);
        }

    if (isset($setupStamp)) {
        $layoutInfo['OVERVIEW'] = '';
    $comment = json_decode($this->mySQL->getVerComment($setupStamp),true);
    
   
    $updatedFrom = $comment['Updated from'];
    unset($comment['Updated from']);
    $updatedBy = $comment['Updated by'];
    unset($comment['Updated by']);

   
    
    $printComment = '';
    $printComment .= "<div class='summary'>";
    $printComment .= "<div><b>Updated:</b> &ensp;  <b>on</b> ".$setupStamp
    ." &ensp;  <b>from</b> $updatedFrom &ensp; <b>by</b> $updatedBy</div><br>";
    $printComment .= "</div>";
   

    
        foreach($this->stages as $stage) { 
            //$summary = array();
            if ($this->mySQL->getSetupMatch($idVal) != 0 ) array_push($tabArray,$stage); 
            $label = htmlentities($stage);
                        
            $layoutInfo['OVERVIEW'] .= "<div class='overviewValidation'>
                        <div class='overview_$label'>$label</div>
                        <div class='summary'>";

            $STAGE_LABEL = true;
            foreach ($this->classList as $choosenCL) {
               // $summary[$choosenCL] = array();

                $CLASS_LABEL = true;
            $devicesList = $this->mySQL->getClassDev($choosenCL,$_GET['acc'], $stage);

            
            
            foreach ($devicesList as $dev) {
                $summary[$stage][$choosenCL][$dev] = array('correct' => 0,'fair' => 0, 'wrong' => 0, 'missing' => 0);
                $originalSetup = $this->mySQL->getClassSetupPerDev($choosenCL,
                                        $dev,
                                        $_GET['acc'],$stage,
                                        $setupID);

                
                $rows = array();
                $types = array();
                foreach($originalSetup as $setup) {
                    $prop = $setup[1];

                    $originalValidation = $this->mySQL->getValidationSetupPerProp($choosenCL,$prop,
                                        $dev,
                                        $_GET['acc'],$stage,
                                        $setupID,$idVal);
               
                    if ( isset($_SESSION['validation'][$_GET['acc']][$stage][$choosenCL][$dev][$prop]) ) {
                        $validationValue = $_SESSION['validation'][$_GET['acc']][$stage][$choosenCL][$dev][$prop]; 
                    } else {
                        $validationValue = (!empty($originalValidation))? $originalValidation[0]: '""';
                    }

                    array_push($rows,array($prop,$validationValue,$setup[2],$setup[3]));
                    
                    $attrID = $this->mySQL->getAttrID('property',$choosenCL,$prop);
                    array_push($types,$this->mySQL->getTypeFromProp($choosenCL,$attrID));
                }

                if (!empty($rows)) {
                    if ($CLASS_LABEL) {
                        //$layoutInfo['OVERVIEW'] .= "<div class='class'>$choosenCL</div></br>";
                       // $layoutInfo['OVERVIEW'] .= "<div class='class'>".
                        //    '<button id="classes" onclick="hideDiv(\''.$stage.$choosenCL.'\')">'.
                        //    "<label > $choosenCL </label>".
                        //    '</button>'.
                        //    "</div></br>". 
                        //    "<div class='hideDiv' id='$stage$choosenCL' >";

                        $layoutInfo[$stage] .= "<div  class='overviewAll'>
                            <div class='class'>$choosenCL</div></br>";
                        if (isset($this->descriptions[$choosenCL][$stage])) {
                            $layoutInfo[$stage] .= "<div class='info' >";
                            foreach (array_keys($this->descriptions[$choosenCL][$stage]) as $item) {
                                $layoutInfo[$stage] .= "<b>".htmlentities($item) .": </b>" . 
                                                        ($this->descriptions[$choosenCL][$stage][$item]) 
                                                        . "</br>";
                            }
                            $layoutInfo[$stage] .= "</div>";
                        }
                               
                        $CLASS_LABEL = false;
                    }
                    $layoutInfo[$stage] .= "<div  class='overviewAllperClass'>";

                    $this->tab->setLabels(array('PROPERTY','value','error'));
                    $this->tab->setRows2($rows,$types);
                    
                    $commentValDev = '';
                    if (isset($_SESSION['validation'][$_GET['acc']]['comment'][$choosenCL][$stage][$dev])) {
                        $commentValDev = $_SESSION['validation'][$_GET['acc']]['comment'][$choosenCL][$stage][$dev];
                    } else if (isset($commentVal['comment'][$choosenCL][$stage][$dev])) {
                        $commentValDev = $commentVal['comment'][$choosenCL][$stage][$dev];
                    }

                   
                    //$layoutInfo[$stage] .= $this->tab->validationTable($stage.'@'.$choosenCL,$dev,$commentValDev,$summary[$stage][$choosenCL][$dev]);
                    $layoutInfo[$stage] .= $this->tab->validationTable(array(false,true,false),$stage,$choosenCL,$dev,$summary[$stage][$choosenCL][$dev],$commentValDev);
                    
                    
                    $layoutInfo[$stage] .= "</div>";
                } else {
                   
                }
            }
            if (!$CLASS_LABEL) {
                //$layoutInfo['OVERVIEW'] .= "</div></br>";
                $layoutInfo[$stage] .= "</div></br>";
            }
        }
        $VALIDATION_NOT_OK = False;
        foreach (array_keys($summary[$stage]) as $class) {
            $list = '';
            foreach (array_keys($summary[$stage][$class]) as $dev) {
                if ( $summary[$stage][$class][$dev]['fair'] != 0 || 
                     $summary[$stage][$class][$dev]['wrong'] != 0 ||
                     $summary[$stage][$class][$dev]['missing'] != 0) {
                        $list .=  "<div class='dev' >  
                                        <span style='font-size:40px;color: #245e94;'>&#8627;</span> $dev 
                                    </div>
                                    <div class='status'>";
                        if ($summary[$stage][$class][$dev]['fair'] != 0) {
                            $filepath = $this->tab->get_match(0.5);
                            $img = "<img src='$filepath' style='width: 40px; 
                                        display: block;float: left; padding-left:1em;
                                        margin-right:0.2em;'>";
                            //$list .= "<div >" . $img . $summary[$stage][$class][$dev]['fair']."</div>";
                            $list .= "<p>" . $img . $summary[$stage][$class][$dev]['fair'] . "</p>";
                        }   
                        if ($summary[$stage][$class][$dev]['wrong'] != 0) {
                            $filepath = $this->tab->get_match(0);
                            $img = "<img src='$filepath' style='width: 40px; 
                                        display: block;float: left; padding-left:1em; 
                                        margin-right:0.2em;'>";
                            //$list .=  "<div >". $img . $summary[$stage][$class][$dev]['wrong'] ."</div>";
                            $list .=  "<p>" .  $img . $summary[$stage][$class][$dev]['wrong'] . "</p>";;
                        }
                        if ($summary[$stage][$class][$dev]['missing'] != 0) {
                            $filepath = $this->tab->get_match(-1);
                            $img = "<img src='$filepath' style='width: 40px; 
                                        display: block;float: left; padding-left:1em;
                                        margin-right:0.2em;'>";
                            //$list .=  "<div >" . $img . $summary[$stage][$class][$dev]['missing']."</div>";
                            $list .=  "<p>" .  $img . $summary[$stage][$class][$dev]['missing'] . "</p>";; 
                        }
                        $list .= "</div>";
                            
                     }
                        
            }
            if ($list != '') {
                $layoutInfo['OVERVIEW'] .= "<div class='class'> $class </div>" . $list;
                $VALIDATION_NOT_OK = True;
            }

        }
        if (!$VALIDATION_NOT_OK) {
            $filepath = $this->tab->get_match(1);
            $img = "<img src='$filepath'>";
            $layoutInfo['OVERVIEW'] .= "<div class='image'> $img </div>";
        }
        $layoutInfo['OVERVIEW'] .= '</div>';
        if ($this->mySQL->getSetupMatch($idVal) != 0 ) $layoutInfo['OVERVIEW'] .= '<div class="loadButton">'. $this->forms->fileLoad($stage) . '</div>';
        $layoutInfo['OVERVIEW'] .= '</div>';

    }
    $layoutInfo['OVERVIEW'] = $printComment . $layoutInfo['OVERVIEW'];
    } 

}
    


    
    
}



}