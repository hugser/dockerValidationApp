<?php

class prettyTab {

    function __construct() {
        $this->forms = new forms();
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
        $this->lists = json_decode(file_get_contents('../BackBone/list.json'),true)[$_GET['acc']];
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


    function setupTable($editable,$labelTab,$class,$dev,$comment='') {
        
        /*------------------------------------------------------------------------ 
                                        Table header
        --------------------------------------------------------------------------*/ 
        $printTab = '';
        $printTab .= '<form method="post">';
        $printTab .= "<table> 
                       <thead>
                        <tr style='background-color: #f5f5f5;box-shadow:none'> 
                        <th style='background:  white;border:0.1px solid white; border-radius: 0.5em;
                        border-bottom:2px solid #eee;color: #333; box-shadow: inset 0em 0em .4em .4em rgba(0,0,0,0.5);'>".htmlentities($dev)."</th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee'></th>
                        <th style='background: white;border: 2px solid white;border-bottom:2px solid #eee'></th>
                        </tr> 
                        <tr>";
        $subLabels = array("","","</br>good/fair","");
        
        
        for ($i=0; $i<sizeof($this->colLabels); $i++) {
            $label = $this->colLabels[$i];
            $subLabel = '';
            if ($editable[$i]) $subLabel = $subLabels[$i]; 
            $printTab .= "<th style='background: #333; 
                                     color: #eee; 
                                     padding: 0.5rem; 
                                     text-align: right; 
                                     border: 2px solid #333;
                                     height: 2em;'>".$label.$subLabel."</th>";
        }

        $printTab .= "</tr> </thead>"; 
        $printTab .= "<tbody>";
    
        $iter = 0;
        
        /*------------------------------------------------------------------------ 
                                        Table rows
        --------------------------------------------------------------------------*/
        foreach($this->rows as $row) {
            $posname = '@'.$dev;
            
            
            $printTabRow = '';
            for ($i = 0; $i < sizeof($this->colLabels); $i++ ) {
                if ( $i < sizeof($row) ) $val = $row[$i];
                else $val = '""';
                                
                if ($row[0] !== 'upload') $printTabRow .= "<td style='border: white; text-align:right; word-break: break-all;'>";

                if ($editable[$i] && $row[0] !== 'upload' ) { # --------- Has input and is not upload

                    $name = $labelTab . $posname . '@' . $this->colLabels[$i]; 

                    if ($this->types[$iter] == 'float') { # --------- Input is float

                        if ($i==1) { # --------- input column

                            $printTabRow .= "<input type='number' size='5' id='$name' name='$name' value=$val class='border-none'  
                                                onkeydown='if (event.keyCode == 13) { this.form.submit(); return false; }' required>";

                        } else { # --------- error column

                            $valArray = explode('|',$val); 
                            $valMin = (float)$valArray[0];
                            if (sizeof($valArray)>1) $valMax = (float)$valArray[1];
                            else $valMax = $valMin;
                            
                            $printTabRow .= "<input type='number' size='1' id='".$name."[min]' 
                                                name='".$name."[min]' max=$valMax value=$valMin class='border-none' required>%";
                            $printTabRow .= "<input type='number' size='1' id='".$name."[max]' 
                                                name='".$name."[max]' min=$valMin value=$valMax class='border-none' required>%";
                            
                        }

                    } else if ($this->types[$iter] == 'int') { # --------- Input is integer

                        if ($i==1) { # --------- input column

                            $printTabRow .= "<input type='number' size='5' setp='1' id='$name' name='$name' 
                                                value=$val class='border-none' onchange='this.form.submit();'>";
                        
                        } else { # --------- No error input

                            $printTabRow .= '';
                        }

                    } else if ($this->types[$iter] == 'bool') { # --------- Input is True/False or On/Off

                        if ($i==1) { # --------- input column

                            $isOn = '';
                            $isOff = '';

                            if (filter_var($val, FILTER_VALIDATE_BOOLEAN)) $isOn = 'checked';
                            else $isOff = 'checked';
                            $printTabRow .= "<label for='$name'> ".
                                       "<input type='radio' name='$name' value='True' $isOn onchange='this.form.submit();'> ON
                                        <input type='radio' name='$name' value='False' $isOff onchange='this.form.submit();'> OFF
                                      </label>";

                        } else { # --------- No error input

                            $printTabRow .= '';

                        }

                    } else if ($this->types[$iter] == 'str') {  # --------- Input is string

                        if ($i==1) { # --------- input column

                            $printTabRow .= "<input type='text' maxlength='50' size='5' id='$name' name='$name' 
                                                value=".$val." class='border-none' onchange='this.form.submit();'>";
                        
                        } else {  # --------- No error input
                            
                            $printTabRow .= '';
                        }

                    } else if ($this->types[$iter] == 'list') {  # --------- Input is elements of pre-defined list

                        if ($i==1) { # --------- input column
                            
                            $valArray = explode(',', $val);
                            
                            $fullList = $this->lists[$dev][$row[$i-1]];
                            
                            $printTabRow .= "<div class='checkboxesTab' id='".str_replace(" ", "",$name)."'>";
                            foreach ($fullList as $item) {
                                $is_checked = '';
                                if (false !== array_search($item,$valArray)) {
                                    $is_checked = 'checked';
                                }
                    
                                $printTabRow .= "<label class='container'>".htmlentities($item)."
                                    <input type='checkbox' name='".$name."[$item]' ".$is_checked." onchange='this.form.submit();' />
                                    <span class='checkmark'></span>
                                    </label>";
                            }
                            $printTabRow .= "</div></label>";
                        
                        } else {   # --------- No error input

                            $printTabRow .= '';

                        }

                    } else {

                        $printTabRow .= "<input type='text'  maxlength='50' size='5' id='$name' name='$name' 
                                        value=".$val." class='border-none' onchange='this.form.submit();'>";
                    
                    }
                } else if ($row[0] !== 'upload'  ) {  # --------- Has no input and is not upload

                    $posname .= '@' . $val;
                    if ($i==0) $printTabRow .= '<b>';
                    
                    if ($this->colLabels[$i] == 'value') {
                       
                        foreach(explode(',',$val) as $key => $value)
                        {
                            $printTabRow .= $value . '<br>';
                        }

                    } else $printTabRow .= $val;
                    if ($i==0) $printTabRow .= '</b>'; 

                } else { # --------- Is upload

                    $LOAD_FILE = True;
                    if ($i==1 && $val !== '') {
                        $stage = $labelTab;
                        $target_file = $class . '_' . $stage . '_' . $dev . '_' . $val;
                        $DISPLAY_FILE = True;
                        
                    }

                }
          
                $printTabRow .= "</td>"; 

                
            }

            if ($row[0] !== 'upload' ) {  # --------- Remove empy line of table

                $printTab .= "<tr>" . $printTabRow . "</tr>"; 

            } 
            
            $iter++;
        }
    
        $printTab .= "</tbody></table>";
        $printTab .= "<input type='hidden' name='Update_$labelTab' style='height: 2em' value='Update'> </form>";
        

        /*------------------------------------------------------------------------ 
                                        Upload button
        --------------------------------------------------------------------------*/
        if (isset($LOAD_FILE) && in_array(true,$editable)) {

            $printTab .= '<form action="../classes/imageUpload.php?action=setup&acc='.$_GET['acc'].'" 
                            method="post" enctype="multipart/form-data">';
            $printTab .= "<input type='file' id='imgfile' onchange='this.form.submit()' name='image' value='Load'>
                            <label for='imgfile' >Upload</label>";
            $printTab .= '</form>';
            unset($LOAD_FILE);
            
        }     
        
        
        /*------------------------------------------------------------------------ 
                                        Load image or plot
        --------------------------------------------------------------------------*/
        if (isset($DISPLAY_FILE) && file_exists('../uploads/images/'.$target_file)) {
          
            $filetype =  end(explode('.',$target_file));
          
            if ($filetype == 'json') {
                
                $path = '../uploads/images/'.$target_file;
                $data = json_decode(file_get_contents($path),true);
                
                $printTab .=  '<div class ="plot" id="plot1D" dataX='.implode(',',$data['X']).' dataY='.implode(',',$data['Y']).'
                                 dataXlabel="'.htmlentities($data['Xlabel']).'" dataYlabel="'.htmlentities($data['Ylabel']).'"
                                  plotTitle="'.htmlentities($data['Title']).'"> 
                                <script type="text/javascript" src = "../JS/1Dplot.js"> </script>
                                </div>';

            } else {

                $printTab .= '<img src="../uploads/images/'.$target_file.'"  alt="test" 
                                    style="max-width:100%;max-height:45%;margin-bottom: 1%;">';
            
            }      
            
            unset($DISPLAY_FILE);
        }

        /*------------------------------------------------------------------------ 
                                        Comment box
        --------------------------------------------------------------------------*/
        if (in_array(true,$editable)) {

            $printTab .= "<form method='post' >
                            <textarea name='comment' class='tabComment'
                                onkeydown='if (event.keyCode == 13) { this.form.submit(); return false; }'
                                placeholder='Add comment here ...'>".$comment."</textarea>
                          </form>";

        } else if ($comment !=='') {
            $printTab .= "<div class='tabComment'> <b>Comment: </b>". $comment ."</div>";
        }
        
        return $printTab;
    }


    function validationTable($editable,$labelTab,$class,$dev,&$summary,$comment='') {
        
        /*------------------------------------------------------------------------ 
                                        Table header
        --------------------------------------------------------------------------*/ 
        $printTab = '';
        $printTab .= '<form method="post">';
        $printTab .= "<table> 
                       <thead>
                        <tr style='background-color: #f5f5f5;box-shadow:none'> 
                        <th style='background:  white;border:0.1px solid white; border-radius: 0.5em;
                        border-bottom:2px solid #eee;color: #333; box-shadow: inset 0em 0em .4em .4em rgba(0,0,0,0.5);'>".htmlentities($dev)."</th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; max-width:25%'></th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; max-width:25%'></th>
                        <th style='background: white;border: 2px solid white;border-bottom:2px solid #eee'></th>
                        </tr> 
                        <tr>";
        $subLabels = array("","","</br>good/fair","");
        
        
       
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

        /*------------------------------------------------------------------------ 
                                        Table rows
        --------------------------------------------------------------------------*/
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
                    
            $printTab .= "<td style='border: white; text-align:right'>";
            $name = $labelTab . '@' . $class . $posname . '@' . 'value'; 

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

             } else if ($this->types[$iter] == 'str') { 

                $printTab .= "<input type='text' maxlength='50' size='10' id='$name' name='$name' value=".$valValidation." class='border-none' onchange='this.form.submit();'>";
            
            } else if ($this->types[$iter] == 'list') {  # --------- Input is elements of pre-defined list
                    
                    $valArray = explode(',', $valValidation);
                    
                    $fullList = $this->lists[$dev][$row[0]];
                    
                    $printTab .= "<div class='checkboxesTab' id='".str_replace(" ", "",$name)."'>";

                    foreach ($fullList as $item) {
                        $is_checked = '';
                        if (false !== array_search($item,$valArray)) {
                            $is_checked = 'checked';
                        }
            
                        $printTab .= "<label class='container'>".htmlentities($item)."
                            <input type='checkbox' name='".$name."[$item]' ".$is_checked." onchange='this.form.submit();' />
                            <span class='checkmark'></span>
                            </label>";
                    }
                    $printTab .= "</div></label>";
                

            } else if ($prop  == 'upload'  ) { # --------- Is upload
                

                $LOAD_FILE = True;
                if ($valSetup !== '') {
                    $stage = $labelTab;
                    $target_file = $class . '_' . $stage . '_' . $dev . '_' . $valSetup;
                    $DISPLAY_FILE = True;
                    
                }

            } else {
        
                $printTab .= "<input type='text'  maxlength='50' size='10' id='$name' name='$name' value=".$valValidation." class='border-none' onchange='this.form.submit();'>";
            }

            $printTab .= "</td>"; 

            $printVal = '';
            foreach(explode(',',$valSetup) as $key => $value) {
                            $printVal .= $value . '<br>';
            }


            $printTab .= "<td style='border: white; text-align:right; word-break: break-all;'> $printVal </td>";
            
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

            $printTab .= "<td > <img src='$filepath' style='display: block; max-width: 2em;max-height: 2em; '> </td>";

            
            $printTab .= "</tr>";

           // if ($row[0] !== 'upload' ) {  # --------- Remove empy line of table

           //     $printTab .= "<tr>" . $printTabRow . "</tr>"; 
    
          //  } 

    
            $iter++;
        }
    
       

        $printTab .= "</tbody></table>";
        $printTab .= "<input type='hidden' name='Update_Validation' style='height: 2em' value='Update'> </form>";
        

        /*------------------------------------------------------------------------ 
                                        Upload button
        --------------------------------------------------------------------------*/
        if (isset($LOAD_FILE) && in_array(true,$editable)) {

            $printTab .= '<form action="../classes/imageUpload.php?action=setup&acc='.$_GET['acc'].'" 
                            method="post" enctype="multipart/form-data">';
            $printTab .= "<input type='file' id='imgfile' onchange='this.form.submit()' name='image' value='Load'>
                            <label for='imgfile' >Upload</label>";
            $printTab .= '</form>';
            unset($LOAD_FILE);
            
        }     
        
        
        /*------------------------------------------------------------------------ 
                                        Load image or plot
        --------------------------------------------------------------------------*/
        if (isset($DISPLAY_FILE) && file_exists('../uploads/images/'.$target_file)) {
          
            $filetype =  end(explode('.',$target_file));
          
            if ($filetype == 'json') {
                
                $path = '../uploads/images/'.$target_file;
                $data = json_decode(file_get_contents($path),true);
                
                $printTab .=  '<div class ="plot" id="plot1D" dataX='.implode(',',$data['X']).' dataY='.implode(',',$data['Y']).'
                                 dataXlabel="'.htmlentities($data['Xlabel']).'" dataYlabel="'.htmlentities($data['Ylabel']).'"
                                  plotTitle="'.htmlentities($data['Title']).'"> 
                                <script type="text/javascript" src = "../JS/1Dplot.js"> </script>
                                </div>';

            } else {

                $printTab .= '<img src="../uploads/images/'.$target_file.'"  alt="test" 
                                    style="float:right; max-width:48%;max-height:80%;margin-bottom: 1%;">';
            
            }      
            
            unset($DISPLAY_FILE);
        }

        /*------------------------------------------------------------------------ 
                                        Comment box
        --------------------------------------------------------------------------*/
        if (in_array(true,$editable)) {

            $printTab .= "<form method='post' >
                            <textarea name='comment' class='tabComment'
                                onkeydown='if (event.keyCode == 13) { this.form.submit(); return false; }'
                                placeholder='Add comment here ...'>".$comment."</textarea>
                          </form>";

        } else if ($comment !=='') {
            $printTab .= "<div class='tabComment'> <b>Comment: </b>". $comment ."</div>";
        }
        
        return $printTab;
    }

    /*
    function validationTable($labelTab,$dev,$comment='',&$summary) {
        $printTab = '';
        $printTab .= '<form method="post" >';
        $printTab .= "<table> 
                       <thead>
                       <tr style='background-color: #f5f5f5;box-shadow:none'> 
                       <th style='background:  white;border:0.1px solid #333;
                       border-bottom:2px solid #eee;color: #333; box-shadow: inset 0em 0em .4em .4em rgba(0,0,0,0.5);'>".htmlentities($dev)."</th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; max-width:25%'></th>
                        <th style='background: white;border: none;border-bottom:2px solid #eee; max-width:25%'></th>
                        <th style='background: white;border: 2px solid white;border-bottom:2px solid #eee; max-width:25%'></th>
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

            $printTab .= "<td > <img src='$filepath' style='display: block; max-width: 2em;max-height: 2em; '> </td>";

            
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

*/
    function compare($valOnline,$settings) {
        if ($valOnline != '""') {
        $type = $settings[0];
        $valSetup = $settings[1];
        
        if ( $type == 'bool' ) {
            $valSetup = (filter_var($valSetup, FILTER_VALIDATE_BOOLEAN))? 'True': 'False';
            return $valOnline==$valSetup;
        } else if ($type == 'str' || $type == 'int' || $type == 'list') {
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
    private $list = array();




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

