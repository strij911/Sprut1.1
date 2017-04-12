<?PHP
	include "./config_wo.inc.php";
	
	$fd = fopen('groups.txt', 'r');
	
	echo "<table border=1>";
	
	while ($a = fgets($fd, 4096)) {
		
		$ps_shed = array_fill(0, 290, ' ');
		
		$x = trim($a);
		$g = $db->query("select * from sprut_group where name='".$x."'");
		$group = $g->fetch(PDO::FETCH_ASSOC);
		
		if ($group['state'] == 2) $state = 'Да'; else $state = 'Нет';

		$m = $db->query("SELECT login FROM users WHERE id='".$group['poster_id']."'");
		$editor = $m->fetch(PDO::FETCH_ASSOC);
		$poster = $editor['login'];

		$m = $db->query("SELECT login FROM users WHERE id='".$group['group_admin_id']."'");
		$editor = $m->fetch(PDO::FETCH_ASSOC);
		$advert = $editor['login'];
		
		echo '<tr><td>'.$group['name'].'<td>'.$state.'<td>'.$group['horizon'].'<td>'.$poster.'<td>'.$advert.'<td>';
		
		$shedule = explode(';', $group['shedule']);
		$ad_shedule = explode(';', $group['ad_shedule']);		
		$vd_shedule = explode(';', $group['video_shedule']);		
		
		for ($i = 0; $i < count($shedule); $i++) {
			$t = explode(':', $shedule[$i]);
			if ($t[1]%5 == 0) $ps_shed[$t[0]*12+ceil($t[1]/5)-1] = 'C;'; else $ps_shed[$t[0]*12+ceil($t[1]/5)-1] = 'C('.$shedule[$i].');';
		}

		for ($i = 0; $i < count($ad_shedule); $i++) {
			$t = explode(':', $ad_shedule[$i]);
			if ($t[1]%5 == 0) $ps_shed[$t[0]*12+ceil($t[1]/5)-1].= 'A;'; else $ps_shed[$t[0]*12+ceil($t[1]/5)-1].= 'A('.$ad_shedule[$i].');';
		}

		for ($i = 0; $i < count($vd_shedule); $i++) {
			$t = explode(':', $vd_shedule[$i]);
			if ($t[1]%5 == 0) $ps_shed[$t[0]*12+ceil($t[1]/5)-1].= 'V;'; else $ps_shed[$t[0]*12+ceil($t[1]/5)-1].= 'V('.$vd_shedule[$i].');';
		}
		
		echo implode('<td>', $ps_shed);
	}
	fclose($fd);

	echo "</table>";