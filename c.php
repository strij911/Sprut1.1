<?PHP
	include "./config_wo.inc.php";
	
	$subID = 'postid'.$_GET['id'];
	
	$link = trim($_GET['ref']);
	$link = str_replace('http://', '', $link);
	
	if (substr_count($link, ':') > 3) {
		for ($i = strlen($link)-1; $i > 0; $i--) {
			if ($link[$i] == ':' || $link[$i] == '/') {
				$new_link = substr($link, 0, $i+1) . $subID;
				break;
			}
		}
	} else if (!strstr($link, ':') && substr_count($link, '/') == 1) $new_link = $link . '/'.$subID; else $new_link = $link . ':'.$subID;
	
	$ln = 'http://'.$new_link;
	
	$ua = htmlspecialchars($_SERVER['REMOTE_ADDR']);
	
	$m = $db->query("INSERT INTO events (datestamp, post_id, identity) VALUES ('".date("Y-m-d H:i:s")."', ".$_GET['id'].", '".$ua."')");

//	file_put_contents('redirect.log', $ln . PHP_EOL, FILE_APPEND);
	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: ".$ln);
	exit;