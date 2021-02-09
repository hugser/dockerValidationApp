<?php

class load {
    public function __construct() {
        
    }
   
    function __destruct() {
       
    }

    public function JSON($filePath) {
     //   $file_ext = explode('.',$name);
      //  if (in_array($file_ext,'json') === false) {
      //      echo "extension not allowed, please choose a JSON file.";
      //  } else {
            return json_decode(file_get_contents($filePath), true);
       // }
    }

    public function uploadImage($filename) {
        $target_dir = "../uploads/images/";
        $target_file = $target_dir . basename($_FILES["image$filename"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["image$filename"]["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["image$filename"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["image$filename"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["image$filename"]["name"])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

}