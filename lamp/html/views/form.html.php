<?php

ob_start();
session_start();


// If the user requested logout or not login go back to index.html.php
if ( ! isset($_SESSION['user']) || isset($_POST['logout']) ) {
    header('Location: ../views/index.html.php');
    return;
}

$onlineData = json_decode(file_get_contents("../data/validation_data_R3.json"), true);
$setupData = json_decode(file_get_contents("../data/setup_data_R3.json"), true);




$tabNONE = array('Ring Optical Properties','Injection Magnet','SCRAPER','STATE MACHINE',
                'Devices Attributes','Beam Position Monitor');
$tabCold = array('Beam Properties','RF Cavities','BBB','BEAM MONITOR','FEEDBACK','Beamline Bumps');
$tabHot = array('Beam Properties','RF Cavities','BBB','BEAM MONITOR');

$tabsContent = array(
    'NONE' => $tabNONE,
    'COLD' => $tabCold,
    'HOT' => $tabHot
);
$tabsContentLabels = array_keys($tabsContent);


if (!isset($_GET['tab'])) {
    $_GET['tab'] = 1;
  }



include('./header.html.php');
include('./functions.php');

?>



<div class = "validationContent">
    <div class = "validationForm" >  
        <form method="post">   
            <div id="tab-container">
                <?php createInputTabs($tabsContentLabels)?>
            </div>

            <div class = "inner" >
                <?php
                    inputFormTab($onlineData,$tabsContent[$tabsContentLabels[$_GET['tab']-1]]);
                ?>  
            </div> 
            
        </form>          
    </div>

    <div class = "validationLayout">
        <div class = 'header'> R3 Validation Form </div>
        <div class = "inner"> 
      
            <?php updateForm(); 
            print_r($_SESSION);
            echo "<br>";
           // print_r($onlineData);
            validateFormTab($onlineData,$setupData,$tabsContent[$tabsContentLabels[$_GET['tab']-1]]);
               
            ?>

        </div>  
    </div>
</div>


<br>
            

<?php include('./footer.html'); ?> 
