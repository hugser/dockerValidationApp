<?php

class forms {

    function __construct() {

    }
    function __destruct() {

    }

    function checkboxes($fullList,$inputList,$entryLabel,$label) {
        $header = htmlentities($entryLabel);
        $name = $label."_".str_replace("[", "",str_replace("]", "", $header));

        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$name."'  >".$header.":  ";
        $checklist .= "<div class='checkboxes' id='".str_replace(" ", "",$name)."'>";
        foreach ($fullList as $item) {
            $is_checked = '';
            if (array_search($item,$inputList,true)) {
                    $is_checked = 'checked';
            }
            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='checkbox' name='".$name."[]' ".$is_checked." />
                <span class='checkmark'></span>
                </label>";
        }
        $checklist .= "</div></label>";
        $checklist .= "</div></br>";

        return $checklist;
    }
    function checkMultipleBox($fullList,$inputList,$label) {
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$label."'  > <b>".$label."</b>  ";
        $checklist .= "<div class='checkboxes' >";
        foreach ($fullList as $item) {
            $is_checked = '';
            if (array_search($item,$inputList,true)!==false) {
                    $is_checked = 'checked';
            }
            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='checkbox' name='".$label."[$item]' ".$is_checked." />
                <span class='checkmark'></span>
                </label>
                ";
        }
        $checklist .= "</div>";
        $checklist .= "</div></br>";

        return $checklist;
    }
    function checkMultipleBox3($fullList,$inputList,$label,$id) {
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$label."'  > <b>".$label."</b>  ";
        $checklist .= "<div class='checkboxes' >";
        foreach ($fullList as $item) {
            $is_checked = '';
            if (array_search($item,$inputList,true)!==false) {
                    $is_checked = 'checked';
            }
            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='checkbox' name='".$label."[$item]' ".$is_checked." onchange='this.form.submit();' />
                <span class='checkmark'></span>
                </label>
                <input type='hidden' name='newSetup_$id'>
                ";
        }
        $checklist .= "</div>";
        $checklist .= "</div></br>";

        return $checklist;
    }

    function checkMultipleBox4($fullList,$inputList,$name,$labels) {
        for ($i=0; $i<sizeof($labels); $i++) {
            if ($i==0 ) {
                $label = $labels[0];
                $finalname = $name."[".$labels[0]."]";
            } else {
                $label .= "[" . $labels[$i] . "]";
                $finalname .= "[" . $labels[$i] . "]";
            }
        }
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$name."'  > <b>".$name."</b>  ";
        $checklist .= "<div class='checkboxes' >";
        foreach ($fullList as $item) {
            $is_checked = '';
            if (array_search($item,$inputList,true)!==false) {
                    $is_checked = 'checked';
            }
            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='checkbox' name='".$label."[$item]' ".$is_checked." onchange='this.form.submit();' />
                <span class='checkmark'></span>
                <input type='hidden' name='$finalname'>
                </label>
                ";
        }
        $checklist .= "</div>";
        $checklist .= "</div></br>";

        return $checklist;
    }

    function checkMultipleBoxCOMMENT($fullList,$comment,$inputList,$label) {
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$label."'  > <b>".$label."</b>  ";
        $checklist .= "<div class='checkboxes' >";
        $i=0;
        foreach ($fullList as $item) {
            $is_checked = '';
            if (array_search($item,$inputList,true)!==false) {
                    $is_checked = 'checked';
            }
            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='checkbox' name='".$label."[$item]' ".$is_checked." />
                <span class='checkmark'></span>  </br>
                <span style='color: grey;'>".$comment[$i]."</span>
                </label>
                ";
            $i++;
        }
        $checklist .= "</div>";
        $checklist .= "</div></br>";

        return $checklist;
    }
    
    function checkMultipleBoxSmall($fullList,$inputList,$label) {
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$label."'  > <b>".$label."</b>  ";
        $checklist .= "<div class='checkboxesSmall' >";
        foreach ($fullList as $item) {
            $is_checked = '';
            if (array_search($item,$inputList,true)) {
                    $is_checked = 'checked';
            }
            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='checkbox' name='".$label."[$item]' ".$is_checked." />
                <span class='checkmark'></span>
                </label>
                ";
        }
        $checklist .= "</div>";
        $checklist .= "</div></br>";

        return $checklist;
    }
    function checkbox($fullList,$choice,$name) {
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$name."'  > <b>".$name."</b>  ";
        $checklist .= "<div class='checkboxes' id='".str_replace(" ", "",$name)."'>";
        foreach ($fullList as $item) {
            $is_checked = '';
            if ($item === $choice) {
                    $is_checked = 'checked';
            } 
            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='radio' name='$name' value ='$item"."'".$is_checked." onclick='this.form.submit();' />
                <span class='checkmark'></span>
                </label>";
        }
        $checklist .= "</div></label>";
        $checklist .= "</div></br>";

        return $checklist; 
    }
    function checkbox2($fullList,$choice,$name) {
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$name."'  > <b>".$name."</b>  ";
        $checklist .= "<div class='checkboxes' id='".str_replace(" ", "",$name)."'>";
        foreach ($fullList as $item) {
            $is_checked = '';
            if ($item === $choice) {
                    $is_checked = 'checked';
            } 
            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='radio' name='$name' value ='$item"."'".$is_checked." onchange='this.form.submit();' />
                <span class='checkmark'></span>
                </label>";
        }
        $checklist .= "</div></label>";
        $checklist .= "</div></br>";

        return $checklist; 
    }
    function checkboxCOMMENT($fullList,$fullComment,$choice,$name) {
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$name."'  > <b>".$name."</b>  ";
        $checklist .= "<div class='checkboxes' id='".str_replace(" ", "",$name)."'>";
        $i=0;
        foreach ($fullList as $item) {
            $is_checked = '';
            if ($item === $choice) {
                    $is_checked = 'checked';
            } 
            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='radio' name='$name' value ='$item"."'".$is_checked." onchange='this.form.submit();' />
                <span class='checkmark'></span> </br>
                <span style='color: grey;'>".$fullComment[$i]."</span>
                </label>";
            $i++;
        }
        $checklist .= "</div></label>";
        $checklist .= "</div></br>";

        return $checklist; 
    }

    function checkboxSearch($fullList,$fullComment,$choice,$name) {
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$name."'  > <b>".$name."</b>  ";
        $checklist .= "<div class='checkboxes' id='options'>";
        $i=0;
        foreach ($fullList as $item) {
            $is_checked = '';
            if ($item === $choice) {
                    $is_checked = 'checked';
            } 
            $checklist .= "<label class='container' id='64'>".htmlentities($item)."
                <input type='radio' for='64' name='$name' value ='$item"."'".$is_checked." onchange='this.form.submit();' />
                <span class='checkmark'></span> </br>
                <span style='color: grey;'>".$fullComment[$i]."</span>
                </label>";
            $i++;
        }
        $checklist .= "</div></label>";
        $checklist .= "</div></br>";

        return $checklist; 
    }

    function checkbox3($fullList,$choice,$name,$labels) {
        for ($i=0; $i<sizeof($labels); $i++) {
            if ($i==0 ) {
                $label = $labels[0];
                $finalname = $name."[".$labels[0]."]";
            } else {
                $label .= "[" . $labels[$i] . "]";
                $finalname .= "[" . $labels[$i] . "]";
            }
        }
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$name."'  > <b>".$name."</b>  ";
        $checklist .= "<div class='checkboxes' id='".str_replace(" ", "",$name)."'>";
        foreach ($fullList as $item) {
            $is_checked = '';
            if ($item === $choice) {
                    $is_checked = 'checked';
            } 
            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='radio' name='$label' value ='$item"."'".$is_checked."  onchange='this.form.submit();' />
                <span class='checkmark'></span>
                <input type='hidden' name='$name' >
                </label>";
        }
        $checklist .= "</div></label>";
        $checklist .= "</div></br>";

        return $checklist; 
    }

    function checkCRUD($name,$list,$choosen) {
        $form = '<form method="post">';
        $form .= $this->checkbox($list,$choosen,$name);
        $form .=  '</form>';
        $form .= '<form method="post">';
        $form .=  "<input type='text' id='new$name' name='new$name' style='direction: ltr;' 
                                    maxlength='50' size='28' required='required' pattern='[a-zA-Z0-9]+[a-zA-Z0-9 ]+'
                                    placeholder='Create new $name ...'>";
        $form .=  '</form>';
        
        $form .= '<form method="post">';
        $form .= "<input type='submit' name='rename$name' style='width: 50%;' value='Rename'>";
        $form .= "<input type='submit' name='del$name' style='width: 50%;' value='Delete'"; 
        if ($choosen !== '') $form .= "onclick='return confirmDelete()'";
        $form .= '>';
        $form .=  '</form>';
        
    
        return $form;
    }
    function adminSCD($name,$list,$choosen) {
        $form = '<form method="post">';
        $form .= $this->checkbox($list,$choosen,$name);
        $form .=  '</form>';
        $form .= '<form method="post">';
        $form .= '<input type="submit" name="new'.$name.'" id="new'.$name.'" style="float: left; width: 48%;" value="New '.$name.'"
                    onclick="return newLabel()">' . "<label for='new$name' >New</label>";
        $form .= "<input type='submit' id='delete$name' name='del$name' style='width: 50%;' value='Delete'"; 
        if ($choosen !== '') $form .= "onclick='return confirmDelete()'";
        $form .= '>' .  "<label for='delete$name' >Delete</label>";
        $form .=  '</form>';
        return $form;
    }

    function checkCU($name,$list,$commentList,$choosen) {
        $form = '<form method="post" >';
        $form .= $this->checkboxSearch($list,$commentList,$choosen,$name);
        $form .=  '</form>';

        $form .= '<form method="post">';
        $form .= "<input type='submit' id='submit' name='new$name'  value='New'
                    onclick='return newLabel();'>
                    <label for='submit' >New</label>";
        $form .= "<input type='submit' id='submit2' name='update$name' value='Update'
                    onclick='return updateLabel();'>
                    <label for='submit2' >Update</label>";
        $form .=  '</form>';
        return $form;
    }

    function checkSetup($name,$list,$commentList,$choosen) {
        $form = '<form method="post">';
        $form .= $this->checkboxSearch($list,$commentList,$choosen,'SETUP');
        $form .=  '</form>';

        $form .= '<form method="post">';
        $form .= "<input type='submit' id='submitSetup' name='update$name' value='Update' 
                    onclick='return updateLabel();'>
                    <label for='submitSetup' >Load Setup</label>";
        $form .=  '</form>';
        return $form;
    }

    function check($name,$list,$commentList,$choosen) {
        $form = '<form method="post">';
        $form .= $this->checkboxSearch($list,$commentList,$choosen,$name);
        $form .=  '</form>';
        return $form;
    }

    function checkLOAD($name,$list,$choosen) {
        $form = '<form method="post">';
        $form .= $this->checkbox($list,$choosen,$name);
        $form .=  '</form>';
        $form .= '<form action="" method="post" enctype="multipart/form-data">';
        $form .= "<input type='file' id='file' onchange='this.form.submit()' name='load$name' value='Load'>
        <label for='file' >Load Values</label>";
        $form .=  '</form>';
        return $form;
    }

    function fileLoad($stage) {
        $form = '<form action="" method="post" enctype="multipart/form-data">';
        $form .= "<input type='file' id='file$stage'
                    onchange='this.form.submit()' name='loadFile' value='Load'>
                    <label for='file$stage' >Load Online $stage Values</label>"; 
        $form .=  "<input type='hidden' name='loadFileStage' value='$stage'> </form>";
        return $form;
    }

    function checkCRUD2($name,$list,$commentList,$choosen) {
        $form = '<form method="post">';
        $form .= $this->checkboxCOMMENT($list,$commentList,$choosen,$name);
        $form .=  '</form>';
        $form .= '<form method="post">';
        $form .=  "<input type='text' id='new$name' name='new$name' style='direction: ltr;' 
                                    maxlength='50' size='28' required='required' pattern='[a-zA-Z0-9]+[a-zA-Z0-9 ]+'
                                    placeholder='Create new $name ...'>";
        $form .=  '</form>';
        
        $form .= '<form method="post">';
        $form .= "<input type='submit' name='rename$name' style='width: 50%;' value='Rename'>";
        $form .= "<input type='submit' name='del$name' style='width: 50%;' value='Delete'"; 
        if ($choosen !== '') $form .= "onclick='return confirmDelete()'";
        $form .= '>';
        $form .=  '</form>';
        
    
        return $form;
    }

    function buttonRDA($name,$choosen) {
        $form = '<form method="post">';
        $form .= "<input type='submit' name='rename$name' style='width: 50%;' value='Rename'>";
        $form .= "<input type='submit' name='del$name' style='width: 50%;' value='Delete'"; 
        if ($choosen !== '') $form .= "onclick='return confirmDelete()'";
        $form .= '>';
        $form .=  '</form>';
        $form .= '<form method="post">';
        $form .=  "<input type='text' id='new$name' name='new$name' style='direction: ltr;' 
                                    maxlength='50' size='28' required='required' pattern='[a-zA-Z0-9]+[a-zA-Z0-9 ]+'
                                    placeholder='Create new $name ...'>";
        $form .=  '</form>';
    
        return $form;
    }

    function buttonRDA2($name,$choosen) {
        $form = '<form method="post">';
        $form .= "<input type='submit' name='rename$name' style='width: 50%;' value='Rename'>";
        $form .= "<input type='submit' name='del$name' style='width: 50%;' value='Delete'"; 
        if ($choosen !== '') $form .= "onclick='return confirmDelete()'";
        $form .= '>';
        $form .=  '</form>';
        $form .= '<form method="post" onsubmit="return confirmProp();" >';
        $form .=  "<input type='text'  id='myId' id='new$name' name='new$name' style='direction: ltr;' 
                                    maxlength='50' size='28' required='required' pattern='[a-zA-Z0-9]+[a-zA-Z0-9 ]+'
                                    placeholder='Create new $name ...'>";
        $form .=  '</form>';
    
        return $form;
    }

    function checkMultiCRUD($name,$list,$choosen) {
        //$form = '<form method="post">';
        $form = $this->checkMultipleBox($list,$choosen,$name);
        //$form .=  '</form>';
        $form .= '<form method="post">';
        $form .= "<input type='submit' name='rename$name' style='width: 10em;' value='Rename'>";
        $form .= "<input type='submit' name='del$name' style='width: 10em;' value='Delete'"; 
        if ($choosen !== '') $form .= "onclick='return confirmDelete()'";
        $form .= '>';
        $form .=  '</form>';
        $form .= '<form method="post">';
        $form .=  "<input type='text' id='new$name' name='new$name' style='direction: ltr;' 
                                    maxlength='50' size='28' required='required' pattern='[a-zA-Z0-9]+[a-zA-Z0-9 ]+'
                                    placeholder='Create new $name ...'>";
        $form .=  '</form>';
    
        return $form;
    }

    function multiplecheckbox($fullList,$choices,$name) {
        $checklist = '';
        $checklist .= "<div class = 'my_form'>";
        $checklist .= "<label for='".$name."'  > <b>".$name."</b>  ";
        $checklist .= "<div class='checkboxes' id='".str_replace(" ", "",$name)."'>";
        foreach ($fullList as $item) {
            $is_checked = '';
            if (false !== array_search($item,$choices)) {
                $is_checked = 'checked';
            }

            $checklist .= "<label class='container'>".htmlentities($item)."
                <input type='checkbox' name='".$name."[$item]' ".$is_checked." onchange='this.form.submit();' />
                <span class='checkmark'></span>
                </label>";
        }
        $checklist .= "</div></label>";
        $checklist .= "</div></br>";

        return $checklist; 
    }

    function updateForm() {
        foreach($_POST as $p_key => $p_value)
        { $_SESSION[$p_key] = $p_value; }
        //print_r($_SESSION);
    }
    

    function radioEntry($label,$value) {
        $isOn = '';
        $isOff = '';

        if ($value) $isOn = 'checked';
        else $isOff = 'checked';

        $radioEntry = "<label for='$label'> ".
        "<input type='radio' name='$label' value='True' $isOn> ON
        <input type='radio' name='$label' value='False' $isOff> OFF
        </label></br>";

        return $radioEntry;
    }

    function numberEntry($label,$value,$min,$max) {
        $labelIDTemp = explode('@', $label);
        $labelID = end($labelIDTemp);
        if (gettype($value) === 'double') $type = "size=8 step='1e-".$this->precision($value)."' required";
        else $type = "size=6"; 

        $labelValue = $label.'@Value';
        $numberEntry = "<label for='$labelValue' >Value :  
        <input type='number' id='$labelValue' name='$labelValue'  maxlength='4'  value='$value'
        min='$min' max='$max' $type></label></br>";

        return $numberEntry;
    }

    function numberEntryFull($label,$value,$range,$error,$size) {

        $labelValue = $label.'@Value';
        $numberEntryFull = "<label for='$labelValue' >Value :  
            <input type='number' id='$labelValue' name='$labelValue' size='$size' step='1e-10' value='$value' required>
            </label></br>";
        
        $numberEntryFull .= $this->stringEntry($label.'@Range',$range,$size);
        if ($error!='NA') $numberEntryFull .= $this->stringEntry($label.'@Error',$error,$size);

        return $numberEntryFull;
    }

    function stringEntry($label,$value,$size) {
        $labelIDTemp = explode('@', $label);
        $labelID = end($labelIDTemp);
        $stringEntry = "<label for='$label' >$labelID :  
        <input type='text' id='$label' name='$label' style='direction: ltr;' maxlength='50' size='$size'
        value='$value'></label></br>";

        return $stringEntry;
    }

    function stringEntrySingle($label,$value,$size) {
        $stringEntry = "<label for='$label' >  
        <input type='text' id='$label' name='$label' style='direction: ltr;' maxlength='50' size='$size'
        value='$value'></label></br>";

        return $stringEntry;
    }
    
    function formEntry($label,$input,$range,$error) {
        $labelIDTemp = explode('@', $label);
        $labelID = end($labelIDTemp);

        $type = gettype($input);

        $formEntry = "<spam class='section'>".htmlentities($labelID)."</spam></br>";
        $formEntry .= "<div style='padding:0.2em;margin-top:0px;background-color:#02a7fa;height:0px;'></div>";
        $formEntry .= "<div class='my_section2'></br>";
        $formEntry .= "<div class = 'my_form'>";
        
        if ($type === 'boolean') $formEntry .= $this->radioEntry($label,$input);
        else if ($type === 'integer' || $type === 'double') $formEntry .= $this->numberEntryFull($label,$input,$range,$error,12); 
        else if ($type === 'string')  $formEntry .= $this->stringEntrySingle($label,$input,30);
       // else if (gettype($input) === 'array') 

        $formEntry .= "</div></br></div></br></br></br>";

        return $formEntry;
    }

    function convStrToType($input) {
        /*
            Gets a string and converts into: int, float, bool, str, array 
        */
        //convert input into an array
        $value = explode(',',str_replace(array('[', ']'), "", $input));
        if (sizeof($value) == 1) {
            $val = $value[0];
            if (is_numeric($val)) {
                return $val+0;
            } else {
                if ($val === 'False') return False;
                else if ($val === 'True') return True;
                else return $val;
            }
             
        } else {
            return $value;
        }
         
    }
    function precision($num)
    {
        $dotPos = strpos($num, '.');
        if ($dotPos > 0) {
            $places = substr($num,$dotPos + 1);
            return strlen($places);
        } else return 0;                    
    }

    function createFullFormEntries($allDevicesAttributes,$stage) {
        $fullFormEntry = '';
        $fullFormEntry .= "<spam class='section_black'>".htmlentities($stage)."</spam></br>";
        $fullFormEntry .= "<div style='padding:0.2em;margin-top:0px;background-color:black;style:dashed;height:0px;'></div>";
        $fullFormEntry .= "</br>";

        $classes = array_keys($allDevicesAttributes[$stage]);
        foreach ($classes as $class) {
            $devs = array_keys($allDevicesAttributes[$stage][$class]);
            foreach ($devs as $dev) {
                $data = $allDevicesAttributes[$stage][$class][$dev];
                if (sizeof($data)>0) {
                    $fullFormEntry .= "<spam class='section_red'>".htmlentities($dev)."</spam></br>";
                    $fullFormEntry .= "<div style='padding:0.2em;margin-top:0px;background-color:red;height:0px;'></div>";
                    $fullFormEntry .= "</br>";
                }

                foreach ($data as $item) {
                    $label = $stage.'@'.$class.'@'.$dev.'@'.$item[0];
                    $input = $this->convStrToType($item[1]);
                    $range = $item[3];
                    $error = $item[2];
                    $fullFormEntry .= $this->formEntry($label,$input,$range,$error);
                }
   
            }
        
        }
        return $fullFormEntry;
    }

}
/*   $fullFormEntry = '';
        foreach ($classes as $class) {
            $devs = $mySQL->allClassesDevicesList[$class];
            foreach ($devs as $dev) {
                $tab->setRows($mySQL->getAttributes("Ver400mA_02","COLD",$class,$dev));
                $data = $tab->getRows();

                if (sizeof($data)>0) {
                    $fullFormEntry .= "<spam class='section'>".htmlentities($dev)."</spam></br>";
                    $fullFormEntry .= "<div style='padding:0.2em;margin-top:0px;background-color:#02a7fa;height:0px;'></div>";
                    $fullFormEntry .= "</br>";
                }

                foreach ($data as $item) {
                    $label = $item[0];
                    $input = $forms->convStrToType($item[1]);
                    $range = $item[3];
                    $error = $item[2];
                    $fullFormEntry .= $forms->formEntry($label,$input,$range,$error);
                }
   
            }
        
        }
        return $fullFormEntry;
        */
?>

