<?php

ob_start();
session_start();

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: ../views/index.html.php');
    return;
}


include __DIR__ . '/../includes/autoloader.php';


$tab = new prettyTab();
$mySQL = new mySQL();
$mySQL->useDB("ValidationDB");

$mySQL->getValidations("R1");

$inputLabels = array('Versions');
$tab->setLabels($inputLabels);
$tab->setRows($mySQL->validationsList);


?>

<?php include('./header.html.php');?>

<img src="../mySQL/schema.png" alt="Schema">

<?php include('./footer.html');?>
