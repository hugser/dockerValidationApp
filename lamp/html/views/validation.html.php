<?php

ob_start();
session_start();

// If the user requested logout or not login go back to index.html.php
if ( ! isset($_SESSION['user']) || isset($_POST['logout']) ) {
    header('Location: ../views/index.html.php');
    return;
}

include_once( __DIR__ . '/../includes/autoloader.php');
 



    $labelValidation = 'VALIDATION';
    $labelSetup = 'SETUP';
    $labelCL = 'CLASS';
    
    
    
    $session = new session();
    
    $session->GET_acc();
    
    
    
    /* 
    Initialize classes
    */
    
    $mySQL = new mySQL();
    $layout = new layout();
    $tab = new prettyTab();
    $forms = new forms();
    
    
    $mySQL->useDB("ValidationDB");
    
    $stages = array_merge(...$mySQL->getTabRows('stageMACHINE', array('stage')));
    $classList = str_replace("_MACHINE", "",$mySQL->getDBTables('%\_MACHINE'));
    
//if (!isset($_SESSION['VALIDATION_INITIALIZED'])) {
//        $_SESSION['VALIDATION_INITIALIZED'] = TRUE; 
    $input = array();
    $input['mySQL'] = $mySQL;
    $input['table'] = $tab;
    $input['forms'] = $forms;
    $input['Setup label'] = $labelSetup;
    $input['Validation label'] = $labelValidation;
    $input['Class label'] = $labelCL;
    $input['stages'] = $stages;
    $input['Classes List'] = $classList;
    
    $validation = new validation($input);
//}



// --------- POST request to SESSION

if (isset($_POST[$labelValidation])) $validation->SESSION_validationStamp($labelValidation);
else if ( isset($_POST[$labelSetup]) ) $validation->SESSION_setupStamp($labelSetup);
else if ( isset($_POST["new$labelValidation"]) ) $validation->SESSION_newValidation($labelValidation);
else if ( isset($_POST["update$labelValidation"]) ) $validation->SESSION_updateValidation($labelValidation);
else if ( isset($_POST["Update_Validation"]) ) $validation->SESSION_updateValInSession();
else if ( isset($_POST['comment']) ) $validation->SESSION_updateComment();



// --------- In SESSION

if ( isset($_SESSION['validation'][$_GET['acc']]['updateVALIDATION']) ) $validation->updateValidation();
else if (isset($_SESSION['validation'][$_GET['acc']]['newVALIDATION']) ) $validation->newValidation();
else if ( isset($_FILES["loadFile"]['tmp_name']) ) $validation->loadJSON();


if ( isset($_SESSION['validation'][$_GET['acc']]) & 
    (!isset($_GET['stage']) || !in_array($_GET['stage'],array('OVERVIEW','NONE','COLD','HOT')))) {
    $_GET['stage'] = 'OVERVIEW';
    header("Location: validation.html.php?acc=".$_GET['acc']. "&stage=".$_GET['stage']);
    return;
}

$tabArray = array('OVERVIEW');
$layoutInfo = array('OVERVIEW'=>'','NONE' => '','COLD' => '','HOT'=>'');
$validation->validationsCheckBox($labelValidation,$formSetup);

if ( isset($_SESSION['validation'][$_GET['acc']]['validation']) ) {
       //   echo '<pre>';
       //   print_r($_SESSION['validation']);
       //   echo '</pre>';
    $validation->validationsActions($labelValidation,$formSetup);
    $validation->createValidationForm($layoutInfo,$tabArray);
} 


function createInputTabs($list_of_tab) {
    echo "<ul>";
    foreach ($list_of_tab as $tab) {
        echo "<li class=".is_selected($tab)." >
                <a href='validation.html.php?acc=".$_GET['acc']."&stage=".htmlentities($tab)."'>".htmlentities($tab)."</a>";
        
    }
    echo "</ul>";        
}



function is_selected($value){
    if ($_GET['stage']==$value) return "selected";
}


?>
<script type="text/javascript" src="../JS/confirmWindow.js"></script>

<?php include('./headerAdmin.html.php');?>


    <div class = "leftLayout" >  

            <div class = "inner" >
                <?php 
                    echo $formSetup;
                ?>    
            </div> 
                  
    </div>

    <div class = "rightLayout">
        <?php 
            if (isset($_SESSION['validation'][$_GET['acc']]['validation'])) {
                
                echo '<div id="tab-container">';
                createInputTabs($tabArray);
                echo '</div>';

                echo '<div class = "inner">';
                echo $layoutInfo[$_GET['stage']];
                echo ' </div>';
            }
        ?>
            



           
        </div>



<br>

<?php include('./footer.html.php'); ?> 
