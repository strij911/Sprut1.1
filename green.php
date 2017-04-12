<?PHP
	if (empty($_GET['id'])) exit;
	try{
		$db = new PDO('pgsql:dbname=sprut1;host=localhost', 'postgres', 'sXrtgHT1');
	}catch(PDOException  $e ){
		echo "Error: ".$e;
	}	

	$ua = $_GET['ua'];
	$id = trim($_GET['id']);
	
	$id = str_replace(',', '', $id);
	$id = str_replace('.', '', $id);
	
	$m = $db->query("INSERT INTO events (datestamp, post_id, identity) VALUES ('".date("Y-m-d H:i:s")."', ".$id.", '".$ua."')");

	$m = $db->query("select link from rabbit_posts where id=".$id);
	$link = $m->fetch(PDO::FETCH_ASSOC);
	
	$subID = 'postid'.$id;
	
	$link = trim($link['link']);
	$link = str_replace('http://', '', $link);
	
	if (substr_count($link, ':') > 3) {
		for ($i = strlen($link)-1; $i > 0; $i--) {
			if ($link[$i] == ':' || $link[$i] == '/') {
				$new_link = substr($link, 0, $i+1) . $subID;
				break;
			}
		}
	} else if (!strstr($link, ':') && substr_count($link, '/') == 1) $new_link = $link . '/'.$subID; else $new_link = $link . ':'.$subID;
	
	echo 'http://'.$new_link;	
	
	$db = null;
	$m = null;
