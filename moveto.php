<?PHP
	setcookie("mv_group", $_GET['groups'], time()+3600); 
	
	include "./config.inc.php";

	if (isset($_GET['txt']) && strlen($_GET['txt']) > 1) {
			$m = $db->query("update posts set text = '".$_GET['txt']."' where id = ".$_GET['rabbit']);
	}
	
	function show_free_slots($group_name, $group_link) {
		global $db;
		$slots = $db->query("SELECT shedule, horizon from sprut_group where myworld_group='".$group_link."'");
		$slot = $slots->fetch(PDO::FETCH_ASSOC);

		$out = array();

		$x = explode(";", $slot['shedule']);
		
		
		for ($a = 0; $a < 20; $a++) {
			for ($i = 0; $i < count($x); $i++) {
				if (strstr($x[$i], ":60")) {
					$d = explode(":", $x[$i]);
					$d[1] = "55";
					$x[$i] = $d[0].":".$d[1];
				}			
				
				$t = strtotime(date("Y-m-d", time()+$a*3600*24)." ".$x[$i].":00"); 
				
				if ($t > time() && $t < time()+$slot['horizon']*3600) {
					$count_slots = $db->query("SELECT count(id) as cnt from rabbit_posts where group_name='".$group_name."' and datestamp='".date("Y-m-d H:i:s", $t)."'");
					$count_slot = $count_slots->fetch(PDO::FETCH_ASSOC);
					if ($count_slot['cnt'] == 0) $out[]=date("Y-m-d H:i:s", $t);
				}
			}
		}	
		return($out);
	}
	
	$m = $db->query("SELECT * from sprut_group where name='".$_GET['groups']."' LIMIT 1");	
	$row = $m->fetch(PDO::FETCH_ASSOC);
		
	$mx = $db->query("UPDATE posts set group_id=".$row['id'].", myword='".$row['myworld_group']."', group_name='".$row['name']."' where id=".$_GET['rabbit']);

	$free_slot = min(show_free_slots($row['name'], $row['myworld_group']));
	
	header("Location: poster.php?rabbit=".$_GET['rabbit']."&slot=".$free_slot."&from=".$_GET['from']."&groups=".urlencode($row['name']));
