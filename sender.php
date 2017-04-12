<?PHP

	include "./config_wo.inc.php";
	include "./mail_api.php";	
	include "./Googl.class.php";	
	
	$m = $db->query("SELECT * from rabbit_posts where datestamp<='".date("Y-m-d H:i:s")."' and deleted<1 and link<>'video' order by datestamp LIMIT 3");

	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		//print_r($row);
		$txt = str_replace('<p>', '', $row['text']);
		$txt = str_replace('</p>', '', $txt);		
		$txt = str_replace('<br />', "\r\n", $txt);
		$txt = str_replace('<br>', "\r\n", $txt);		
		$txt = str_replace('&quot;', "\"", $txt);
		$txt = str_replace("@", "", $txt);				
				
		$picture = $row['picture'];
		
		$slots = $db->query("SELECT id, uid, admin_uid, photo_album from sprut_group where myworld_group='".$row['myword']."'");
		$slot = $slots->fetch(PDO::FETCH_ASSOC);
	
		$uid = $slot['admin_uid'];						
	
		if (empty($uid)) {
			//$x1 = file_get_contents('http://book.vm0901.temafon.ru/application/setty.php?tpo='.urlencode('Пустой UID админа группы!'));
			$m = $db->query("UPDATE rabbit_posts SET deleted=2 where id=".$row['id']);
		} else {
	
			if (!strstr($row['link'], 'https://vk.com')) {
					$googl = new Googl('AIzaSyApXdSJJPz7Y6e-EVvsgT0hchAMaermopU');
					$link = $googl->shorten('http://upstreak.ru/sprut1/c.php?id='.$row['id'].'&ref='.urlencode(trim($row['link'])));
					if (empty($link)) $link = 'http://toctep.ru/?sp' . $row['id']; //$link = 'http://upstreak.ru/sprut1/c.php?id='.$row['id'].'&ref='.urlencode(trim($row['link'])); 
					
					$txt = str_ireplace('[%link%]', $link, $txt);
					$s = api_request_commercial($txt, $picture, $link, $slot['uid'], $uid, $slot['photo_album']);
					$post_type = 'adv';
			} else {
				$txt = preg_replace("#(.*?)\[.*?\](.*?)#is", "\\1\\3", $txt);
				$s = api_request($txt, $picture, $slot['uid'], $uid, $slot['photo_album']);
				$post_type = 'txt';
			}
			
			
			echo $s;
			$x = json_decode($s, true);

			if ($x['sent'] == 1) { // && !strstr($x['id'], 'ext_url_ratelimit')
				$m = $db->query("UPDATE rabbit_posts SET deleted=2 where id=".$row['id']);
				$m = $db->query("INSERT INTO rabbit (post_type, post_id, shedule, mail_post_id) VALUES ('".$post_type."', ".$row['id'].", '".date("Y-m-d H:i:s")."', '".$x['id']."')");
			} else {
				if ($x['photo'] == 'error' && strstr($row['link'], 'https://vk.com')) {
					$m = $db->query("DELETE FROM rabbit_posts where id=".$row['id']);
				}
				if (strstr($s, 'Permission error') || strstr($s, 'parameter is missing or invalid')) {
					$m = $db->query("UPDATE rabbit_posts SET deleted=3 where id=".$row['id']);
				}
			}
		}
	}