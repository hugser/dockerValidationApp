<?php

session_start();

if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to index.php
    header("Location: ../views/index.html.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = 'c3770d90087c11f9e3cd2825e64a0fdd';  // Pw is maxiv




// Check to see if we have some POST data, if we do process it
if ( isset($_POST['user']) && isset($_POST['pass']) ) {
    unset($_SESSION["user"]);  // Logout current user
    if ( strlen($_POST['user']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION["error"] = "user id and password are required";
        header( 'Location: login.php' ) ;
        return;
    } else {
        $check = hash('md5', $salt.$_POST['pass']);
        if ( $check == $stored_hash ) {
            // Redirect the browser to autos.php
            $_SESSION["user"] = $_POST["user"];
            header("Location: ../views/index.html.php");            
            error_log("Login success ".$_POST['email']);
            return;
        } else {
            $_SESSION["error"] = "Incorrect password";
            error_log("Login fail ".$_POST['email']." $check");
            header( 'Location: ../views/index.html.php' ) ;
            return;
        }
    }
}

?>

<?php include('../views/header.html.php');?>

<div>
    <h1>Please Log In</h1>
    <?php
        if ( isset($_SESSION["error"]) ) {
            echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
            unset($_SESSION["error"]);
        }
    ?>
    <form method="POST">
        <label for="user">User Name</label>
        <input type="text" name="user" id="user" style='direction: ltr;' maxlength='50' size='28'><br/>
        <label for="pass">Password</label>
        <input type="password" name="pass" id="pass" style='direction: ltr;' maxlength='50' size='28'><br/>
        <input type="submit" value="Log In">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>

<?php include('../views/footer.html');?>
