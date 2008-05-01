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

if(isset($_POST['submit_email'])){
	try{
		$user->setEmail($_POST['user_email']);
        $_SESSION['auth'] = serialize($user);
        $message = "Email met succes gewijzigd.";
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
        Op deze pagina kunt u uw profiel bijwerken.<br /><br /><hr /><br />
        
        <form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">
            <table class='overview_profile'>
                <tr>
                    <td>Email adres:</td>
                    <td><input type="text" name="user_email" value="<?php echo($user->getEmail()); ?>" size="40" maxlength="128"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="submit_email" value="Wijzigen"/></td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo($message); ?></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>