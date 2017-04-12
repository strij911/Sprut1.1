<?PHP
	include "./config.inc.php";

	function get_free_slots($group_id, $ad_shedule, $horizon) {
		global $db;

		$slots = $db->query("SELECT datestamp from rabbit_posts where link like 'https://vk.com%' and group_id=".$group_id." and datestamp>='".date("Y-m-d")." 00:00:00'");
		while ($slot = $slots->fetch(PDO::FETCH_ASSOC)) {
			$slots_join.=$slot['datestamp']."|";
		}

		$nslots = $db->query("SELECT datestamp from rabbit_posts where link like 'https://vk.com%' and group_id=".$group_id." and datestamp>='".date("Y-m-d")." 00:00:00' and id in (select post_id from rabbit)");
		while ($nslot = $nslots->fetch(PDO::FETCH_ASSOC)) {
			$nslots_join.=$nslot['datestamp']."|";
		}
		
		$x = explode(";", $ad_shedule);

		$result = array();
		for ($a = 0; $a < ceil($horizon/24)+1; $a++) {
			for ($i = 0; $i < count($x); $i++) {
				if (strstr($x[$i], ":60")) {
					$d = explode(":", $x[$i]);
					$d[1] = "55";
					$x[$i] = $d[0].":".$d[1];
				}			
				
				$t = strtotime(date("Y-m-d", time()+$a*3600*24)." ".$x[$i].":00"); 

				if ($t < time()+$horizon*3600) {
					if ($t > time()) {
						if (!strstr($slots_join, date("Y-m-d H:i:s", $t))) $result['count']++;
						if (!strstr($slots_join, date("Y-m-d H:i:s", $t)) && $a == 0) $result['count_whorizon']++;
						if (strstr($slots_join, date("Y-m-d H:i:s", $t))) $result['planned']++;
					}
					
					if (strstr($nslots_join, date("Y-m-d H:i:s", $t))) $result['ads']++;
					if (strstr($slots_join, date("Y-m-d H:i:s", $t))) $result['last'] = $t;
				}
			}
		}
		return ($result);
	}
?>

<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="index_files/bootstrap.css">
<link rel="stylesheet" href="index_files/other.css">
<link rel="stylesheet" href="index_files/font-awesome.css">
<link rel="stylesheet" href="index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="index_files/mask.js" charset="UTF-8"></script>
<style>
#progressbar {
  background-color: #eeeeee;
  border-radius: 13px;
  padding: 3px;
}

#progressbar > div {
   background-color: orange;
   width: 100%;
   height: 20px;
   border-radius: 10px;
}
</style>
<title>SPRUT 1.1 - Главная</title>
</head>
<script type="text/javascript" src="gallery/hwg.js"></script>
<script type="text/javascript">
	hs.graphicsDir = './gallery/images/';
	hs.align = 'center';
	hs.transitions = ['expand', 'crossfade'];
	hs.outlineType = 'rounded-white';
	hs.fadeInOut = true;
	//hs.dimmingOpacity = 0.75;

	// Add the controlbar
	if (hs.addSlideshow) hs.addSlideshow({
		slideshowGroup: 'group1',
		interval: 5000,
		repeat: false,
		useControls: true,
		fixedControls: true,
		overlayOptions: {
			opacity: .75,
			position: 'bottom center',
			hideOnMouseOut: true
		}
	});

</script>

<link href="gallery/highslide.css" rel="stylesheet" type="text/css" />
<link href="gallery/style1.css" rel="stylesheet" type="text/css" />

<body>

<div class="main-container container-fluid">

<?PHP
	include "sidebar.inc";

?>

<div class="page-content">
<div class="page-body">

<div class="row">
    <div class="col-lg-12 col-xs-12 col-md-12">
        <div class="widget radius-bordered">
            <div class="widget-header bg-danger">
                <span class="widget-caption">Группы</span>
            </div>
            <div class="widget-body">
			<button class="btn btn-info shiny" disabled onclick="document.location.href='get_xls_posts.php';return(false);">Выгрузить сетку постов</button>
<?PHP

		if (get_userid()>1) $sql = "where poster_id='".get_userid()."'";
		$m = $db->query("SELECT * from sprut_group ".$sql." order by name");

		$slots = $db->query("SELECT count(id) as cnt from rabbit_posts where link like 'https://vk.com%' and group_id in (SELECT id from sprut_group ".$sql.") and datestamp>='".date("Y-m-d")." 00:00:00' and datestamp<='".date("Y-m-d H:i:s", time()+120*3600)."'");
		$slot = $slots->fetch(PDO::FETCH_ASSOC);

		$total_slots = 0;
		$xslots = $db->query("SELECT shedule, horizon from sprut_group ".$sql);
		while ($xslot = $xslots->fetch(PDO::FETCH_ASSOC)) {
			$s = $xslot['horizon']*(count(explode(';', $xslot['shedule']))/24);
			$total_slots = $total_slots + $s;
		}

		if (($slot['cnt']*100)/$total_slots < 31) $color = 'green';
		if (($slot['cnt']*100)/$total_slots > 30 && ($slot['cnt']*100)/$total_slots < 70) $color = 'orange';	
		if (($slot['cnt']*100)/$total_slots > 70) $color = 'red';	
		
		echo "<br><br>Постов запланировано / всего: <div id=\"progressbar\"><div align=center style=\"background-color: $color; width: ".(round(($slot['cnt']*100)/$total_slots, 2))."%; \"><nobr>".round(($slot['cnt']*100)/$total_slots, 2)." %</nobr></div></div>";
		echo "<br><br><table class=\"table table-bordered table-hover dataTable no-footer\">";
		echo "<thead><tr><th>Название группы</th><th>Группа в Моём мире</th><th>Заполнение в пределах горизонта, %</th><th>Свободно слотов сегодня</th><th>Вышло постов сегодня</th><th>Последний запланированный пост</th></tr></thead>";
		
		while($list = $m->fetch(PDO::FETCH_ASSOC)) {
			if (strstr(get_user_role(), 'poster') || get_user_role() == 'admin') $link = "poster.php?groups=".urlencode($list['name']);
			if (strstr(get_user_role(), 'advert')) $link = "promo.php";
			
			echo "<tr><td width=250><a href=".$link.">".$list['name']."</a></td>";
			echo "<td><a href=".$list['myworld_group'].">".$list['myworld_group']."</a></td>";			
			
			$x = get_free_slots($list['id'], $list['shedule'], $list['horizon']);
			if (empty($x['last'])) $last = "постов нет"; else $last = $x['last'];
			if (empty($x['count'])) $count = 0; else $count = $x['count'];
			if (empty($x['count_whorizon'])) $count_h = 0; else $count_h = $x['count_whorizon'];			
			if (empty($x['ads'])) $ads = 0; else $ads = $x['ads'];
			if (empty($x['planned'])) $x['planned'] = 0;	
			
			$s = $list['horizon']*(count(explode(';', $list['shedule']))/24);
			
			if ($x['planned']*100/$s < 31) $color = 'green';
			if ($x['planned']*100/$s > 30 && $x['planned']*100/$s < 70) $color = 'orange';	
			if ($x['planned']*100/$s > 70) $color = 'red';	
			
			echo "<td><div id=\"progressbar\"><div align=center style=\"background-color: $color; width: ".round(($x['planned']*100/$s), 2)."%; \"><nobr>".round(($x['planned']*100/$s), 2)." %</nobr></div></div></td>";			
			echo "<td>".$count_h."</td>";						
			echo "<td>".$ads."</td>";
			echo "<td>".date("Y-m-d H:i:s", $last)."</td>";
//			echo "<td><button class=\"btn btn-info shiny\" onclick=\"document.location.href='xls_read.php';return(false);\">Редактировать</button></td>";
			echo "</tr>";
		}					
?>					
				</table>
            </div>
        </div>
    </div>
	
</div>

</div>
</div>

</div>
</body></html>