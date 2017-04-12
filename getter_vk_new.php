<?PHP
	$x = file_get_contents('/var/www/advertmania.com/web/sprut1/flags/getter_vk.lck');
	if (strlen($x) > 0) exit;
	$x = file_put_contents('/var/www/advertmania.com/web/sprut1/flags/getter_vk.lck', date("Y-m-d H:i:s").PHP_EOL);
	
	include "./config_wo.inc.php";

	function get_vk($owner_id) {
		if (empty($owner_id)) return;
		
		$params_wall = array(
			'owner_id' => '-'.$owner_id, 
			'offset' => '',
			'count' => 7, 
			'filter' => 'all', 
			'extended' => '1',
			'access_token' => '',
		);
				
		$methods = '';
		$url = 'https://api.vk.com/method/wall.get';
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params_wall)));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($curl);

		curl_close($curl);
		$groupe_Info = json_decode($result, true);

		$p = array();
		$z = 0;

		foreach ($groupe_Info['response']['wall'] as $post) {
			if (!empty($post['id']) && $post['post_type'] != 'copy') {
				
				$p[$z]['datestamp'] = $post['date'];
				$p[$z]['url'] = 'https://vk.com/feed?w=wall'.$params_wall['owner_id'].'_'.$post['id'];
				$p[$z]['text'] = $post['text'];
				$s = '';
				if (!empty($post['src_xxbig'])) {
					//$size = getimagesize($post['src_xxbig']);
					$size[0] = 250;
					if ($size[0]>=250 || $size[1]>=250) $s.= $post['src_xxbig'].";";
				} else
					if (!empty($post['src_big'])) {
						//$size = getimagesize($post['src_xxbig']);
						$size[0] = 250;
						if ($size[0]>=250 || $size[1]>=250) $s.= $post['src_big'].";";
					} 
						
				if (isset($post['attachments'])) {
					for ($i = 0;$i < count($post['attachments']); $i++) {
						if ($post['attachments'][$i]['type'] == 'photo' && !empty($post['attachments'][$i]['photo']['src_xxbig'])) {
							if ($post['attachments'][$i]['photo']['width'] >= 250 || $post['attachments'][$i]['photo']['height'] >= 250) $s.=$post['attachments'][$i]['photo']['src_xxbig'].";"; 
						} else
							if ($post['attachments'][$i]['type'] == 'photo' && !empty($post['attachments'][$i]['photo']['src_big'])) {
								if ($post['attachments'][$i]['photo']['width'] >= 250 || $post['attachments'][$i]['photo']['height'] >= 250) $s.=$post['attachments'][$i]['photo']['src_big'].";"; 
							} /* else
								if ($post['attachments'][$i]['type'] == 'link' && !empty($post['attachments'][$i]['link']['image_src'])) {
									$size = getimagesize($post['attachments'][$i]['link']['image_src']);
									$size[0] = 250;
									if ($size[0]>=250 || $size[1]>=250) $s.=$post['attachments'][$i]['link']['image_src'].";";
								} */
					}
				}
				
				$x = explode(";", $s);
				$x = array_unique($x);

				$p[$z]['picture'] = implode(';', $x);
				$s = '';

				$z++;
			}
		}
		return $p;
	}

	$id = $_GET['id'];
	if (!empty($id)) $sql = "and sprut_group.id = $id";
	$u = $db->query("SELECT * FROM vk_groups, sprut_group where sprut_group.id=vk_groups.mail_group_id and active=1 $sql");
	
	while($list = $u->fetch(PDO::FETCH_ASSOC)) {
		$x = get_vk($list['vk_owner_id']);

		foreach ($x as $value) {
			if (!stristr($value['text'], '.cc') && strlen($value['picture'])>10 && !stristr($value['text'], '.ua') && !stristr($value['text'], '.by') && !stristr($value['text'], '.kz') && !stristr($value['text'], '.ru') && !stristr($value['text'], '.com') && !strstr($value['text'], 'club') && !stristr($value['text'], 'http:') && !stristr($value['text'], 'https:')) {
				$value['text'] = str_replace("'", "\"", $value['text']);
				$value['text'] = str_replace("@", "", $value['text']);				
				$value['text'] = preg_replace("#(.*?)\[.*?\](.*?)#is", "\\1\\3", $value['text']);
				$x = file_put_contents('/var/www/advertmania.com/web/sprut1/flags/getter_vk.lck', $value['url'].":".$value['picture'].PHP_EOL, FILE_APPEND);
				//echo $value['url'].":".$list['name'].PHP_EOL;
				if (!empty($value['picture'])) $m = $db->query("INSERT INTO posts (link, text, picture, group_id, myword, group_name, datestamp) VALUES ('".$value['url']."', '".$value['text']."', '".$value['picture']."', ".$list['mail_group_id'].", '".$list['myworld_group']."', '".$list['name']."', '".date("Y-m-d H:i:s", $value['datestamp'])."')");
				echo '.';
				//echo "INSERT INTO posts (link, text, picture, group_id, myword, group_name, datestamp) VALUES ('".$value['url']."', '".$value['text']."', '".$value['picture']."', ".$list['mail_group_id'].", '".$list['myworld_group']."', '".$list['name']."', '".date("Y-m-d H:i:s", $value['datestamp'])."')<br>";

			}
		}
	}
	
	$s = file_put_contents('/var/www/advertmania.com/web/sprut1/flags/getter_vk.lck', '');