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

if( ! isset($_GET['att_id'])){
    echo('<meta http-equiv="Refresh" content="0;URL=index.php">');
    exit;
}else{
    $att = Attachment::findByID($_GET['att_id'], true);
    
    if( ! is_null($att)){
    	if($att instanceof Attachment){
            header("Content-length: ".$att->getSize());
            header("Content-type: ",$att->getType());
            header("Content-disposition: attachment; filename=".$att->getName());
            echo($att->getContent());
        }
    }
    exit;
}