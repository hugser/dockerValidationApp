<?php

ob_start();
session_start();

// If the user requested logout or not login go back to index.html.php
if ( ! isset($_SESSION['user']) || isset($_POST['logout']) ) {
    header('Location: ../views/index.html.php');
    return;
}

include __DIR__ . '/../includes/autoloader.php';
 

$labelSetup = 'SETUP';
$labelCL = 'CLASS';

if ( !isset($_GET['accSetup']) || !in_array($_GET['accSetup'],array('R1','R3'))) {
    $_SESSION['error'] = "Missing acc";
    header('Location: ../views/index.html.php');
    return;
}




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
//$devList = $mySQL->getDBTables('devList%');


$input = array();
$input['mySQL'] = $mySQL;
$input['table'] = $tab;
$input['forms'] = $forms;
$input['Setup label'] = $labelSetup;
$input['Class label'] = $labelCL;
$input['stages'] = $stages;
$input['Classes List'] = $classList;

$setup = new setup($input);

if ( isset($_POST['updateSETUP']) ) {
    $_SESSION['setup'][$_GET['accSetup']]['updateSETUP'] = $_POST['updateSETUP'];
    header("Location: setup.html.php?accSetup=".$_GET['accSetup']. "&accStage=".$_GET['accStage']);
    return;
}

if ( isset($_POST[$labelSetup]) ) {
    unset($_SESSION['setup'][$_GET['accSetup']]);
    $_SESSION['setup'][$_GET['accSetup']][$labelSetup] = $_POST[$labelSetup];
    header("Location: setup.html.php?accSetup=".$_GET['accSetup']. "&accStage=".$_GET['accStage']);
    return;
}

if ( isset($_POST[$labelCL]) ) {
    if (isset($_SESSION['setup'][$_GET['accSetup']][$labelCL]) && 
        $_SESSION['setup'][$_GET['accSetup']][$labelCL] === $_POST[$labelCL]) {
            unset($_SESSION['setup'][$_GET['accSetup']][$labelCL]);
            
            header("Location: setup.html.php?accSetup=".$_GET['accSetup']. "&accStage=".$_GET['accStage']);
            return;
        } 
    $_SESSION['setup'][$_GET['accSetup']][$labelCL] = $_POST[$labelCL];
 
    header("Location: setup.html.php?accSetup=".$_GET['accSetup']. "&accStage=".$_GET['accStage']);
    return;
}

if ( isset($_POST["newSETUP"]) ) {
    $_SESSION['setup'][$_GET['accSetup']]['newSETUP'] = $_POST['newSETUP'];
    header("Location: setup.html.php?accSetup=".$_GET['accSetup']. "&accStage=".$_GET['accStage']);
    return;
}

// load class data
$setup->loadJSON();

// update $_SESSION with new label of device for new setup
$setup->SESSION_newSetupDev();

// update $_SESSION with new label of properties for new setup
$setup->SESSION_newSetupProp();

// Update $_SESSION with new setup vales
$setup->SESSION_updateStage();

// update $_SESSION with comment 
$setup->SESSION_updateComment();

// If 'newSETUP' true, creates a ghost setup
$setup->newSetup();

// If 'updateSETUP' true, creates a new setup with the old setup valuesa and the new online values
$setup->updateSetup();


if ( isset($_SESSION['setup'][$_GET['accSetup']][$labelCL]) & 
    (!isset($_GET['accStage']) || !in_array($_GET['accStage'],array('OVERVIEW','NONE','COLD','HOT')))) {
    $_GET['accStage'] = 'OVERVIEW';
    header("Location: setup.html.php?accSetup=".$_GET['accSetup']. "&accStage=".$_GET['accStage']);
    return;
}

if ( isset($_SESSION['setup']) & 
    (!isset($_GET['accStage']) || !in_array($_GET['accStage'],array('OVERVIEW','NONE','COLD','HOT')))) {
    $_GET['accStage'] = 'OVERVIEW';
    header("Location: setup.html.php?accSetup=".$_GET['accSetup']. "&accStage=".$_GET['accStage']);
    return;
}
  

$formClass = '';
$printDevProps = '';
$tabArray = array('OVERVIEW');
$layoutInfo = array('OVERVIEW'=>'','NONE' => '','COLD' => '','HOT'=>'');

//If


if ( isset($_SESSION['setup'][$_GET['accSetup']][$labelSetup]) ) {
  //  echo '<pre>';
  //  print_r($_SESSION['setup']);
  //  echo '</pre>';
    /* GET CLASSES LIST */

    /* CLASSES FORM */
    $choosenCL = isset($_SESSION['setup'][$_GET['accSetup']][$labelCL]) ? $_SESSION['setup'][$_GET['accSetup']][$labelCL] : '';
    $formClass = $forms->checkLOAD($labelCL,$classList,$choosenCL);

    if (isset($_SESSION['setup'][$_GET['accSetup']][$labelCL])) {

        $setup->createClassForm($layoutInfo,$tabArray,$printDevProps,$choosenCL);
        
    } else {
        $setup->createSetupLayout($layoutInfo);
        
   }
}

 
$IDs = $mySQL->getSetupVerIDs($_GET['accSetup']);
$setupList = $mySQL->getSetupAttrByID("stamp",$IDs);
$choosenSetup = isset($_SESSION['setup'][$_GET['accSetup']][$labelSetup]) ? $_SESSION['setup'][$_GET['accSetup']][$labelSetup] : '';
$commentList = $mySQL->getSetupAttrByID("label",$IDs);

$formSetup = $forms->checkCU($labelSetup,$setupList,$commentList,$choosenSetup);




function createInputTabs($list_of_tab) {
    echo "<ul>";
    foreach ($list_of_tab as $tab) {
        echo "<li class=".is_selected($tab)." >
                <a href='setup.html.php?accSetup=".$_GET['accSetup']."&accStage=".htmlentities($tab)."'>".htmlentities($tab)."</a>";
        
    }
    echo "</ul>";        
}



function is_selected($value){
    if ($_GET['accStage']==$value) return "selected";
}


?>
<script type="text/javascript" src="../JS/confirmWindow.js"></script>

<?php include('./headerAdmin.html.php');?>



    <div class = "leftLayout" >  

            <div class = "inner" >
                <?php 
                    echo $formSetup;
                    echo "<div style='height: 10%'></div>";
                    echo $formClass;
                ?>    
            </div> 
                  
    </div>

    <div class = "rightLayout">
        <!--<div class = "header"> <?php echo $_GET['accSetup']; ?> Setup </div>-->

        <?php 
            if (isset($_SESSION['setup'][$_GET['accSetup']][$labelSetup])) {
                if (sizeof($tabArray)>1) {
                    echo '<div id="tab-container">';
                    createInputTabs($tabArray);
                    echo '</div>';
                }

                echo '<div class = "inner">';
                echo $layoutInfo[$_GET['accStage']];
                echo ' </div>';
            }
                

                
         
        ?>
 
    </div>



<br>

<?php include('./footer.html.php'); ?> 
