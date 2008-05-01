<?php
function __autoload($class_name){
    chdir(dirname(__FILE__));
    require_once(realpath('../') .DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $class_name . '.class.php');
}
?>
