<?php

include __DIR__ . '/../includes/autoloader.php';
$bpmlist = json_decode(file_get_contents("../data/bpm_list_R3.json"), true);
    

function updateForm() {
    foreach($_POST as $p_key => $p_value)
    { $_SESSION[$p_key] = $p_value; }
    //print_r($_SESSION);
}

function checkedOld($name,$value){
    if (isset($name) && $name==$value) return "checked";
}


function formEntry($entryArray,$entryLabel,$label) {
    $header = htmlentities($entryLabel);
    $name = $label."_".str_replace("[", "",str_replace("]", "", $header));
    
    $value = $entryArray['value'];
    $defaultValue = "";

    echo "<div class = 'my_form'>";
    if ($entryArray['inputForm'] === 'ON/OFF') {
        $var_radio = explode('/',$entryArray['inputForm']);
        echo "<label for='".$name."' >".$header.":  ".
        "<input type='radio' name='".$name."' value='Yes' ".checkedOld($value,true).">".
        htmlentities($var_radio[0]).
        "<input type='radio' name='".$name."' value='No' ".checkedOld($value,false).">".
        htmlentities($var_radio[1]).
        "</label></br>";
    } else if ($entryArray['inputForm'] === 'Int') {
        if ($value !== 'FAILED') $defaultValue = "value=".htmlentities($value);

        echo "<label for='".$name."' >".$header.":  ".
        "<input type='number' id='".$name."' name='".$name."'  maxlength='4' size=6 ".
        $defaultValue.
        " min='".htmlentities($entryArray['min'])."' max='".htmlentities($entryArray['max'])."'></br>";
    } else if ($entryArray["inputForm"] === 'Float') {
        if ($value !== 'FAILED') $defaultValue = "value=".htmlentities(round($value,$entryArray['digits']));
    
        echo "<label for='".$name."' >".$header.":  ".
        "<input type='number' id='".$name."' name='".$name."' ".
        $defaultValue." maxlength='4' size=8  step='1e-".
        htmlentities($entryArray['digits'])."'  required></br>";
    } else if ($entryArray["inputForm"] === 'String') {
        if ($value !== 'FAILED') $defaultValue = "value=".htmlentities($value);

        echo "<label for='".$name."' >".$header.":  ".
        "<input type='text' id='".$name."' name='".$name."' style='direction: ltr;' maxlength='30'".
        $defaultValue."></br>";
    } else if ($entryArray["inputForm"] === 'Array_String') {
        echo "<label for='".$name."'  >".$header.":  ";
        echo "<div class='checkboxes' id='".str_replace(" ", "",$name)."'>";
        if ($label === 'BPM'|| $entryLabel === 'Sensor Names') {
            global $bpmlist;
            foreach ($bpmlist as $bpm) {
                $is_checked = '';
                if (array_search($bpm,$value,true)) {
                    $is_checked = 'checked';
                }
                echo "<label class='container'>".htmlentities($bpm)."
                        <input type='checkbox' name='".$name."[]' ".$is_checked." />
                        <span class='checkmark'></span>
                    </label>";
            }
        }
        

        echo "</div></label>";
    } 

    echo "</div></br>";

}

/*-----------------------------------------------------------------------------------
  formBlock :  
-----------------------------------------------------------------------------------*/
function inputFormBox($persubDataArray,$label) {
    echo "<spam class='section'>".htmlentities($label)."</spam></br>";
    echo "<div style='padding:0.2em;margin-top:0px;background-color:#02a7fa;height:0px;'></div>";
    echo "<div class='my_section2'></br>";
    
    $entryArrayLabels = array_keys($persubDataArray);
    foreach ($entryArrayLabels as $entryLabel) {
        formEntry($persubDataArray[$entryLabel],$entryLabel,$label);
    }
    echo "</div>";
    echo "</br></br></br>";
}

function inputFormTab($fullDataArray,$tabContent) {
    foreach ($tabContent as $item) {
        $subDataArray = $fullDataArray[$item];
        $subDataLabels = array_keys($subDataArray);
        foreach ( $subDataLabels as $label) {
            inputFormBox($subDataArray[$label],$label);
        }
    }
}






function compare($var,$setting) {
    if ($setting["inputForm"] == 'radio' || $setting["inputForm"] == 'text' || $setting["inputForm"] == 'Int') {
        return $var['value']==$setting['value'];     
    } else if ($setting["inputForm"] == 'Float') {
        $is_good = ($setting['value']*(1-$setting['lowerErrorGood']) <= $var['value']) && 
        ($var['value'] <= $setting['value']*(1+$setting['upperErrorGood']));
        if ($is_good) {
            return $is_good;
        } else {
            $is_fair = ($setting['value']*(1-$setting['lowerErrorFair']) <= $var['value']) && 
        ($var['value'] <= $setting['value']*(1+$setting['upperErrorFair']));
            return $is_fair/2;
        }       
    }     
}

function get_match($value){
    if ($value == 1) return "class='fa fa-check-circle good'";
    else if ($value == 0.5) return "class='fa fa-exclamation-circle fair'";
    else if ($value == 0) return "class='fa fa-times-circle bad'";
}

function validateRow($entryArray,$entryLabel,$setupEntryArray) {
    $row = array($entryLabel,$entryArray['value'],$setupEntryArray['value'],
    "<i ".get_match(compare($entryArray,$setupEntryArray))."></i>");
    return $row;

}


//include('../classes/prettyTab.php');



function validateBox($persubDataArray,$label,$persubSetupArray) {
   
    $rows = array();
    $entryArrayLabels = array_keys($persubDataArray);
    foreach ($entryArrayLabels as $entryLabel) {
        array_push($rows,validateRow($persubDataArray[$entryLabel],$entryLabel,$persubSetupArray[$entryLabel]));
    }
    
    $tab = new prettyTab();
    $tab->setRows($rows);
    $tab->setLabels(array($label,"ONLINE","SETUP","MATCH"));
    $tab->printTabComment($label); 
    $tab = null;
    
}

function validateFormTab($fullDataArray,$setupDataArray,$tabContent) {
    foreach ($tabContent as $item) {
        $subDataArray = $fullDataArray[$item];
        $subDataLabels = array_keys($subDataArray);
        foreach ( $subDataLabels as $label) {
            validateBox($subDataArray[$label],$label,$setupDataArray[$item][$label]);
        }
    }
}



function createInputTabs($list_of_tab) {
    echo "<ul>";
    for ($i=1; $i < sizeof($list_of_tab)+1;$i++) {
        echo "<li class=".is_selected($i)." ><a href='form.html.php?tab=".$i."'>".htmlentities($list_of_tab[$i-1])."</a>";
        if($_GET['tab']==$i) {
            echo "<button class='remove_single_button' type='submit' name='tab".$_GET['tab']."'>
                    <i class='fa fa-eject' style='font-size:18px;'></i>
                 </button>
                 <button class='run_single_button' type='submit'>
                    <i class='fa fa-repeat' style='font-size:18px;'></i>
                 </button>
                 <button class='submit_single_button' type='submit'>
                 <i class='fa fa-share' style='font-size:18px;'></i>
                 </button>";
        }
    }
    echo "</ul>";        
}



function is_selected($value){
    if ($_GET['tab']==$value) return "selected";
}


?>



