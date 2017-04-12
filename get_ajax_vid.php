<?PHP
	include "./config.inc.php";

	if (get_userid()>1) $usr = "and group_id in (select id from sprut_group where group_admin_id='".get_userid()."')";
	
	if (!empty($_GET['slot']) && strstr($_GET['act'], 'warning')) {
		$txt = trim($_GET['p']);
		
		if (strlen($txt)>0) $txt_id = $txt;
		
		$texts = $db->query("SELECT text from texts where id=".$txt_id);
		$txt = $texts->fetch(PDO::FETCH_ASSOC);

		if (get_userid()>1) $sx = "group_admin_id='".get_userid()."' and ";
			
		$groups = $db->query("SELECT myworld_group, name from sprut_group where $sx id=".$_GET['group_id']);
		$group = $groups->fetch(PDO::FETCH_ASSOC);

		$sql = "INSERT INTO rabbit_posts (link, text, picture, group_id, myword, group_name, datestamp, deleted) VALUES ('video', '".$txt['text']."', '', ".$_GET['group_id'].", '".$group['myworld_group']."', '".$group['name']."', '".$_GET['slot']."', 0)";
		if (!empty($group['name'])) $m = $db->query($sql);
		log_event($sql);
	}
	
	if (!empty($_GET['slot']) && strstr($_GET['act'], 'info')) {
		$sql = "DELETE from rabbit_posts where datestamp='".$_GET['slot']."' and group_id=".$_GET['group_id']." ".$usr;
		$m = $db->query($sql);
		log_event($sql);	
	}
	