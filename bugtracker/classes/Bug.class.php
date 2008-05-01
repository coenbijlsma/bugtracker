<?php
require_once(realpath('../') . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');

class Bug {

    const STATUS_UNREAD         = 0;
    const STATUS_READ           = 1;
    const STATUS_IN_PROGRESS    = 2;
    const STATUS_SOLVED         = 3;
    
    const IMPORTANCE_LOW        = 0;
    const IMPORTANCE_MID        = 1;
    const IMPORTANCE_HIGH       = 2;
    const IMPORTANCE_CRITICAL   = 3; 
     
    private $_id;
    private $_status;
    private $_apply_ts;
    private $_applied_by_name;
    private $_applied_by_email;
    private $_applied_by_ip;
    private $_description;
    private $_importance;
    private $_solvedTS;
    private $_category;
    private $_attachment;
    
    function __construct($status, $apply_ts, $apply_by_name, $apply_by_email, $apply_by_ip, $description, $importance, $id = -1) {
        $this->setStatus($status);
        $this->setApplyTS($apply_ts);
        $this->setAppliedByName($apply_by_name);
        $this->setAppliedByEmail($apply_by_email);
        $this->setAppliedByIP($apply_by_ip);
        $this->setDescription($description);
        $this->setImportance($importance);
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
    
    public function getStatus(){
    	return $this->_status;
    }
    
    public function getStatusDescription($st = -1){
        $switch = null;
        
    	if($st == -1){
    	   $switch = $this->_status;	
    	}else{
    		$switch = $st;
    	}
        switch($switch){
    		case self::STATUS_UNREAD: return 'Ongelezen';
            case self::STATUS_READ: return 'Gelezen';
            case self::STATUS_IN_PROGRESS: return 'In behandeling';
            case self::STATUS_SOLVED: return 'Opgelost';
    	}
    }
    
    public function setStatus($status){
    	if( ! is_numeric($status)){
    		throw new Exception('The status must be numeric.');
    	}elseif($status < self::STATUS_UNREAD || $status > self::STATUS_SOLVED){
    		throw new Exception('The status must be between 0 and 3.');
    	}
        $this->_status = $status;
    }
    
    public function getApplyTS(){
    	return $this->_apply_ts;
    }
    
    private function setApplyTS($apply_ts){
    	$this->_apply_ts = $apply_ts;
    }
    
    public function getAppliedByName(){
    	return $this->_applied_by_name;
    }
    
    private function setAppliedByName($name){
    	$this->_applied_by_name = $name;
    }
    
    public function getAppliedByEmail(){
    	return $this->_applied_by_email;
    } 
    
    private function setAppliedByEmail($email){
    	$this->_applied_by_email = $email;
    }
    
    public function getAppliedByIP(){
    	return $this->_applied_by_ip;
    }
    
    private function setAppliedByIP($ip){
    	$this->_applied_by_ip = $ip;
    }
    
    public function getDescription(){
    	return $this->_description;
    }
    
    public function setDescription($desc){
    	$this->_description = $desc;
    }
    
    public function getImportance(){
    	return $this->_importance;
    }
    
    public function getImportanceDescription($imp = -1){
    	$switch = null;
        
        if($imp == -1){
        	$switch = $this->_importance;
        }else{
        	$switch = $imp;
        }
        
        switch($switch){
    		case self::IMPORTANCE_LOW: return 'Laag';
            case self::IMPORTANCE_MID: return 'Gemiddeld';
            case self::IMPORTANCE_HIGH: return 'Hoog';
            case self::IMPORTANCE_CRITICAL: return 'Kritisch';
    	}
    }
    
    public function setImportance($importance){
    	if($importance < self::IMPORTANCE_LOW || $importance > self::IMPORTANCE_CRITICAL){
            throw new Exception('The importance must be between 0 and 3.');
        }
        $this->_importance = $importance;
    }
    
    public function getSolvedTS(){
    	return $this->_solvedTS;
    }
    
    public function setSolvedTS($ts){
    	$this->_solvedTS = $ts;
    }
    
    public function getCategory(){
    	return $this->_category;
    }
    
    public function setCategory(Category $category){
    	$this->_category = $category;
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
    
    public function unreadRepliesFor(User $user){
    	$query = "SELECT COUNT(*) FROM BUG_REPLIES WHERE BUG_ID = ".$this->_id." ".
                "AND ID NOT IN (SELECT BUG_REPLY_ID FROM USER_READS WHERE USER_NAME = '".$user->getName()."')";
        
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        
        if($result !== false){
        	$row = mysql_fetch_array($result);
            return $row[0];
        }else{
        	return 0;
        }
    }
    
    public function setRepliesReadFor(User $user){
    	$query = "INSERT INTO USER_READS(BUG_REPLY_ID, USER_NAME) ".
                "SELECT ID, '".$user->getName()."' FROM BUG_REPLIES WHERE BUG_ID = ".$this->_id." ".
                "AND ID NOT IN (SELECT BUG_REPLY_ID FROM USER_READS WHERE USER_NAME = '".$user->getName()."')";
        $db = DatabaseFactory::getMySQLi();
        return $db->query($query);
    }
    
    public function persist(){
    	if(is_null($this->_category)){
            $cat = Category::findByName('STD');
            
            if(is_null($cat)){
            	$cat = new Category('STD');
                $cat->persist();
            }
            $this->setCategory($cat);
        }
        
        if($this->_category->getID() == -1){
    		$this->_category->persist();
    	}
        if( ! is_null($this->_attachment)){
        	if($this->_attachment->getID() == -1){
        		$this->_attachment->persist();
        	}
        }
        
        $att_id = is_null($this->_attachment) ? "NULL" : $this->_attachment->getID();
        
        $query = "INSERT INTO BUGS(status, applyTS, appliedByName, appliedByEmail, appliedByIP, description, importance, CATEGORY_ID, ATTACHMENT_ID) ".
                "VALUES(".$this->_status.", FROM_UNIXTIME(".$this->_apply_ts."), '".$this->_applied_by_name.
                "', '".$this->_applied_by_email."', '".$this->_applied_by_ip."'".", '".$this->_description.
                "', ".$this->_importance.", ".$this->_category->getID().", ".$att_id.")";
        
        $db = DatabaseFactory::getMySQLi();
        if($db->query($query)){
            $this->setID($db->insert_id);
            $mail = new Email('Coen Bijlsma', 'coenbijlsma@gmail.com', 'coenbijlsma@gmail.com', 'Nieuwe bug aangemaakt', 'Aangemaakt door '.$this->_applied_by_name."\n".'Bericht:'."\n".$this->_description);
            
            if( ! $mail->send()){
                throw new Exception('Mail not sent!');
            }
            return true;
        }else{
            throw new Exception($db->error);
        }
    }
    
    public function update(){
    	$query = "";
        
        if($this->_status == self::STATUS_SOLVED){
        	$this->_solvedTS = time();
            $query = "UPDATE BUGS SET status = ".$this->_status.", solvedTS = FROM_UNIXTIME(".$this->_solvedTS.") WHERE ID = ".$this->_id;
        }else{
        	$query = "UPDATE BUGS SET status = ".$this->_status." WHERE ID = ".$this->_id;
        }
        
        $db = DatabaseFactory::getMySQLi();
        if($db->query($query)){
        	return true;
        }else{
        	throw new Exception($db->error);
        }
        
    }
    
    public function delete(){
    	
    }
    
    public static function getAll(){
    	//function __construct($status, $apply_ts, $apply_by_name, $apply_by_email, $apply_by_ip, $description, $importance, $id = -1) {
        $query = "SELECT ID, status, UNIX_TIMESTAMP(applyTS) as aTS, appliedByName, appliedByEmail, appliedByIP, description, importance, ".
                "UNIX_TIMESTAMP(solvedTS) as sTS, CATEGORY_ID, ATTACHMENT_ID ".
                "FROM BUGS ".
                "ORDER BY status, importance DESC, aTS";
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $r_result = array();
        
        if($result !== false){
            while($row = mysql_fetch_assoc($result)){
                $bug = new Bug($row['status'], $row['aTS'], $row['appliedByName'], $row['appliedByEmail'], $row['appliedByIP'], $row['description'], $row['importance'], $row['ID']);
                $cat = Category::findByID($row['CATEGORY_ID']);
                $bug->setCategory($cat);
                if( ! is_null($row['ATTACHMENT_ID'])){
                	$att = Attachment::findByID($row['ATTACHMENT_ID']);
                    $bug->setAttachment($att);
                }
                $r_result[] = $bug;
            }
        }
        
        return $r_result;
    }
    
    public static function findByID($id){
    	$query = "SELECT ID, status, UNIX_TIMESTAMP(applyTS) as aTS, appliedByName, appliedByEmail, appliedByIP, description, importance, UNIX_TIMESTAMP(solvedTS) as sTS, CATEGORY_ID, ATTACHMENT_ID ".
                "FROM BUGS WHERE ID = ".$id;
        $db = DatabaseFactory::getMySQLi();
        $result = $db->query($query);
        $bug = null;
        
        if($result !== false){
            while($row = mysql_fetch_assoc($result)){
                $bug = new Bug($row['status'], $row['aTS'], $row['appliedByName'], $row['appliedByEmail'], $row['appliedByIP'], $row['description'], $row['importance'], $row['ID']);
                $cat = Category::findByID($row['CATEGORY_ID']);
                $bug->setCategory($cat);
                if( ! is_null($row['ATTACHMENT_ID'])){
                    $att = Attachment::findByID($row['ATTACHMENT_ID']);
                    $bug->setAttachment($att);
                }
            }
        }
        
        return $bug;
    }
}
?>