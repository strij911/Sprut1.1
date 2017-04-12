<?PHP
	include "./config.inc.php";
	
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
				
				if ($t > time() && $t < time()+3600) {
					$count_slots = $db->query("SELECT count(id) as cnt from rabbit_posts where group_name='".$group_name."' and datestamp='".date("Y-m-d H:i:s", $t)."'");
					$count_slot = $count_slots->fetch(PDO::FETCH_ASSOC);
					if ($count_slot['cnt'] == 0) $out[]=date("Y-m-d H:i:s", $t);
				}
			}
		}	
		return($out);
	}

	echo "<table>";
	$slots = $db->query("SELECT * from sprut_group where state=2 and name<>'' ");
	while ($slot = $slots->fetch(PDO::FETCH_ASSOC)) {
		$m = $db->query("SELECT * from posts where char_length(picture)>10 and deleted<1 and text not like '%[id%' and group_name='".$slot['name']."' order by datestamp desc LIMIT 1");		
		$post = $m->fetch(PDO::FETCH_ASSOC);
		
		$free_slot = min(show_free_slots($slot['name'], $slot['myworld_group']));
		
		if (!empty($free_slot)) {
			echo "<tr bgcolor=#eeeeee><td>".$slot['name']."</td><td nowrap>".$free_slot."</td><td>".$post['text']."</td><td>".$post['picture']."</td></tr>";
			
			$mx = $db->query("INSERT INTO rabbit_posts (SELECT * FROM posts WHERE id=".$post['id'].")");
			$mx = $db->query("UPDATE rabbit_posts SET datestamp='".$free_slot."' where id=".$post['id']);
			$mx = $db->query("UPDATE posts SET deleted=1 WHERE id=".$post['id']."");			
		} else echo "<tr bgcolor=#eeeeee><td colspan=4>".$slot['name']."</td></tr>";
	}
	echo "</table>";