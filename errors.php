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
		
?><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="./index_files/bootstrap.css">
<link rel="stylesheet" href="./index_files/other.css">
<link rel="stylesheet" href="./index_files/font-awesome.css">
<link rel="stylesheet" href="./index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="./index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/mask.js" charset="UTF-8"></script>
<title>SPRUT 1.1 - Уведомления</title>
</head>

<body>

<div class="main-container container-fluid">

<?PHP include "sidebar.inc";?>

<div class="page-content">
<div class="page-body">

<div class="row">
    <div class="col-lg-12 col-xs-12 col-md-12">
        <div class="widget radius-bordered">
            <div class="widget-header bg-danger">
                <span class="widget-caption">Ошибки и уведомления</span>
            </div>
            <div class="widget-body">
<?PHP

	$out = '<B>Разница во времени между последним запланированным выходом и фактическим временем:</B><br>';
	$nomore = strlen($out);

	$m = $db->query("select datestamp,id from rabbit_posts where id in (select post_id from rabbit where post_type='adv') and deleted>0 order by datestamp desc");	
	$row = $m->fetch(PDO::FETCH_ASSOC);
	$date1 = $row['datestamp'];
		
	$m = $db->query("select shedule from rabbit where post_id=".$row['id']);
	$row = $m->fetch(PDO::FETCH_ASSOC);
	$date2 = $row['shedule'];
	
	$out.='<br>'.round((strtotime($date2) - strtotime($date1))/60, 1).' минут';
	
	if (strlen($out) > $nomore)  echo $out;
	
	$out = '<br><br><B>Ошибки постинга:</B><br>';
	$nomore = strlen($out);
	$x = file_get_contents('log_api.txt');
	
	if (strstr($x, 'Application is not installed for this user')) $out.= '<br>Ошибка приложения';
	if (strstr($x, 'Please resubmit the request')) $out.= '<br>Неизвестная ошибка - требуется отправить заново';
	if (strstr($x, 'Image is incorrect or inaccessible')) $out.= '<br>Некорректная картинка';
	if (strstr($x, 'ext_url_ratelimit')) $out.= '<br>Превышение лимита ссылок в коммерческих постах';
	if (strstr($x, 'Permission error:')) $out.= '<br>Нет прав на постинг в группу';
	
	if (strlen($out) > $nomore)  echo $out;
	
	$out = '<br><br><B>Забаненные группы:</B><br>';
	$nomore = strlen($out);
	
	$m = $db->query("select distinct name from sprut_group,rabbit_posts,rabbit where rabbit.post_id=rabbit_posts.id and rabbit_posts.group_id=sprut_group.id and rabbit.post_type='block' and rabbit.shedule>=current_date");
	
	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		$out.='<br>'.$row['name'];
	}
	
	if (strlen($out) > $nomore)  echo $out;

	$m = $db->query("SELECT id, login from users");

	$users = array();
	$i = 0;
	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		$users[$i]['id'] = $row['id'];
		$users[$i]['login'] = $row['login'];		
		$i++;
	}
	
	$out = '<br><br><B>Свободные рекламные слоты сегодня:</B><br><br><table>';
	$nomore = strlen($out);
	
	$m = $db->query("SELECT * from sprut_group order by name");

	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		$mx = show_free_slots($row['name'], $row['myworld_group'], $row['ad_shedule'], 24);
		if (!empty($mx)) {
			$out.='<tr bgcolor='.$col.'><td><B>'.$row['name'].'</B></td><td>'.user_search($users, $row['group_admin_id']).'&nbsp;&nbsp;&nbsp;&nbsp;</td><td>'.implode('; ', $mx);
			if ($col == '#eeeeee') $col = '#ffffff'; else $col = '#eeeeee';
		}
	}
	
	if (strlen($out) > $nomore)  echo $out.'</table>';
	
	$out = '<br><br><B>Свободные контентные слоты сегодня (ручной постинг):</B><br><br><table>';
	$nomore = strlen($out);
	
	$m = $db->query("SELECT * from sprut_group where state=1 order by name");

	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		$mx = show_free_slots($row['name'], $row['myworld_group'], $row['shedule'], 24);
		if (!empty($mx)) {
			$out.='<tr bgcolor='.$col.'><td><B>'.$row['name'].'</B></td><td>'.user_search($users, $row['poster_id']).'&nbsp;&nbsp;&nbsp;&nbsp;</td><td>'.implode('; ', $mx);
			if ($col == '#eeeeee') $col = '#ffffff'; else $col = '#eeeeee';
		}

	}
	
	if (strlen($out) > $nomore)  echo $out.'</table>';
	
	$out = '<br><br><B>Свободные контентные слоты в ближайший час (авто):</B><br><br><table>';
	$nomore = strlen($out);
	
	$m = $db->query("SELECT * from sprut_group where state=2 order by name");

	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		$mx = show_free_slots($row['name'], $row['myworld_group'], $row['shedule'], 1);
		if (!empty($mx)) {
			$out.='<tr bgcolor='.$col.'><td><B>'.$row['name'].'</B></td><td>'.implode('; ', $mx);
			if ($col == '#eeeeee') $col = '#ffffff'; else $col = '#eeeeee';
		}

	}
	
	if (strlen($out) > $nomore)  echo $out.'</table>';
	
?>

            </div>
        </div>
    </div>
	
	
</div>

</div>
</div>

</div>
</body></html>	