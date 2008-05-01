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
<title>BugTracker v1.0 :: Overzicht ingediende bugs</title>
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
        Op deze pagina vindt u een overzicht van de ingediende bugs. Klik op het id van de bug om 
        een reactie te plaatsen. Een lichtgroene achtergrond betekend dat er ongelezen berichten
        zijn.<br /><br /><hr /><br />
        <table class='overview_bugs'>
            <tr>
                <th>Code</th>
                <th>Status</th>
                <th>Ingediend</th>
                <th>Aanmelder</th>
                <th>Prioriteit</th>
                <th>Opgelost</th>
                <th>Categorie</th>
            </tr>
            <?php
                $bugs = Bug::getAll();
                
                for($i = 0; $i < count($bugs); $i++){
                	$bug = $bugs[$i];
                    $css_text = '';
                    $num_unread_replies = 0;
                    
                    $num_unread_replies = $bug->unreadRepliesFor($user);
                    
                    if($num_unread_replies > 0){
                    	$css_text = ' class="new_replies"';
                    }
                    echo('<tr'.$css_text.'>');
                        echo('<td align="center">');
                            echo('<a href="edit_bug.php?bug_id='.$bug->getID().'">'.$bug->getID().'</a>');
                        echo('</td>');
                        echo('<td>');
                            echo($bug->getStatusDescription());
                        echo('</td>');
                        echo('<td>');
                            echo(date("d-m-Y G:i", $bug->getApplyTS()));
                        echo('</td>');
                        echo('<td>');
                            echo($bug->getAppliedByName());
                        echo('</td>');
                        echo('<td>');
                            echo($bug->getImportanceDescription());
                        echo('</td>');
                        echo('<td>');
                            if( ! is_null($bug->getSolvedTS())){
                                echo(date("d-m-Y G:i", $bug->getSolvedTS()));
                            }else{
                            	echo('---');
                            }
                        echo('</td>');
                        echo('<td>');
                            echo($bug->getCategory()->getName());
                        echo('</td>');
                    echo('</tr>');
                }
            ?>
        </table>
    </div>
</body>
</html>