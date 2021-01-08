<?php
ob_start();
session_start();
// Demand a GET parameter
#if ( ! isset($_SESSION['user'])) {
#    die('Not logged in');
#}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.html.php');
    return;
}

include __DIR__ . '/../includes/autoloader.php';
//require_once '../classes/prettyTab.php';

$tab = new prettyTab();
$mySQL = new mySQL();
$mySQL->useDB("ValidationDB");

//$mySQL->getValidations("R1");

//$inputLabels = array('Versions');
//$tab->setLabels($inputLabels);
//$tab->setRows($mySQL->validationsList);


?>

<?php include('./headerAdmin.html.php');?>

<div>
<?php  print_r($_SESSION) ;?>
</div>

<?php include('./footer.html');?>
