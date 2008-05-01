<?php
session_start();

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'menu.php');

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

if( ! isset($_GET['bug_id'])){
	echo('<meta http-equiv="Refresh" content="0;URL=overview_bugs.php">');
    exit;
}else{
	$bug = Bug::findByID($_GET['bug_id']);
    $bug->setRepliesReadFor($user);
}
    
if(isset($_POST['commit'])){
    try{
        $att = null;
        //function __construct(Bug $bug, $reply_ts, $reply_by_name, $reply_by_email, $reply_by_ip, $reply, $id = -1) {
        $reply = new BugReply($bug, time(), $user->getName(), $user->getEmail(), $_SERVER['REMOTE_ADDR'], $_POST['reply_description']);
        
        if(isset($_FILES['attachment'])){
            if($_FILES['attachment']['size'] > 0){
                $fileName = $_FILES['attachment']['name'];
                $tmpName  = $_FILES['attachment']['tmp_name'];
                $fileSize = $_FILES['attachment']['size'];
                $fileType = $_FILES['attachment']['type'];
                
                $fp      = fopen($tmpName, 'r');
                $content = fread($fp, filesize($tmpName));
                $content = addslashes($content);
                fclose($fp);
                
                if(!get_magic_quotes_gpc()){
                   // $fileName = addslashes($fileName);
                }
            
                
                $att = new Attachment($fileName, $fileType, $fileSize, -1);
                $att->setContent($content);
                $att->persist();
            }else{
                if(strlen($_FILES['attachment']['name']) > 0){
                    $message = 'De bijlage is niet geupload. Misschien is het bestand te groot? (max 300Kb)';
                }
            }
            
            if( ! is_null($att)){
                $reply->setAttachment($att);
            }    
        }
        
        $reply->persist();
        $message = $message . '<br />Uw reply is succesvol opgeslagen.';
    }catch(Exception $ex){
        $message = $message . '<br />' .$ex->getMessage();
    }
}elseif(isset($_POST['change_status'])){
	$status = $_POST['bug_status'];
    
    try{
        $bug->setStatus($status);
        $bug->update();
    }catch(Exception $ex){
    	$message = $ex->getMessage();
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!--
<link rel="stylesheet" type="text/css" href="css/measures.css">
<link rel="stylesheet" type="text/css" href="css/colors.css">
-->
<link rel="stylesheet" type="text/css" href="css/style_loggedin.css">
<title>BugTracker v1.0 :: Nieuwe bug indienen</title>
</head>
<body>
    <!-- Div voor het hoofdmenu aan de bovenkant van de site -->
    <div class='menu' id='top' >
        <table align='center'>
            <tr>
                <td>
                    <!-- Plaatje? -->
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Div voor het submenu aan de linkerkant van de site -->
    <?php
        showMenu();
    ?>
    
    <!-- Div voor de informatie over het beheerders-gedeelte -->
    <div class='content' id='center'>
        Op deze pagina kunt u een nieuwe bug bijwerken of een reply plaatsen. 
        Zorg dat een eventuele reply duidelijk is, en stuur eventueel een bijlage mee (maximaal 300Kb).
        <br /><br /><hr size="2" /><br />

        <table class='existing_bug'>
            <tr>
                <td align='right'>Prioriteit</td>
                <td>
                    <select name='bug_importance' disabled='disabled'>
                        <?php
                            $bug_importance = $bug->getImportance();
                            for($i = 0; $i < 4; $i++){
                            	if($i == $bug_importance){
                            		echo('<option value="'.$i.'" selected="selected">'.$bug->getImportanceDescription($i).'</option>');
                            	}else{
                            		echo('<option value="'.$i.'">'.$bug->getImportanceDescription($i).'</option>');
                            	}
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td align='right'>Ingediend door</td>
                <td><input name="applied_by_name" size="20" disabled="disabled" value="<?php echo($bug->getAppliedByName()); ?>"></td>  
            </tr>
            <tr>
                <td align='right'>Ingediend op</td>
                <td><input name="apply_ts" size="20" disabled="disabled" value="<?php echo(date("d-m-Y G:i", $bug->getApplyTS())); ?>"></td>
            </tr>
            
            <!-- form voor het bijwerken van de bugstatus -->
            <form action="<?php echo($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
                <tr>
                    <td align='right'>Status</td>
                    <td>
                        <select name="bug_status" <?php if( ! $user->isAdmin()){ echo('disabled="disabled"'); } ?>>
                            <?php
                                $bug_status = $bug->getStatus();
                                for($i = 0; $i < 4; $i++){
                                	if($i == $bug_status){
                                		echo('<option value="'.$i.'" selected="selected">'.$bug->getStatusDescription($i).'</option>');
                                	}else{
                                		echo('<option value="'.$i.'">'.$bug->getStatusDescription($i).'</option>');
                                	}
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align='right' valign='top' >Omschrijving</td>
                    <td>
                        <textarea name="bug_description" rows="10" cols="65" disabled="disabled"><?php echo($bug->getDescription()); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td align='right'>Bijlage</td>
                    <?php
                        $att = $bug->getAttachment();
                        $att_text = "";
                        
                        if( ! is_null($att)){
                        	$att_text = '<a href="download_attachment.php?att_id='.$att->getID().'" target="_blank">Downloaden</a>';
                        }else{
                        	$att_text = 'Geen bijlage.';
                        }
                    ?>
                    <td><?php echo($att_text); ?></td>
                </tr>
                <?php
                    if($user->isAdmin()){
                    	echo('<tr>');
                            echo('<td>&nbsp;</td>');
                            echo('<td><input name="change_status" type="submit" value="Bugstatus wijzigen"></td>');
                        echo('</tr>');
                    } 
                ?>
            </form>
            <?php
                
                $replies = BugReply::findAllByBugID($bug->getID());
                
                for($i = 0; $i < count($replies); $i++){
                	$reply = $replies[$i];
                    $att = $reply->getAttachment();
                    $att_text = "";
                    
                    if( ! is_null($att)){
                    	$att_text = ' <a href="download_attachment.php?att_id='.$att->getID().'" target="_blank">Bijlage downloaden</a>';
                    }
                    echo('<tr>');
                        echo('<td>&nbsp;</td>');
                        echo('<td><hr /></td>');
                    echo('</tr>');
                    echo('<tr>');
                        echo('<td>&nbsp</td>');
                        echo('<td>Reply van '.$reply->getReplyByName().' @ '.date("d-m-Y G:i", $reply->getReplyTS()). $att_text .'</td>');
                    echo('</tr>');
                    echo('<tr>');
                        echo('<td>&nbsp;</td>');
                        echo('<td><pre>'.$reply->getReply().'</pre></td>');
                    echo('</tr>');
                } 
            ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            
            <form action="<?php echo($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
                <tr>
                    <td align='right' valign='top'>Nieuwe reactie</td>
                    <td>
                        <textarea name="reply_description" rows="10" cols="65"></textarea>
                    </td>
                </tr>
                <tr>
                    <td align='right'>Bijlage</td>
                    <td>
                        <input type="hidden" name="MAX_FILE_SIZE" value="307200" />
                        <input name="attachment" type="file" />
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align='right'><input name="commit" type="submit" value="Reactie opslaan"></td>
                </tr>
            </form>
            
            <tr>
                <td colspan="2"><?php echo($message); ?></td>
            </tr>
        </table>
    </div>
</body>
</html>