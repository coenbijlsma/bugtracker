<?php
require_once(realpath('../') . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');

class BugReply {

    private $_id;
    private $_bug;
    private $_reply_ts;
    private $_reply_by_name;
    private $_reply_by_email;
    private $_reply_by_ip;
    private $_reply;
    private $_attachment = null;
    
    function __construct(Bug $bug, $reply_ts, $reply_by_name, $reply_by_email, $reply_by_ip, $reply, $id = -1) {
        $this->setBug($bug);
        $this->setReplyTS($reply_ts);
        $this->setReplyByName($reply_by_name);
        $this->setReplyByEmail($reply_by_email);
        $this->setReplyByIP($reply_by_ip);
        $this->setReply($reply);
        $this->setID($id);
    }
    
    public function getID(){
    	return $this->_id;
    }
    
    private function setID($id){
    	if( ! is_numeric($id)){
    		throw new Exception('The id must be numeric.');
    	}
        $this->_id = $id;
    }
    
    public function getBug(){
    	return $this->_bug;
    }
    
    private function setBug(Bug $bug){
    	$this->_bug = $bug;
    }
    
    public function getReplyTS(){
    	return $this->_reply_ts;
    }
    
    private function setReplyTS($reply_ts){
    	$this->_reply_ts = $reply_ts;
    }
    
    public function getReplyByName(){
    	return $this->_reply_by_name;
    }
    
    private function setReplyByName($name){
    	$this->_reply_by_name = $name;
    }
    
    public function getReplyByEmail(){
    	return $this->_reply_by_email;
    }
    
    private function setReplyByEmail($email){
    	$this->_reply_by_email = $email;
    }
    
    public function getReplyByIP(){
    	return $this->_reply_by_ip;
    }
    
    private function setReplyByIP($ip){
    	$this->_reply_by_ip = $ip;
    }
    
    public function getReply(){
    	return $this->_reply;
    }
    
    public function setReply($reply){
    	$this->_reply = $reply;
    }
    
    public function getAttachment(){
    	return $this->_attachment;
    }
    
    public function setAttachment(Attachment $attachment){
    	$this->_attachment = $attachment;
    }
    
    public function removeAttachment(){
    	try{
            $this->_attachment->delete();
            $this->_attachment = null;
        }catch(Exception $ex){
        	throw $ex;
        }
    }
    
    public function persist(){
    	if( ! is_null($this->_attachment)){
    		if($this->_attachment->getID() == -1){
    			$this->_attachment->persist();
    		}
    	}
        if($this->_bug->getID() == -1){
        	throw new Exception('A bug must first be saved before you can repy to it.');
        }
        
        $att_id = is_null($this->_attachment) ? "NULL" : $this->_attachment->getID();
        
        $query = "INSERT INTO BUG_REPLIES(BUG_ID, replyTS, replyByName, replyByIP, reply, ATTACHMENT_ID) ".
                 "VALUES(".$this->_bug->getID().", FROM_UNIXTIME(".$this->_reply_ts."), '".$this->_reply_by_name.
                        "', '".$this->_reply_by_ip."', '".$this->_reply."', ".$att_id.")";
        
        $db = DatabaseFactory::getMySQLi();
        if($db->query($query)){
            $this->setID($db->insert_id);
            return true;
        }else{
            throw new Exception($db->error);
        }
        
    }
    
    public function update(){
    	
    }
    
    public function delete(){
    	if( ! is_null($this->_attachment)){
            if($this->_attachment->getID() > -1){
                $this->_attachment->delete();
            }
        }
        
        $query = "DELETE FROM BUG_REPLIES WHERE ID = ".$this->_id;
    }
    
    public static function getAll(){
    	$query = "SELECT ID, BUG_ID, UNIX_TIMESTAMP(replyTS) AS rTS, replyByName, replyByEmail, replyByIP, reply, ATTACHMENT_ID FROM BUG_REPLIES";
        
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $r_result = array();
        
        if($result !== false){
            while($row = mysql_fetch_assoc($result)){
                $bug = Bug::findByID($row['BUG_ID']);
                $bug_reply = new BugReply($bug, $row['rTS'], $row['replyByName'], $row['replyByEmail'], $row['replyByIP'], $row['reply'], $row['ID']);
                
                if( ! is_null($row['ATTACHMENT_ID'])){
                    $att = Attachment::findByID($row['ATTACHMENT_ID']);
                    $bug_reply->setAttachment($att);
                }
                $r_result[] = $bug_reply;
            }
        }
        
        return $r_result;        
    }
    
    public static function findAllByBugID($id){
        $query = "SELECT ID, BUG_ID, UNIX_TIMESTAMP(replyTS) AS rTS, replyByName, replyByEmail, replyByIP, reply, ATTACHMENT_ID FROM BUG_REPLIES ".
                "WHERE BUG_ID = ".$id;
        
        $bug = Bug::findByID($id);
        
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $r_result = array();
        
        if($result !== false){
            while($row = mysql_fetch_assoc($result)){
                $bug_reply = new BugReply($bug, $row['rTS'], $row['replyByName'], $row['replyByEmail'], $row['replyByIP'], $row['reply'], $row['ID']);
                
                if( ! is_null($row['ATTACHMENT_ID'])){
                    $att = Attachment::findByID($row['ATTACHMENT_ID']);
                    $bug_reply->setAttachment($att);
                }
                $r_result[] = $bug_reply;
            }
        }
        
        return $r_result;        
    }
    
    public static function findByID($id){
    	$query = "SELECT ID, BUG_ID, UNIX_TIMESTAMP(replyTS) AS rTS, replyByName, replyByEmail, replyByIP, reply, ATTACHMENT_ID FROM BUG_REPLIES ".
                 "WHERE ID = ".$id;
        
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $bug_reply = null;
        
        if($result !== false){
            while($row = mysql_fetch_assoc($result)){
                $bug = Bug::findByID($row['BUG_ID']);
                $bug_reply = new BugReply($bug, $row['rTS'], $row['replyByName'], $row['replyByEmail'], $row['replyByIP'], $row['reply'], $row['ID']);
                
                if( ! is_null($row['ATTACHMENT_ID'])){
                    $att = Attachment::findByID($row['ATTACHMENT_ID']);
                    $bug_reply->setAttachment($att);
                }
            }
        }
        
        return $bug_reply;  
    }
}
?>