<?PHP
	include "./config.inc.php";
		
	if (isset($_GET['id']) && isset($_GET['p'])) {
		$m = $db->query("select picture from rabbit_posts WHERE id=".$_GET['id']);
		$row = $m->fetch(PDO::FETCH_ASSOC);
		$picture = str_replace($_GET['p'].';', '', $row['picture']);
		$m = $db->query("UPDATE rabbit_posts SET picture='".$picture."' WHERE id=".$_GET['id']);
		$m = $db->query("UPDATE posts SET picture='".$picture."' WHERE id=".$_GET['id']);		
	}
	header("Location: poster_edit_rb.php?edit=".$_GET['id']."&groups=".urlencode($_GET['groups']));

