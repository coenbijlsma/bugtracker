<?php
require_once(realpath('../') . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');

class Category {

    private $_id;
    private $_name;
    
    function __construct($name, $id = -1) {
        $this->setName($name);
        $this->setID($id);
    }
    
    public function getID(){
    	return $this->_id;
    }
    
    private function setID($id){
        $this->_id = $id;
    }
    
    public function getName(){
    	return $this->_name;
    }
    
    public function setName($name){
    	if (! is_string($name)){
            throw new Exception('The name must exist out of text.');
        }
        $this->_name = $name;
    }
    
    public function persist(){
    	$query = "INSERT INTO CATEGORIES(name) VALUES('" . $this->getName() . "')";
        $db = DatabaseFactory::getMySQLi();
        if($db->query($query)){
            $this->setID($db->insert_id);
            return true;
        }else{
            throw new Exception($db->error);
        }
    }
    
    public function update(){
    	if($this->_id == -1){
    		throw new Exception('The '.__CLASS__.' must be inserted before it can be updated.');
    	}
        
        $query = "UPDATE CATEGORIES SET name = '" . $this->getName() . "' WHERE ID = " . $this->getID();
        $db = DatabaseFactory::getMySQLi();
        if($db->query($query)){
            return true;
        }else{
        	throw new Exception($db->error);
        }
    }
    
    public function delete(){
    	if($this->_id == -1){
            throw new Exception('The '.__CLASS__.' must be inserted before it can be deleted.');
        }
        
        $query = "DELETE FROM CATEGORIES WHERE ID = " . $this->getID();
        $db = DatabaseFactory::getMySQLi();
        if($db->query($query)){
        	return true;
        }else{
        	throw new Exception($db->error);
        }
    }
    
    public static function getAll(){
    	$query = "SELECT ID, name FROM CATEGORIES";
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $r_data = array();
        
        if($result !== false){
        	while($row = mysql_fetch_assoc($result)){
        		$cat = new Category($row['name'], (int)$row['ID']);
                $r_data[] = $cat;
        	}
        }
        
        return $r_data;
    }
    
    public static function findByName($name){
    	$query = "SELECT ID, name FROM CATEGORIES WHERE name = '".$name."' LIMIT 1";
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $cat = null;
        
        if($result !== false){
            while($row = mysql_fetch_assoc($result)){
                $cat = new Category($row['name'], (int)$row['ID']);
            }
        }
        
        return $cat;
    }
    
    public static function findByID($id){
    	$query = "SELECT ID, name FROM CATEGORIES WHERE ID = '".$id."'";
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $cat = null;
        
        if($result !== false){
            while($row = mysql_fetch_assoc($result)){
                $cat = new Category($row['name'], (int)$row['ID']);
            }
        }
        
        return $cat;
    }
}
?>