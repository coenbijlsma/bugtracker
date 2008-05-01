<?php
require_once(realpath('../') . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');

class User {

    private $_name;
    private $_email;
    private $_admin;
    
    function __construct($username, $password) {
        if($this->check($username, $password)){
        	$this->_name = $username;
        }else{
        	throw new Exception('Incorrect username or password.');
        }
    }
    
    private function check($username, $password){
    	$query = "SELECT email, IsAdmin FROM USERS WHERE name = '".$username."' AND password = '".$password."'";
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $r_value = false;
        
        if(!$result){
            throw new Exception('blaat');
        }else{
        	$row = mysql_fetch_assoc($result);
            $email = $row['email'];
            $admin = $row['IsAdmin'];
            
            if( ! is_null($email) && ! is_null($admin)){
            	$this->_email = $email;
                
                if($admin == 0){
                	$this->_admin = false;
                }else{
                	$this->_admin = true;
                }
                $r_value = true;
            }
            
        }
        
        return $r_value;
    }
    
    public function getName(){
    	return $this->_name;
    }
    
    public function getEmail(){
    	return $this->_email;
    }
    
    public function setEmail($email){
    	$query = "UPDATE USERS SET email = '".$email."' WHERE name = '".$this->_name."'";
        $db = DatabaseFactory::getMySQLi();
        
        if( ! $db->query($query)){
        	throw new Exception($db->error);
        }else{
        	$this->_email = $email;
        }
    }
    
    public function isAdmin(){
    	return $this->_admin;
    }
}
?>