<?php 

    $menu = '';
    if ( isset($_SESSION['user']) ) {
        $menu .= '<div class="subnav">
                    <button class="subnavbtn">Submit Validation
                    </button>
                    <div class="subnav-content">
                        <a href="../views/validation.html.php">R3 </a><br>
                        <a href="#">R1 </a><br>
                        <a href="#">SPF</a>
                    </div>
                </div> 

                <div class="subnav">
                    <button class="subnavbtn">Update Setup
                    </button>
                    <div class="subnav-content">
                        <a href="../views/setup.html.php">R3 </a><br>
                        <a href="#">R1 </a><br>
                        <a href="#">SPF</a>
                    </div>
            </div> 
            <a href="../views/about.html.php">About</a>
            <a href="../session/logout.php">Logout</a>';
    } else {
        $menu .=  '<a href="../views/about.html.php">About</a>
                  <a href="../session/login.php">Login</a>';
    }
?>

<!DOCTYPE html>
    <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <link rel="stylesheet" href="../CSS/style.css">
            <link rel="stylesheet" href="../CSS/styleTabs.css">
        </head>

        <body>

        <div class="navbar">
            <a href="../views/index.html.php">Main</a>
            <?php echo $menu; ?>
        </div>
        <div style="padding:5px;margin-top:0px;background-color:#02a7fa;height:0px;"></div>

