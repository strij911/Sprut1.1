<?PHP
	include "./config.inc.php";

	if (get_userid()>1) $usr = "and group_id in (select id from sprut_group where group_admin_id='".get_userid()."')";
	
	if (!empty($_GET['slot']) && strstr($_GET['act'], 'warning')) {
		$x = explode("|", $_GET['p']);
		$pic = $x[0];
		$url = $x[1];
		$txt = $x[2];
		
		if (strlen($pic)>0) $pic_id = $pic;
		if (strlen($url)>0) $url_id = $url;
		if (strlen($txt)>0) $txt_id = $txt;
		
		$pics = $db->query("SELECT picture from pictures where id=".$pic_id);
		$picture = $pics->fetch(PDO::FETCH_ASSOC);

		$urls = $db->query("SELECT url from urls where id=".$url_id);
		$url = $urls->fetch(PDO::FETCH_ASSOC);

		$texts = $db->query("SELECT text from texts where id=".$txt_id);
		$txt = $texts->fetch(PDO::FETCH_ASSOC);

		if (get_userid()>1) $sx = "group_admin_id='".get_userid()."' and ";
			
		$groups = $db->query("SELECT myworld_group, name from sprut_group where $sx id=".$_GET['group_id']);
		$group = $groups->fetch(PDO::FETCH_ASSOC);

		$sql = "INSERT INTO rabbit_posts (link, text, picture, group_id, myword, group_name, datestamp, deleted) VALUES ('".$url['url']."', '".$txt['text']."', 'http://88.198.139.146/sprut1".$picture['picture']."', ".$_GET['group_id'].", '".$group['myworld_group']."', '".$group['name']."', '".$_GET['slot']."', 0)";
		if (!empty($group['name'])) $m = $db->query($sql);
		
		if (get_userid() == 1) {
			$xid = $db->lastInsertId('rabbit_posts_id_seq');
			$m = $db->query("INSERT INTO admin_posts values (".$xid.")");
		}
		
		log_event($sql);
	}
	
	if (!empty($_GET['slot']) && strstr($_GET['act'], 'info')) {
		$sql = "SELECT id from rabbit_posts where datestamp='".$_GET['slot']."' and group_id=".$_GET['group_id']." ".$usr;
		$m = $db->query($sql);
		$xid = $m->fetch(PDO::FETCH_ASSOC);
		
		if (get_userid() > 1) $usr1 = 'and id not in (select post_id from admin_posts) '; else $usr1 = '';
			
		$sql = "DELETE from rabbit_posts where datestamp='".$_GET['slot']."' ".$usr1." and group_id=".$_GET['group_id']." ".$usr;
		$m = $db->query($sql);
		
		if (get_userid() == 1) {
			$m = $db->query("DELETE from admin_posts where post_id=".$xid['id']);
		}
		
		log_event($sql);	
	}
	