<?php
session_start();

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'menu.php');

$message = "";
$user = null;

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
    
if(isset($_POST['commit'])){
    try{
        $bug = new Bug(Bug::STATUS_UNREAD, time(), $user->getName(), $user->getEmail(), $_SERVER['REMOTE_ADDR'], $_POST['bug_description'], $_POST['bug_importance']);
        $att = null;
        
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
            	$bug->setAttachment($att);
            }
            
            $bug->persist();
            $message = $message . '<br />Uw bug is succesvol ingediend.';
        }
    }catch(Exception $ex){
    	$message = $message . '<br />' .$ex->getMessage();
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
        Op deze pagina kunt u een nieuwe bug indienen. Zorg ervoor dat de omschrijving duidelijk is, en stuur eventueel een bijlage mee
        (maximaal 300Kb). <br /><br /><hr size="2" /><br />
  
  
        
        <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
            <table class='new_bug'>
                <tr>
                    <td align='right'>Prioriteit</td>
                    <td>
                        <select name='bug_importance'>
                            <option value='0'>Laag</option>
                            <option value='1' selected='selected'>Gemiddeld</option>
                            <option value='2'>Hoog</option>
                            <option value='3'>Kritisch</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align='right' valign='top' >Omschrijving</td>
                    <td>
                        <textarea name="bug_description" rows="10" cols="65"></textarea>
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
                    <td><input name="commit" type="submit" value="Bug indienen"></td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo($message); ?></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>