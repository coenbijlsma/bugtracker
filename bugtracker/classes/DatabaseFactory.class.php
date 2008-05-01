<?php
require_once(realpath('../') . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');

class DatabaseFactory {

    private static $_instance = null;
    
    private function __construct() {

    }
    
    public static function getMySQLi(){
    	if(is_null(self::$_instance)){
    		self::$_instance = new Database('localhost', 'root', 'fietsrekbureaustoel', 'bugtracker');
    	}
        return self::$_instance;
    }
}
?>