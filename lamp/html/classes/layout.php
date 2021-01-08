<?php

class layout {

    function __construct() {

    }
    function __destruct() {

    }

    function is_selected($value){
        if ($_GET['tab']==$value) return "selected";
    }
    
    function createInputTabs($list_of_tab) {
        $tabs = '';
        $tabs .= "<ul>";
        for ($i=1; $i < sizeof($list_of_tab)+1;$i++) {
            $tabs .= "<li class=".$this->is_selected($i)." ><a href='setup.html.php?tab=".$i."'>".
            htmlentities($list_of_tab[$i-1])."</a>";
            if($_GET['tab']==$i) {
                $tabs .= "<button class='remove_single_button' type='submit' name='tab".$_GET['tab']."'>
                        <i class='fa fa-eject' style='font-size:18px;'></i>
                     </button>
                     <button class='run_single_button' type='submit'>
                        <i class='fa fa-repeat' style='font-size:18px;'></i>
                     </button>
                     <button class='submit_single_button' type='submit'>
                     <i class='fa fa-share' style='font-size:18px;'></i>
                     </button>";
            }
        }
        $tabs .= "</ul>"; 
        
        return $tabs;
    }
}

?>