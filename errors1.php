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
		
		
		for ($a = 0; $a < 1; $a++) {
			for ($i = 0; $i < count($x); $i++) {
				if (strstr($x[$i], ":60")) {
					$d = explode(":", $x[$i]);
					$d[1] = "55";
					$x[$i] = $d[0].":".$d[1];
				}			
				
				$t = strtotime(date("Y-m-d", time()+$a*3600*24)." ".$x[$i].":00"); 
				
				if ($t > time() && $t < time()+$deep*3600) {
					$count_slots = $db->query("SELECT count(id) as cnt from rabbit_posts where group_name='".$group_name."' and datestamp='".date("Y-m-d H:i:s", $t)."'");
					$count_slot = $count_slots->fetch(PDO::FETCH_ASSOC);
					if ($count_slot['cnt'] == 0) $out[]=date("H:i", $t);
				}
			}
		}	
		return($out);
	}	
		
	$m = $db->query("select datestamp,id from rabbit_posts where id in (select post_id from rabbit where post_type='adv') and deleted>0 order by datestamp desc");	
	$row = $m->fetch(PDO::FETCH_ASSOC);
	$date1 = $row['datestamp'];
		
	$m = $db->query("select shedule from rabbit where post_id=".$row['id']);
	$row = $m->fetch(PDO::FETCH_ASSOC);
	$date2 = $row['shedule'];
	
	echo round((strtotime($date2) - strtotime($date1))/60, 1);
	
