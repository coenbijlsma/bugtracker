<?php
session_start();

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'menu.php');

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
<title>BugTracker v1.0</title>
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
        Welkom, <?php echo($user->getName()); ?>
    </div>
</body>
</html>