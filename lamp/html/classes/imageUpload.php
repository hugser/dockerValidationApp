<?php 
        session_start();

        $openTab = $_SESSION[$_GET['action']][$_GET['acc']]['OPEN'];
        $class = array_keys($openTab)[0];
        $stage = array_keys($openTab[$class])[0];
        $dev = $openTab[$class][$stage];
        $date = new DateTime();


        
        $uploadOk = 1;
        //$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        $target_dir = "../uploads/images/";
        $imageFileType = end(explode('.',basename($_FILES["image$filename"]["name"])));
        $fileID = strval($date->getTimestamp()) . '.' . $imageFileType;
        $target_file = $target_dir . $class . '_' . $stage . '_' . $dev . '_' . $fileID;
        //print_r(_SESSION[$_GET['action']][$_GET['acc']]['newSetup'][$class][$stage][$dev]);
        //array_push($_SESSION[$_GET['action']][$_GET['acc']]['newSetup'][$class][$stage][$dev],"file");


        // Check if image file is a actual image or fake image
        if(isset($_POST["Load"])) {
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if($check !== false) {
                //echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
               // echo "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $_SESSION['fail'] = "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["image"]["size"] > 500000) {
           // echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif"  && $imageFileType != "json") {
            //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
           // echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $_SESSION[$_GET['action']][$_GET['acc']]["$stage@$dev@upload@value"] = $fileID;
                $_SESSION[$_GET['action']][$_GET['acc']]["$stage@$dev@upload@error"] = '';

              // if (!in_array('file',$_SESSION[$_GET['action']][$_GET['acc']]['newSetup'][$class][$stage][$dev])) {
              //      array_push($_SESSION[$_GET['action']][$_GET['acc']]['newSetup'][$class][$stage][$dev],"upload");
              //  }
                
                $_SESSION['ok'] = "The file ". htmlspecialchars( basename( $_FILES["image"]["name"])). " has been uploaded.";
            } else {
               // echo "Sorry, there was an error uploading your file.";
            }
        }
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
        

?>