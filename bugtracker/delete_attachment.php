<?php
session_start();

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');

$message = "";
$user = null;
$bug = null;

if(is_null($_SESSION['auth'])){
    echo('<meta http-equiv="Refresh" content="0;URL=login.php">');
    exit;
}else{
    $user = unserialize($_SESSION['auth']);
    
    if($user === false){
        echo('<meta http-equiv="Refresh" content="0;URL=login.php">');
        exit;
    }else{
        if( ! $user instanceof User){
            echo('<meta http-equiv="Refresh" content="0;URL=login.php">');
            exit;
        }
    }
}

if( ! isset($_GET['bug_id']) && ! isset($_GET['bug_reply_id'])){
    echo('<meta http-equiv="Refresh" content="0;URL=index.php">');
    exit;
}else{
    if( ! is_null($_GET['bug_id'])){
    	$bug = Bug::findByID($_GET['bug_id']);
        
        if( ! is_null($bug)){
        	try{
        		$bug->removeAttachment();
                echo('<meta http-equiv="Refresh" content="0;URL=edit_bug.php?bug_id='.$GET_['bug_id'].'">');
                exit;
        	}catch(Exception $ex){
        		echo('<html><head><title>Bijlage verwijderen</title></head><body>Bijlage niet verwijderd ('.$ex->getMessage().
                        ')!<a href="edit_bug.php?bug_id='.$GET_['bug_id'].'">Terug</a></body>');
                exit;
        	}
        }
    }elseif(! is_null($_GET['bug_reply_id'])){
    	$bug_reply = BugReply::findByID($_GET['bug-reply_id']);
        
        if( ! is_null($bug_reply)){
        	try{
        		$bug_reply->removeAttachment();
                echo('<meta http-equiv="Refresh" content="0;URL=edit_bug.php?bug_id='.$bug_reply->getBug()->getID().'">');
                exit;
        	}catch(Exception $ex){
        		echo('<html><head><title>Bijlage verwijderen</title></head><body>Bijlage niet verwijderd ('.$ex->getMessage().
                        ')!<a href="edit_bug.php?bug_id='.$bug_reply->getBug()->getID().'">Terug</a></body>');
                exit;
        	}
        }
    }
    
    echo('<meta http-equiv="Refresh" content="0;URL=overview_bugs.php">');
    exit;
}