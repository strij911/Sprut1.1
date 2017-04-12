<?PHP
	include "./config.inc.php";

	function show_lz($x) {
		$s = explode(":", $x);
		$result = '';
		if (strlen($s[0])>1) $result=$s[0]; else $result='0'.$s[0];
		if (strlen($s[1])>1) $result.=':'.$s[1]; else $result.=':'.'0'.$s[1];
		return($result);
	}
	
	function show_slots($group_name, $group_link, $a, $ad_shedule, $horizon, $id) {
		global $db, $thumbs;

		$count_slots = $db->query("SELECT datestamp from rabbit_posts where link = 'video' and group_name='".$group_name."' and datestamp>='".date("Y-m-d", time()-3600*24*2)." 00:00:00'");
		while ($count_slot = $count_slots->fetch(PDO::FETCH_ASSOC)) {
			$c_slot.=$count_slot['datestamp'];
		}
		
		$r_slots = $db->query("select datestamp from rabbit_posts where deleted>0 and link = 'video' and group_name='".$group_name."' and datestamp>='".date("Y-m-d", time()-3600*24*2)." 00:00:00' and id not in (select post_id from rabbit)");
		while ($r_slot = $r_slots->fetch(PDO::FETCH_ASSOC)) {
			$red_slots.=$r_slot['datestamp'];
		}
		
		$g_slots = $db->query("select datestamp from rabbit_posts where deleted>0 and link = 'video' and group_name='".$group_name."' and datestamp>='".date("Y-m-d", time()-3600*24*2)." 00:00:00' and id in (select post_id from rabbit)");
		while ($g_slot = $g_slots->fetch(PDO::FETCH_ASSOC)) {
			$green_slots.=$g_slot['datestamp'];
		}

		$out = '';
		$z = 0;
		$x = explode(";", $ad_shedule);
			for ($i = 0; $i < count($x); $i++) {
				if (strstr($x[$i], ":60")) {
					$d = explode(":", $x[$i]);
					$d[0]++;
					$d[1] = "00";
					$x[$i] = $d[0].":".$d[1];
				}
				$t = strtotime(date("Y-m-d", time()+$a*3600*24)." ".$x[$i].":00"); 
				if ($t < time()+$horizon*3600) {
					$z++;
					if ($t < time()) {
						if (strstr($red_slots, date("Y-m-d H:i:s", $t))) {

							$idx = $id.date("Y-m-d H:i:s", $t);
							if (!empty($thumbs[$idx]['picture'])) $slt='<img src='.$thumbs[$idx]['picture'].' width=70>'; else $slt='';

							$div_id = 'kr'.$id.$i.$a;
							$out.="&nbsp;<button class=\"btn btn-danger shiny\" onmouseout=\"var s=getElementById('".$div_id."'); if (s) s.style.display = 'none';\" onmouseover=\"var s=getElementById('".$div_id."'); if (s) { positionAt(this, s); s.style.display = 'block'; }\">".show_lz($x[$i])."</button><div style=\"display:none;z-index:1000;position:absolute;\" id=".$div_id."><table><tr bgcolor=#eeeeee><td colspan=3><font style=\"font-size:9px;\">".substr($thumbs[$idx]['text'], 0, 60)."</td></tr><tr bgcolor=#eeeeee><td>&nbsp;</td><td align=center>".$slt."</td><td>&nbsp;</td></tr></table></div></button>";							
						}
						
						if (strstr($green_slots, date("Y-m-d H:i:s", $t))) {
							$idx = $id.date("Y-m-d H:i:s", $t);
							if (!empty($thumbs[$idx]['picture'])) $slt='<img src='.$thumbs[$idx]['picture'].' width=70>'; else $slt='';

							$div_id = 'kg'.$id.$i.$a;
							$out.="&nbsp;<button class=\"btn btn-success shiny\" onmouseout=\"var s=getElementById('".$div_id."'); if (s) s.style.display = 'none';\" onmouseover=\"var s=getElementById('".$div_id."'); if (s) { positionAt(this, s); s.style.display = 'block'; }\">".show_lz($x[$i])."</button><div style=\"display:none;z-index:1000;position:absolute;\" id=".$div_id."><table><tr bgcolor=#eeeeee><td colspan=3><font style=\"font-size:9px;\">".substr($thumbs[$idx]['text'], 0, 60)."</td></tr><tr bgcolor=#eeeeee><td>&nbsp;</td><td align=center>".$slt."</td><td>&nbsp;</td></tr></table></div></button>";							
						}
						
					} else 
					{
						if (!strstr($c_slot, date("Y-m-d H:i:s", $t))) $out.="&nbsp;<button class=\"btn btn-info shiny\" onclick=\"for (i=1;i<500;i++) {s = getElementById('sprut'+i); if (s && s.style.display == 'block') { var nameRadio = document.getElementsByName('fText'+i); for (var e = 0; e < nameRadio.length; e++) {  if (nameRadio[e].type === 'radio' && nameRadio[e].checked) fText = nameRadio[e].value;}  var p = fText; if (strstr(p, 'false')) {alert('Выберите текст!');return(false);} if (this.className=='btn btn-warning shiny') this.className='btn btn-info shiny'; else this.className='btn btn-warning shiny';ajax({
																   url:'get_ajax_vid.php?slot=".urlencode(date("Y-m-d H:i:s", $t))."&act='+this.className+'&p='+p+'&group_id=".$id."',
																   method:'GET',
																   data: { },
																   success:function(data){ }
																}); }}\">".show_lz($x[$i])."</button>"; else {
							$idx = $id.date("Y-m-d H:i:s", $t);
							if (!empty($thumbs[$idx]['picture'])) $slt='<img src='.$thumbs[$idx]['picture'].' width=70>'; else $slt='';
							$div_id = 'ks'.$id.$i.$a; 
							$out.="&nbsp;<button class=\"btn btn-warning shiny\" onmouseout=\"var s=getElementById('".$div_id."'); if (s) s.style.display = 'none';\" onmouseover=\"var s=getElementById('".$div_id."'); if (s) { positionAt(this, s); s.style.display = 'block'; }\" onclick=\"ajax({
																   url:'get_ajax_vid.php?slot=".urlencode(date("Y-m-d H:i:s", $t))."&act=info&group_id=".$id."',
																   method:'GET',
																   data: { },
																   success:function(data){ document.location.reload(); }
																});\">".show_lz($x[$i])."</button><div style=\"display:none;z-index:1000;position:absolute;\" id=".$div_id."><table><tr bgcolor=#eeeeee><td colspan=3><font style=\"font-size:9px;\">".substr($thumbs[$idx]['text'], 0, 60)."</td></tr><tr bgcolor=#eeeeee><td>&nbsp;</td><td align=center>".$slt."</td><td>&nbsp;</td></tr></table></div></button>";
						}
					}
				}
			}
			$t = strtotime(date("Y-m-d", time()+($a+1)*3600*24)." 00:00:00"); 
			
			if ($a == 0 && (strstr($out, '<button class="btn btn-warning') || strstr($out, '<button class="btn btn-success') || strstr($out, '<button class="btn btn-danger'))) {
				$out.="<a href=# onclick=\"var s = prompt('На какую дату скопировать слоты?', '".date("Y-m-d", strtotime(date("Y-m-d")) + 3600*24)."'); if (s) document.location.href='copy_slots.php?r=1&gid=".$id."&d='+s;\" title=\"Копировать слоты на следующий день\"> <img src=\"i/copy.png\" alt=\"Копировать слоты на следующий день\"></a>";
			}
		return($out);
	}
?>

<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="./index_files/bootstrap.css">
<link rel="stylesheet" href="./index_files/other.css">
<link rel="stylesheet" href="./index_files/font-awesome.css">
<link rel="stylesheet" href="./index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="./js/ajax_func.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/mask.js" charset="UTF-8"></script>
<title>SPRUT 1.1 - Видеопосты</title>
</head>
<script type="text/javascript" src="./gallery/hwg.js"></script>
<script type="text/javascript">
	hs.graphicsDir = '/gallery/images/';
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

<link href="./gallery/highslide.css" rel="stylesheet" type="text/css" />
<link href="./gallery/style1.css" rel="stylesheet" type="text/css" />

<body>

<div class="main-container container-fluid">

<?PHP
	include "sidebar.inc";

	$prod_select = "<B>Продукт:</B> <select name='products' onchange=\"for (i=1;i<100;i++) {s = getElementById('sprut'+i); if (s) s.style.display = 'none'; } var s = getElementById('sprut'+this.value); s.style.display = 'block';\"><option value=0>Выберите продукт";
	
	if (get_userid()>1) $sql = 'and user_id='.get_userid();
	
	$products = $db->query("SELECT id, title from products where prod_type='vid' $sql order by id");
	$prods = array();
	while($product = $products->fetch(PDO::FETCH_ASSOC)) {
		$prods[] = $product['id'];
		$prod_select.="<option value='".$product['id']."'>".$product['title'];
	}
	$prod_select.="</option></select>";
?>

<div class="page-content">
<div class="page-body">

<div class="row">
    <div class="col-lg-12 col-xs-12 col-md-12">
        <div class="widget radius-bordered">
            <div class="widget-header bg-danger">
                <span class="widget-caption">Видео посты</span>
            </div>
            <div class="widget-body">
					<button class="btn btn-success shiny">Опубликован</button>
					<button class="btn btn-danger shiny">Ошибка</button>
					<button class="btn btn-warning shiny">Запланирован</button>					
					<button class="btn btn-info shiny">Свободен</button>
					<br><br>
			<?PHP echo $prod_select;
			for ($j = 1; $j < count($prods)+1; $j++) {
				echo "<div id=sprut".$prods[$j-1]." style=\"display:none;\"><br><B>Теперь выберите промо и временной слот:</B><br>";
				$s = file_get_contents('http://admin:cmyk7701@88.198.139.146/sprut1/show_product_vid.php?pid='.$prods[$j-1]);
				echo $s;
				echo "</div>";
			}
			?>
			<br><br>
			<?PHP
				echo "<a href=# onclick=\"var s = prompt('На какую дату скопировать слоты?', '".date("Y-m-d", strtotime(date("Y-m-d")) + 3600*24)."'); if (s) document.location.href='copy_slots.php?r=1&gid=".$id."&d='+s;\" title=\"Копировать слоты на другой день\"> <img src=\"i/copy.png\" alt=\"Копировать слоты на другой день\"> скопировать все сегодняшние слоты на другую дату</a>";
			?>
			<br><br><table class="table table-bordered table-hover dataTable no-footer">
			<thead><tr><th><B>Группы</th><th></th><th></th><th></th><th></th><th></th><th align=center><B>Сегодня</b></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr></thead>
<?PHP
		if (get_userid() > 1) $sql = "and group_admin_id='".get_userid()."'";

		$thumbs = array();
		
		$sslots = $db->query("SELECT picture, text, group_id, datestamp from rabbit_posts where datestamp>='".date("Y-m-d H:i:s", time()-3600*24*2)."'");
		while ($sslot = $sslots->fetch(PDO::FETCH_ASSOC)) {
			$idx = $sslot['group_id'].$sslot['datestamp'];
			$thumbs[$idx]['picture'] = $sslot['picture'];
			$thumbs[$idx]['text'] = $sslot['text'];	
		}
		
		$users = array();
		
		$m = $db->query("SELECT id, login FROM users");
		while($list = $m->fetch(PDO::FETCH_ASSOC)) {
			$users[$list['id']] = $list['login'];
		}
		
		$m = $db->query("SELECT id as group_id, name as group_name, myworld_group as myword, video_shedule, horizon, poster_id, group_admin_id from sprut_group where video_shedule <> '' ".$sql." order by name");
		
		while($list = $m->fetch(PDO::FETCH_ASSOC)) {
			echo "<tr><td width=250><a href=".$list['myword'].">".$list['group_name']."</a>";

			if (get_userid() == 1) {
				echo "<br><font size=-2>Арбитражник: ".$users[$list['group_admin_id']].", постер: ".$users[$list['poster_id']]."</font>";
			}
			
			echo "</td>";

				for ($i = -2; $i < 6; $i++) {
				if ($i == 0) $disp = "style=\"display:block;\""; else $disp = "style=\"display:none;\""; 
				echo "<td bgcolor=#eeeeee style=\"cursor: hand;width:30px;\" onclick=\"for (i=33;i<3153;i++) { var s = getElementById('row'+i+'_'+'".$i."'); if (s) { if (s.style.display == 'none') s.style.display = 'block'; else s.style.display = 'none';}} \"><B><font style=\"font-size:10px;color:#aaaaaa;\">".date("Y m.d", time()+($i)*3600*24)."</font></B></td><td nowrap><div id=row".$list['group_id']."_".$i." $disp>".show_slots($list['group_name'], $list['myword'], $i, $list['video_shedule'], $list['horizon'], $list['group_id'])."</div></td>";
			}
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