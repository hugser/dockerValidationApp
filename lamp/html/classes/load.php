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

}