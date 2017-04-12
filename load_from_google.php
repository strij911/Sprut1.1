<?PHP
	include "./config.inc.php";
	
	if (!function_exists('log_event')) {
		function log_event($event) {
		 file_put_contents('log.txt', date('Y-m-d H:i:s')."|1|".$event.PHP_EOL , FILE_APPEND);
		 return;
		}
	}
	
	function lz($x) {
		if (strlen($x) > 1) return ($x); else return('0'.$x);
	}

	$template = file_get_contents('slot_template.txt');
	$slot_template = explode(chr(9), $template);
	
	$x1 = fopen('https://docs.google.com/spreadsheets/d/1s0Q2FvxtLwwFdPxNvrqcnvcoyIgdkQvkZpMqG-hCqYo/pub?gid=0&single=true&output=tsv', 'r');

	$i = 0;
	
	$x = fgets($x1, 4096);
	$x = fgets($x1, 4096);
	
	while ($x = fgets($x1, 90000)) {
		
		$ps_shed = '';	
		$ad_shed = '';	
		$vd_shed = '';	
		
		$a = explode(chr(9), $x);

		$group['name'] = $a[0];

		if (strtoupper($a[1]) == 'ДА') $group['state'] = 2; else $group['state'] = 1;
		
		$group['horizon'] = $a[2];

		$m = $db->query("SELECT id FROM users WHERE login='".$a[3]."'");
		$editor = $m->fetch(PDO::FETCH_ASSOC);
		$group['poster'] = $editor['id'];

		$m = $db->query("SELECT id FROM users WHERE login='".$a[4]."'");
		$editor = $m->fetch(PDO::FETCH_ASSOC);
		$group['advert'] = $editor['id'];
		
		for ($j = 5; $j < count($a); $j++) {
			$slot = $slot_template[$j];
			
			if (strstr($slot, ":60")) {
				$d = explode(":", $slot);
				$d[0]++;
				$d[1] = "00";
				$slot = $d[0].":".$d[1];
			}			
			
			$t = explode(';', $a[$j]);
			
			for ($k = 0; $k < count($t); $k++) {
				if (strstr(strtoupper($t[$k]), 'A')) {
					if (strstr($t[$k], '(')) {
						preg_match('/\((.+)\)/', $t[$k], $mx);
						$ad_shed.=$mx[1].';';
					} else $ad_shed.=$slot.';';
				} 
				
				if (strstr(strtoupper($t[$k]), 'C')) {
					if (strstr($t[$k], '(')) {
						preg_match('/\((.+)\)/', $t[$k], $mx);
						$ps_shed.=$mx[1].';';
					} else $ps_shed.=$slot.';';
				}
			
				if (strstr(strtoupper($t[$k]), 'V')) {
					if (strstr($t[$k], '(')) {
						preg_match('/\((.+)\)/', $t[$k], $mx);
						$vd_shed.=$mx[1].';';
					} else $vd_shed.=$slot.';';
				}
			
			}
		}
			
		$group['shedule'] = substr($ps_shed, 0, strlen($ps_shed)-1);
		$group['ad_shedule'] = substr($ad_shed, 0, strlen($ad_shed)-1);
		$group['video_shedule'] = substr($vd_shed, 0, strlen($vd_shed)-1);
		
		$m = $db->query("UPDATE sprut_group SET state = ".$group['state'].", horizon = ".$group['horizon'].", poster_id = '".$group['poster']."', group_admin_id = '".$group['advert']."', shedule = '".$group['shedule']."', ad_shedule = '".$group['ad_shedule']."', video_shedule = '".$group['video_shedule']."' WHERE name='".$group['name']."'");
		$g = $m->fetch(PDO::FETCH_ASSOC);		
		
		echo "Группа (".$group['name'].") успешно обновлена!<br>";
		
		log_event("Группа (".$group['name'].") успешно обновлена!"); 
		$i++;
	}
