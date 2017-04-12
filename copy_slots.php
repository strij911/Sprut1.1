<?PHP
	include "./config.inc.php";

	if (get_userid() > 1) $sql = "and group_id in (select id from sprut_group where group_admin_id='".get_userid()."')";

	if (isset($_GET['gid']) && !empty($_GET['gid'])) $sql_groups = "and group_id=".$_GET['gid'];
	if (isset($_GET['from'])) $date = $_GET['from']; else $date = date("Y-m-d");
	
	$m = $db->query("SELECT * from rabbit_posts where link not like 'https://vk.com%' ".$sql_groups." and datestamp>='".$date." 00:00:00' and datestamp<='".$date." 23:59:59' ".$sql);
	
	while($list = $m->fetch(PDO::FETCH_ASSOC)) {
		$x = end(explode(' ', $list['datestamp']));
		$sql = "INSERT INTO rabbit_posts (link, text, picture, group_id, myword, group_name, datestamp, deleted) VALUES ('".$list['link']."', '".$list['text']."', '".$list['picture']."', ".$list['group_id'].", '".$list['myword']."', '".$list['group_name']."', '".$_GET['d']." ".$x."', 0)";
		$mx = $db->query($sql);
	}		

	log_event("Слоты пользователя ".get_userid()." скопированы на ".$_GET['d']);
	header("Location: promo.php");
