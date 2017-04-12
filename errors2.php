<?PHP
	
	include "./config.inc.php";
	
	function user_search($users, $id) {
		for ($i = 0; $i < count($users); $i++) {
			if ($users[$i]['id'] == $id) return($users[$i]['login']);
		}
	}
	
	function show_free_slots($group_name, $group_link, $shedule, $deep) {
		global $db;

		$out = array();

		$x = explode(";", $shedule);
		
		
		for ($a = -1; $a < 1; $a++) {
			for ($i = 0; $i < count($x); $i++) {
				if (strstr($x[$i], ":60")) {
					$d = explode(":", $x[$i]);
					$d[1] = "55";
					$x[$i] = $d[0].":".$d[1];
				}			
				
				$t = strtotime(date("Y-m-d", time()+$a*3600*24)." ".$x[$i].":00"); 
				
				if ($t < time()) {
					$count_slots = $db->query("SELECT count(id) as cnt from rabbit_posts where group_name='".$group_name."' and datestamp='".date("Y-m-d H:i:s", $t)."'");
					$count_slot = $count_slots->fetch(PDO::FETCH_ASSOC);
					if ($count_slot['cnt'] > 0) $out[]=$t;
				}
			}
		}	
		return($out);
	}	
		
	$m = $db->query("SELECT * from sprut_group where state=2 order by name");

	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		$mx = show_free_slots($row['name'], $row['myworld_group'], $row['shedule'], 23);
		if (!empty($mx)) {
			$mx = max($mx);
			if (time() - $mx > 3600*23)	$out.=$row['name'].'|'.date("Y-m-d H:i:s", $mx)."\n";
		}

	}
	
	echo $out;
