<?php
require_once(realpath('../') . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');

class Attachment {
    
    const MAX_UPLOAD_FILE_SIZE = 307200;
    
    private $_id;
    private $_name;
    private $_type;
    private $_size;
    private $_content;
    
    function __construct($name, $type, $size, $id = -1) {
        $this->setName($name);
        $this->setType($type);
        $this->setSize($size);
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
    	if( ! is_string($name)){
    		throw new Exception('The name of the file should be a string.');
    	}
        $this->_name = $name;
    }
    
    public function getType(){
    	return $this->_type;
    }
    
    public function setType($type){
    	if( ! is_string($type)){
    		throw new Exception('The type of the file should be a string, not a(n) ' . gettype($type) . '.');
    	}
        $this->_type = $type;
    }
    
    public function getSize(){
    	return $this->_size;
    }
    
    public function setSize($size){
    	if( ! is_numeric($size)){
    		throw new Exception('The size should be numeric, not a(n) ' .gettype($size). '.');
    	}elseif($size > self::MAX_UPLOAD_FILE_SIZE){
    		throw new Exception('The maximum size of the uploaded file is 300Kb (307200 bytes). You supplied a file of '.$size.' bytes.');
    	}
        $this->_size = $size;
    }
    
    public function getContent(){
    	return $this->_content;
    }
    
    public function setContent($content){
    	$this->_content = $content;
    }
    
    public function persist(){
        $query = "INSERT INTO ATTACHMENTS(name, type, size, content) " .
                 "VALUES('". $this->getName() ."', '". $this->getType() ."', ". $this->getSize() .", '".$this->_content."')";
        
        $db = DatabaseFactory::getMySQLi();
        if($db->query($query)){
        	$this->setID($db->insert_id);
            return true;
        }else{
        	throw new Exception($db->error);
        }
    }
    
    public function delete(){
    	if($this->_id == -1){
            throw new Exception('The '.__CLASS__.' must be inserted before it can be deleted.');
        }
        
        $query = "DELETE FROM ATTACHMENTS WHERE ID = " .$this->_id;
        $db = DatabaseFactory::getMySQLi();
        if($db->query($query)){
        	return true;
        }else{
        	throw new Exception($db->error);
        }
    }
    
    public static function getAll(){
    	$query = "SELECT ID, name, type, size, content FROM ATTACHMENTS";
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $r_data = array();
        
        if($result !== false){
            while($row = mysql_fetch_assoc($result)){
                $att = new Attachment($row['name'], $row['type'], $row['size'], $row['ID']);
                $att->setContent(stripslashes($row['content']));
                $r_data[] = $att;
            }
        }
        
        return $r_data;
    }
    
    public static function findByID($id, $with_content = false){
    	$query = "";
        if($with_content === true){
            $query = "SELECT ID, name, type, size, content FROM ATTACHMENTS WHERE ID = ".$id;
        }else{
        	$query = "SELECT ID, name, type, size, NULL FROM ATTACHMENTS WHERE ID = ".$id;
        }
        
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $att = null;
        
        if($result !== false){
            while($row = mysql_fetch_assoc($result)){
                $att = new Attachment($row['name'], $row['type'], $row['size'], $row['ID']);
                if( ! is_null($row['content'])){
                    $att->setContent($row['content']);
                }
            }
        }
        
        return $att;
    }
}
?>