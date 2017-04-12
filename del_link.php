<?PHP
	include "./config.inc.php";

	$over = "";
	
    if (!empty($_GET['pid'])) {
	
		$m = $db->query("select product_id from urls where id=".$_GET['pid']);	
		$list = $m->fetch(PDO::FETCH_ASSOC);
		
		$m = $db->query("DELETE FROM urls where id=".$_GET['pid']);
		
		log_event("Ссылка ".$list['product_id'].':'.$_GET['pid']." успешно удалёна!");
		header('Location: products.php?pid='.$list['product_id'].'#active');		
	} //if	
	
	header('Content-Type: text/html; charset=utf-8');
?>

