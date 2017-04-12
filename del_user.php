<?PHP

	include "./config.inc.php";

    if (!empty($_GET['pid'])) {
		$m = $db->query("delete from users WHERE id=".$_POST['pid']);
		log_event("Пользователь ".$_POST['login']." удален!");
		header('Location: users.php');	
	} //if	
	
?>

