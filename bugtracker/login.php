<?php
session_start();

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');

$message = "";

if( ! is_null($_SESSION['auth'])){
    $user = unserialize($_SESSION['auth']);
    
    if($user !== false){
        if($user instanceof User){
            echo('<meta http-equiv="Refresh" content="0;URL=index.php">');
            exit;
        }
    }
}

if(isset($_POST['login'])){
	try{
		$user = new User($_POST['username'], $_POST['password']);
        $_SESSION['auth'] = serialize($user);
        echo('<meta http-equiv="Refresh" content="0;URL=index.php">');
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
<link rel="stylesheet" type="text/css" href="css/style.css">
<title>BugTracker v1.0</title>
</head>
<body>
  <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" enctype="application/x-www-form-urlencoded">
        <div class='login'>
            <table align='center'>
                <tr class='login_info'>
                    <td colspan="2" align='center'>INLOGGEN</td>
                </tr>
                <tr class='login_user_data'>
                    <td>Gebruikersnaam:</td>
                    <td><input type="text" name="username" size="20" maxlength="20"/></td>
                </tr>
                <tr class='login_user_data'>
                    <td>Wachtwoord:</td>
                    <td><input type="password" name="password" size="20" maxlength="20"/></td>
                </tr>
                <tr class='login_user_data'>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="login" value="Aanmelden"/></td>
                </tr>
                <tr class='login_user_error'>
                    <td colspan="2"><b><?php echo($message); ?></b></td>
                </tr>
            </table>
        </div>
    </form>
</body>
</html>