<?php
if (!function_exists('dd')) {
    function dd($arg) {
        if (is_string($arg)) {
            
            
            if ($arg !== '') {
                echo $arg;
            }else{
                echo "value is empty";
            }
            die();
        } elseif (is_array($arg)) {
            echo "<pre>";
            print_r($arg);
            die();
        } elseif (is_object($arg)) {
            var_dump($arg);
            die();
        }elseif (is_numeric($arg)) {
            echo $arg;
            die();
        }else{
            echo "<br/>";
            die("STEM LEARNING");
        }
    }
}
?>