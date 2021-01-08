<?php

class prettyTab {

    function __construct() {
    }
    function __destruct() {
        //print "Destroying " . __CLASS__ . "\n";
    }

    

    function setLabels($inputLabels) {
        $this->colLabels = $inputLabels;
    }
    function setRows($inputrows) {
        $this->rows = $inputrows;
    }
    function setNames($inputNames) {
        $this->names = $inputNames;
    }
    function setRows2($inputrows,$dataTypes) {
        $this->rows = $inputrows;
        $this->types = $dataTypes;
    }
    function getLabels() {
        return $this->colLabels;
    }
    function getRows() {
        return $this->rows;
    }
    

    function printTabActions($edit,$delete) {
        $printTab = '';
        $printTab .= "<table>
                        <thead>
                            <tr>";
                            foreach ($this->colLabels as $label) {
                                $printTab .= "<th>".str_replace('_', ' ',$label)."</th>";
                            }
                            $printTab .= "<th> Actions </th>";
        $printTab .= "      </tr>
                        </thead>
                        <tbody>"; 
    
                        foreach($this->rows as $row) {
                            $printTab .= "<tr>"; 
                            for ($i = 0; $i < sizeof($this->colLabels); $i++ ) {
                                $printTab .= "<td>".str_replace('_', ' ',$row[$i])."</td>";
                            }
                            if ($edit !== '') $printTab .= "<td> <a href='$edit'>Edit</a> / ";
                            if ($delete !== '') $printTab .= "<a href='$delete'>Delete</a> </td>";
                            
                            $printTab .= "</tr>";
                        }
    
        $printTab .= "  </tbody> 
                    </table>
                    </br>";
        
        return $printTab;
    }

    function printEditableTab($editable,$labelTab) {
        $printTab = '';
        $printTab .= '<form method="post">';
        $printTab .= "<table>
                        <thead>
                            <tr>";
                            foreach ($this->colLabels as $label) {
                                $printTab .= "<th>".str_replace('_', ' ',$label)."</th>";
                            }
                            $printTab .= "<th> <input type='submit' name='delete_$labelTab' style='height: 2em' value='Delete'> </th>";
        $printTab .= "      </tr>
                        </thead>
                        <tbody>"; 
    
                        $iter = 0;
                        foreach($this->rows as $row) {
                            $posname = '';
                            $printTab .= "<tr>"; 
                            for ($i = 0; $i < sizeof($this->colLabels); $i++ ) {
                                if ( $i < sizeof($row) ) $val = $row[$i];
                                else $val = '""';
                                
                                $printTab .= "<td>";
                                if ($editable[$i]) {
                                    $name = $labelTab . $posname . '@' . $this->colLabels[$i]; 
                                    $printTab .= "<input type='text'  maxlength='50' size='10' id='$name' name='$name' value=".str_replace('_', ' ',$val)." class='border-none'>";
                                } else {
                                    $posname .= '@' . $val;
                                    $printTab .= str_replace('_', ' ',$val); 
                                }
                                $printTab .= "</td>"; 
                            }
                            $item1 = $row[0];
                            $item2 = $row[1];
                            $printTab .= "<td> <input type='checkbox' name='rows_$labelTab"."[$item1][$item2]' /> </td>";
                            $printTab .= "</tr>";
                            $iter++;
                        }
    
        $printTab .= "  </tbody> 
                    </table>";
        $printTab .= "<input type='submit' name='Update_$labelTab' style='height: 2em' value='Update'>";
        $printTab .= '</form>';
        //$printTab .= "</br>";
        
        return $printTab;
    }

    function printEditTab($editable,$labelTab,$dev,$hasComment=false,$comment='') {
        //$counts = array_count_values($this->types);
        //$onlyChecked = (isset($counts['checked']) && $counts['checked']===sizeof($this->types))? true: false; 
        //$columnNumber = ($onlyChecked)? 2: sizeof($this->colLabels);

        $printTab = '';
        //$printTab .= "<div style='align:center'>";
        //$printTab .= "<spam id='TableTitle'>".htmlentities($dev)."</spam></br></br>";
        $printTab .= '<form method="post">';
        $printTab .= "<table> 
                       <thead>
                        <tr style='background-color: #f5f5f5;box-shadow:none'> 
                        <th style='background:  white;border:2px solid #333;
                        border-bottom:2px solid #eee;color: #333;'>".htmlentities($dev)."</th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee'></th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee'></th>
                        </tr> 
                        <tr>";
        $subLabels = array("","","</br>good/fair","");
        

        for ($i=0; $i<sizeof($this->colLabels); $i++) {
            $label = $this->colLabels[$i];
            $subLabel = '';
            if ($editable[$i]) $subLabel = $subLabels[$i]; 
            $printTab .= "<th style='background: #333; color: #eee;
            padding: 0.5rem;
            text-align: right;
            border: 2px solid #333;
            height: 2em;'>".$label.$subLabel."</th>";
        }

        $printTab .= "</tr>
                       </thead>
                        <tbody>"; 
    
        $iter = 0;

        
        foreach($this->rows as $row) {
            $posname = '@'.$dev;
            $printTab .= "<tr>"; 
            

            for ($i = 0; $i < sizeof($this->colLabels); $i++ ) {
                if ( $i < sizeof($row) ) $val = $row[$i];
                else $val = '""';
                                
                $printTab .= "<td style='border: white; text-align:right;'>";
                if ($editable[$i]) {
                    $name = $labelTab . $posname . '@' . $this->colLabels[$i]; 
                    if ($this->types[$iter] == 'float') {
                        if ($i==1) $printTab .= "<input type='number' size='10' id='$name' name='$name' value=$val class='border-none'  onkeydown='if (event.keyCode == 13) { this.form.submit(); return false; }' required>";
                        else {
                            $valArray = explode('|',$val); 
                            $valMin = (float)$valArray[0];
                            if (sizeof($valArray)>1) $valMax = (float)$valArray[1];
                            else $valMax = $valMin;
                            
                            $printTab .= "<input type='number' size='5' id='".$name."[min]' name='".$name."[min]' max=$valMax value=$valMin class='border-none' required>%";
                            $printTab .= "<input type='number' size='5' id='".$name."[max]' name='".$name."[max]' min=$valMin value=$valMax class='border-none' required>%";
                            
                        }
                    } else if ($this->types[$iter] == 'int') {
                        if ($i==1) $printTab .= "<input type='number' size='10' setp='1' id='$name' name='$name' value=$val class='border-none' onchange='this.form.submit();'>";
                        else $printTab .= '';
                    } else if ($this->types[$iter] == 'bool') {
                        if ($i==1) {
                        $isOn = '';
                        $isOff = '';

                        if (filter_var($val, FILTER_VALIDATE_BOOLEAN)) $isOn = 'checked';
                        else $isOff = 'checked';
                        $printTab .= "<label for='$name'> ".
                                       "<input type='radio' name='$name' value='True' $isOn onchange='this.form.submit();'> ON
                                        <input type='radio' name='$name' value='False' $isOff onchange='this.form.submit();'> OFF
                                      </label>";
                        } else {
                            $printTab .= '';
                        }
                    } else if ($this->types[$iter] == 'checked') {
                        if ($i==1) {
                            $isOn='';
                            if (filter_var($val, FILTER_VALIDATE_BOOLEAN)) $isOn = 'checked';
                        $printTab .= "<label for='$name'> ".
                        "<input type='checkbox' name='$name' value='True' $isOn onchange='this.form.submit();'>
                       </label>";
                       
                        } else {
                            $printTab .= '';
                        }
                    } else if ($this->types[$iter] == 'str') {
                        if ($i==1) $printTab .= "<input type='text' maxlength='50' size='10' id='$name' name='$name' value=".$val." class='border-none' onchange='this.form.submit();'>";
                        else $printTab .= '';
                    } else {
                        $printTab .= "<input type='text'  maxlength='50' size='10' id='$name' name='$name' value=".$val." class='border-none' onchange='this.form.submit();'>";
                    }
                } else {
                    $posname .= '@' . $val;
                    if ($i==0) $printTab .= '<b>';
                    $printTab .= $val;
                    if ($i==0) $printTab .= '</b>'; 
                }
                $printTab .= "</td>"; 
            }
            $printTab .= "</tr>";
            $iter++;
        }
    
        $printTab .= "  </tbody> 
                    </table>";
        $printTab .= "<input type='hidden' name='Update_$labelTab' style='height: 2em' value='Update'>";
        $printTab .= '</form>';
        
        if ($hasComment) {

            //$printTab .= '</form>';
          //  $printTab .= " <form method='post'>
          //               <textarea name='comment' 
          //               style='font-size: 1.5em; border: none;padding-left: 2em; max-width:80%; 
          //               min-width:80%; max-height:30vh; background: #F3F3F3;' placeholder='Add comment here ...'>".$comment."</textarea>
          //               <br>
          //               <button class='comment_button' type='submit'><i>Comment</i></button>".
          //               "</form>";
            $printTab .= " <form method='post' >
                         <textarea name='comment' 
                         style='font-size: 1.5em; border: none;padding-left: 2em; min-width:100%; 
                         max-width:100%; max-height:30vh; background: #F3F3F3;' 
                         onkeydown='if (event.keyCode == 13) { this.form.submit(); return false; }'
                         placeholder='Add comment here ...'>".$comment."</textarea>
                         ".
                         "</form>";
        } 
        //$printTab .=  "</div>";

        return $printTab;
    }

    function printEditTab2($dev) {
        $printTab = '';
        $printTab .= "<table> 
                       <thead>
                        <tr style='background-color: #f5f5f5;box-shadow:none;'> 
                        <th style='background:  white;border:2px solid #333;
                        border-bottom:2px solid #eee;color: #333; min-width: max-content';>".htmlentities($dev)."</th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; min-width:33.3%'></th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; min-width:33.3%'></th>
                        </tr> 
                        <tr>";
        
        $colLabels = array("Property","Setup","Error");

        foreach ($colLabels as $label) {
            $printTab .= "<th style='background: #333; color: #eee;
            padding: 0.5rem;
            text-align: right;
            border: 2px solid #333;
            height: 2em;'>".$label."</th>";
        }

        $printTab .= "</tr>
                       </thead>
                        <tbody>"; 
    
       
        
        foreach($this->rows as $row) {
            $posname = '@'.$dev;
            $printTab .= "<tr'>"; 

            $propLabel = $row[0];
            $propVal = $row[1];
            $propError = $row[2];

            $printTab .= "<td style='border: white; text-align:right; min-width: max-content;'> <b> ". 
                            str_replace("_"," ",$propLabel)  ." </b> </td>";
            $printTab .= "<td style='border: white; text-align:right; word-break: break-all'> $propVal </td>";
            $printTab .= "<td style='border: white; text-align:right;'> $propError </td>";
            
            $printTab .= "</tr>";
        }
    
        $printTab .= "  </tbody> 
                            </table><br>";
        

        return $printTab;
    }
    

    function validationTable($labelTab,$dev,$comment='') {
        $printTab = '';
        $printTab .= '<form method="post" >';
        $printTab .= "<table> 
                       <thead>
                        <tr style='background-color: #f5f5f5;box-shadow:none; max-width:25%'> 
                        <th style='background:  white;border:2px solid #333;
                        border-bottom:2px solid #eee;color: #333; max-width:25%'>". 
                        str_replace("_"," ",htmlentities($dev))."</th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; max-width:25%'></th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; max-width:25%'></th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; max-width:25%'></th>
                        </tr> 
                        <tr>";


        $colLabels = array("Property","Validation","Setup","Match");

        foreach ($colLabels as $label) {
            $printTab .= "<th style='background: #333; color: #eee;
            padding: 0.5rem;
            text-align: right;
            border: 2px solid #333;
            height: 2em;'>".$label."</th>";
        }

        $printTab .= "</tr>
                       </thead>
                        <tbody>"; 
    
        $iter = 0;

        
        foreach($this->rows as $row) {
            $posname = '@'.$dev;
            $printTab .= "<tr>"; 
            
            $prop = $row[0];
            $posname .= '@' . $prop;

            $printTab .= "<td style='border: white; text-align:right; min-width:15em;'> <b> ". str_replace("_"," ",$prop)  ." </b> </td>";

            $valValidation = $row[1];
            $valSetup = $row[2];
            
            $lowErr = '';
            $maxErr = '';
                    
            $printTab .= "<td style='border: white; text-align:right'>";
            $name = $labelTab . $posname . '@' . 'value'; 

            if ($this->types[$iter] == 'float') {
                $err = explode('|',$row[3]);
                $lowErr = $err[0];
                $maxErr = $err[1];
                $printTab .= "<input type='number' size='10' id='$name' name='$name' value=$valValidation class='border-none'  onkeydown='if (event.keyCode == 13) { this.form.submit(); return false; }' required>";       
            } else if ($this->types[$iter] == 'int') {
                $printTab .= "<input type='number' size='10' setp='1' id='$name' name='$name' value=$valValidation class='border-none' onchange='this.form.submit();'>";
            } else if ($this->types[$iter] == 'bool') {
                $isOn = '';
                $isOff = '';
                
                if ($valValidation !== '""') {
                    $valValidation = (filter_var($valValidation, FILTER_VALIDATE_BOOLEAN))? 'True': 'False';
                }
                
                if ($valValidation === 'True') $isOn = 'checked';
                else if ($valValidation === 'False') $isOff = 'checked';
            
                $printTab .= "<label for='$name'> ".
                            "<input type='radio' name='$name' value='True' $isOn onchange='this.form.submit();'> On
                            <input type='radio' name='$name' value='False' $isOff onchange='this.form.submit();'> Off
                            </label>";
             } else if ($this->types[$iter] == 'checked') {
                $printTab .= "<label for='$name'> ".
                        "<input type='checkbox' name='$name' value='True' checked onchange='this.form.submit();'>
                       </label>";
            } else if ($this->types[$iter] == 'str') {
                $printTab .= "<input type='text' maxlength='50' size='10' id='$name' name='$name' value=".$valValidation." class='border-none' onchange='this.form.submit();'>";
            } else {
                $printTab .= "<input type='text'  maxlength='50' size='10' id='$name' name='$name' value=".$valValidation." class='border-none' onchange='this.form.submit();'>";
            }
            $printTab .= "</td>"; 

            $printTab .= "<td style='border: white; text-align:right; word-break: break-all;'> $valSetup </td>";
            
            $settings = array($this->types[$iter],$valSetup,$lowErr,$maxErr);
            $comparisson = $this->compare($valValidation,$settings);
            $filepath = $this->get_match($comparisson);

            $printTab .= "<td style='border: #f5f5f5; text-align:right'> <img src='$filepath' style='max-width: 150%;
            max-height: 150%;
            display: block;float: left; padding-left:1em'> </td>";

            
            $printTab .= "</tr>";
            $iter++;
        }
    
        $printTab .= "  </tbody> 
                    </table>";
        $printTab .= "<input type='hidden' name='Update_Validation' style='height: 2em' value='Update' id='test'>";
        $printTab .= '</form>';
        
       
            $printTab .= " <form method='post' >
                         <textarea name='$labelTab@$dev' 
                         style='font-size: 1.5em; border: none;padding-left: 2em; 
                         margin-left:5%; width:90%; max-width:90%; 
                          max-height:30vh; background: #F3F3F3;' 
                         onkeydown='if (event.keyCode == 13) { this.form.submit(); return false; }'
                         placeholder='Add comment here ...'>".$comment."</textarea>
                         <input type='hidden' name='comment' value='comment'>
                         </form>";
        
        return $printTab;
    }

    function validationTableFixed($dev,&$summary,$comment='') {
        $printTab = '';
        $printTab .= "<table> 
                       <thead>
                        <tr style='background-color: #f5f5f5;box-shadow:none; max-width:25%'> 
                        <th style='background:  white;border:2px solid #333;
                        border-bottom:2px solid #eee;color: #333; max-width:25%'>". 
                        str_replace("_"," ",htmlentities($dev))."</th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; max-width:25%'></th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; max-width:25%'></th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; max-width:25%'></th>
                        </tr> 
                        <tr>";


        $colLabels = array("Property","Validation","Setup","Match");

        foreach ($colLabels as $label) {
            $printTab .= "<th style='background: #333; color: #eee;
            padding: 0.5rem;
            text-align: right;
            border: 2px solid #333;
            height: 2em;'>".$label."</th>";
        }

        $printTab .= "</tr>
                       </thead>
                        <tbody>"; 
    
        $iter = 0;

        
        foreach($this->rows as $row) {
            $posname = '@'.$dev;
            $printTab .= "<tr>"; 
            
            $prop = $row[0];
            $posname .= '@' . $prop;

            $printTab .= "<td style='border: white; text-align:right;'> <b> ". str_replace("_"," ",$prop)  ." </b> </td>";

            $valValidation = $row[1];
            $valSetup = $row[2];
            
            $lowErr = '';
            $maxErr = '';
                    
            $printTab .= "<td style='border: white; text-align:right'> $valValidation </td>";
            

            if ($this->types[$iter] == 'float') {
                $err = explode('|',$row[3]);
                $lowErr = $err[0];
                $maxErr = $err[1];
            } else if ($this->types[$iter] == 'bool') {
                $isOn = '';
                $isOff = '';
                
                if ($valValidation !== '""') {
                    $valValidation = (filter_var($valValidation, FILTER_VALIDATE_BOOLEAN))? 'True': 'False';
                }
                
                if ($valValidation === 'True') $isOn = 'checked';
                else if ($valValidation === 'False') $isOff = 'checked';
            }


            $printTab .= "<td style='border: white; text-align:right; word-break: break-all;'> $valSetup </td>";
            
            $settings = array($this->types[$iter],$valSetup,$lowErr,$maxErr);
            $comparisson = $this->compare($valValidation,$settings);
            if ($comparisson == 1) {
                $summary['correct'] += 1;
            } else if ($comparisson == 0.5) {
                $summary['fair'] += 1;
            } else if ($comparisson == 0) {
                $summary['wrong'] += 1;
            } else if ($comparisson == -1) {
                $summary['missing'] += 1;
            }
            $filepath = $this->get_match($comparisson);

            $printTab .= "<td style='border: #f5f5f5; text-align:right'> <img src='$filepath' style='max-width: 150%;
            max-height: 150%;
            display: block;float: left; padding-left:1em'> </td>";

            
            $printTab .= "</tr>";
            $iter++;
        }
    
        $printTab .= "  </tbody> 
                    </table>";
        if ($comment !== '') {
            $printTab .= "
                         <div 
                         style='font-size: 1.2em;  border: none;padding: 0.5em; 
                         margin-top:-2%; margin-left:5%; margin-bottom:5%; width:90%; max-width:90%; 
                          max-height:30vh; background: #F3F3F3;' 
                         >".$comment."</div> 
                         ";
        }
        return $printTab;
    }

    function compare($valOnline,$settings) {
        if ($valOnline != '""') {
        $type = $settings[0];
        $valSetup = $settings[1];
        
        if ( $type == 'bool' ) {
            $valSetup = (filter_var($valSetup, FILTER_VALIDATE_BOOLEAN))? 'True': 'False';
            return $valOnline==$valSetup;
        } else if ($type == 'str' || $type == 'int') {
            return $valOnline==$valSetup;     
        } else if ($type == 'float') {
            $lowErr = $settings[2]/100.;
            $maxErr = $settings[3]/100.;

            if (abs(($valOnline-$valSetup)/$valSetup) <= $lowErr) {
                return 1;
            } else if (abs(($valOnline-$valSetup)/$valSetup) <= $maxErr) {
                return 0.5;
            } else {
                return 0;
            }       

        }     
    } else {
        return -1;
    }
    }

    function get_match($value){
        if ($value == 1) return "../images/correct.png";
        else if ($value == 0.5) return "../images/fair.png";
        else if ($value == 0) return "../images/wrong.png";
        else if ($value == -1) return "../images/missing.png";

    }
    


    function printSetup($allDevicesAttributes) {
        $printSetup = '';
    
        $stages = array_keys($allDevicesAttributes);
        foreach ($stages as $stage) {
            $printSetup .= "<spam class='section_black'>".htmlentities($stage)."</spam></br>";
            $printSetup .= "<div style='padding:0.2em;margin-top:0px;background-color:black;height:0px;'></div>";
            $printSetup .= "</br>";
            
            $classes = array_keys($allDevicesAttributes[$stage]);
            foreach ($classes as $class) {
              

                $classDevs = array_keys($allDevicesAttributes[$stage][$class]);
                foreach ($classDevs as $dev) {
                    $printSetup .= "<spam class='section'>".htmlentities($dev)."</spam></br>";
                    $printSetup .= "<div style='padding:0.2em;margin-top:0px;background-color:#02a7fa;height:0px;'></div>";
                    $printSetup .= "</br>";

                    $this->rows = $allDevicesAttributes[$stage][$class][$dev];
                    $printSetup .= $this->printTab();
                }               
            }       
        }
           
        return  $printSetup;
    
    }

  
    private $colLabels = array();
    private $rows = array();
    private $types = array();
    private $names = array();



}
?>

<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
   -webkit-appearance: none;
   margin: 0;
}
input[type="number"] {
   -moz-appearance: textfield;
}
</style>