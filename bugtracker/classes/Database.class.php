<?php
require_once(realpath('../') . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');

class Database {
    
    private $_mysql_link = null;
    private $_host;
    private $_username;
    private $_password;
    private $_database;
    
    function __construct($host, $username, $password, $database) {
        $this->_mysql_link = mysql_connect($host, $username, $password);
        
        if(!$this->_mysql_link){
        	$this->_mysql_link = null;
            throw new Exception('Could not connect to database!');
        }else{
        	if(!mysql_select_db($database, $this->_mysql_link)){
        		throw new Exception('Could not select database!');
        	}
        }
    }
    
    function __destruct(){
    	if(!is_null($this->_mysql_link)){
            mysql_close($this->_mysql_link);
        }
    }
    
    public function query($query){
        return mysql_query($query, $this->_mysql_link);
    }
    
    public function __get($var_name){
    	switch($var_name){
    		case 'insert_id': return mysql_insert_id($this->_mysql_link); break;
            case 'error': return mysql_error($this->_mysql_link); break;
            default: throw new Exception('No such variable in '.__CLASS__);       
    	}
    }
}
?>