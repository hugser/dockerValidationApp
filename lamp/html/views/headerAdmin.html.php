<?php 


    $menu = '';
    if ( isset($_SESSION['user']) ) {
        $menu .= '<div class="subnav">
                    <button class="subnavbtn">Submit Validation
                    </button>
                    <div class="subnav-content">
                        <a href="../views/validation.html.php?acc=R3">R3 </a><br>
                        <a href="../views/validation.html.php?acc=R1">R1 </a><br>
                        <a href="#">SPF</a>
                    </div>
                </div> 

                <div class="subnav">
                    <button class="subnavbtn">Update Setup
                    </button>
                    <div class="subnav-content">
                        <a href="../views/setup.html.php?acc=R3">R3 </a><br>
                        <a href="../views/setup.html.php?acc=R1">R1 </a><br>
                        <a href="#">SPF</a>
                    </div>
            </div> 
            <a href="../views/about.html.php">About</a>
            <a href="../session/logout.php">Logout</a>';
    } else {
        $menu .=  '<a href="../views/about.html.php">About</a>
                  <a href="../session/login.php">Login</a>';
    }

    $line = '<div style="margin-top:0%;background-color:#02a7fa;height:1%;"></div>';

    
    if (strpos($_SERVER['REQUEST_URI'],'setup.html.php')!==false) {
        $menu .= '<div class="head"> ' . $_GET['acc'] . ' Setup </div>';
    } elseif (strpos($_SERVER['REQUEST_URI'],'validation.html.php')!==false) {
        $menu .= '<div class="head"> ' . $_GET['acc'] . ' Validation </div>';
    } elseif (strpos($_SERVER['REQUEST_URI'],'admin.html.php')!==false) {
        $menu .= '<div class="headAdmin"> DB Managment </div>';
        $line = '<div style="margin-top:0%;background-color:#fa1702;height:1%;"></div>';
    }

    
    

?>

<!DOCTYPE html>
    <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="../CSS/styleAdmin.css">
            <link rel="stylesheet" href="../CSS/styleTabs.css">
            <script src="../includes/echarts/dist/echarts.min.js"></script>

        </head>

        <body>
        
        <div class="navbar">
            <a href="../views/index.html.php">Main</a>
            <?php 
            
            if (isset($_SESSION['fail'])) {
                $menu .= '<div class="flash_fail">' . $_SESSION['fail'] .'</div>';
                unset($_SESSION['fail']);
            } else if (isset($_SESSION['ok'])) {
                $menu .= '<div class="flash_ok">' . $_SESSION['ok'] .'</div>';
                unset($_SESSION['ok']);
            }

            echo $menu; ?>
        </div>
        <?php echo $line; ?>

