<?PHP
	if (!isset($_COOKIE["mv_group"])) {
		setcookie("mv_group", $_GET['groups'], time()+3600); 
		$_COOKIE["mv_group"] = $_GET['groups'];
	}
		
	include "./config.inc.php";
		
	if (isset($_GET['rabbit'])) {
			$m = $db->query("INSERT INTO rabbit_posts (SELECT * FROM posts WHERE id=".$_GET['rabbit'].")");
			$m = $db->query("UPDATE rabbit_posts SET datestamp='".$_GET['slot']."' where id=".$_GET['rabbit']);
			$m = $db->query("UPDATE posts SET deleted=1 WHERE id=".$_GET['rabbit']."");
		}

	if (isset($_GET['del'])) {
			$m = $db->query("UPDATE posts SET deleted=1 WHERE id=".$_GET['del']."");
		}

	if (isset($_GET['rabbitdel'])) {
			$m = $db->query("DELETE FROM rabbit_posts WHERE id=".$_GET['rabbitdel']."");
			$m = $db->query("UPDATE posts SET deleted=0 WHERE id=".$_GET['rabbitdel']."");			
		}

	if (empty($_GET['groups'])) {
		if (get_userid()>1) $sql = "where poster_id='".get_userid()."'";
		$m = $db->query("SELECT name from sprut_group ".$sql." order by name LIMIT 1");
		$row = $m->fetch(PDO::FETCH_ASSOC);
		header("Location: poster.php?groups=".$row['name']);
	}
	
	if (isset($_GET['from'])) header("Location: poster.php?groups=".$_GET['from']);
	
	if (get_userid()>1) $sql = "where poster_id='".get_userid()."'";
	$m = $db->query("SELECT id as group_id, name as group_name, myworld_group as myword from sprut_group ".$sql." order by name");

?><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="./index_files/bootstrap.css">
<link rel="stylesheet" href="./index_files/other.css">
<link rel="stylesheet" href="./index_files/font-awesome.css">
<link rel="stylesheet" href="./index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="./index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/mask.js" charset="UTF-8"></script>
<title>SPRUT 1.1 - Публикация обычных постов</title>
</head>

<script type="text/javascript" src="gallery/hwg.js"></script>
<script type="text/javascript">
	hs.graphicsDir = 'gallery/images/';
	hs.align = 'center';
	hs.transitions = ['expand', 'crossfade'];
	hs.outlineType = 'rounded-white';
	hs.fadeInOut = true;
	//hs.dimmingOpacity = 0.75;

	// Add the controlbar
hs.addSlideshow({
	//slideshowGroup: 'group1',
	interval: 5000,
	repeat: false,
	useControls: true,
	fixedControls: 'fit',
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

<?PHP include "sidebar.inc";?>

<div class="page-content">
<div class="page-body">

<div class="row">
    <div class="col-lg-12 col-xs-12 col-md-12">
        <div class="widget radius-bordered">
            <div class="widget-header bg-danger">
                <span class="widget-caption">Публикация обычных постов</span>
            </div>
            <div class="widget-body">
<B>Выберите группу (Мой мир):</B>	<select name=groups onchange="document.location.href='poster.php?groups='+this.value;">
<?PHP
	$all_sel = '';
	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		if ($row['group_name'] == $_GET['groups']) {
			$sel = 'selected';
			$slots = $db->query("SELECT shedule, horizon from sprut_group where myworld_group='".$row['myword']."'");
			$slot = $slots->fetch(PDO::FETCH_ASSOC);
			$horizon = $slot['horizon'];
			$shedule = $slot['shedule'];
			$grp = $_GET['groups'];
		} else $sel = '';

		$all_sel.="<option $sel value='".$row['group_name']."'>".$row['group_name'];
		echo "<option $sel value='".$row['group_name']."'>".$row['group_name'];
	}
	
?></option></select>
<form method=GET>
				<br><br>
				<table><tr><td width=50% valign=top>Новые посты<br><br>
					<table class="table table-bordered table-hover dataTable no-footer">
						<thead><th><B>Текст<th><B>Картинка</th></thead>
<?PHP				
	$x = explode(";", $shedule);
	$a = 0;
	$slot_sel = '';
	for ($a = 0; $a < 20; $a++) {
		for ($i = 0; $i < count($x); $i++) {
			if (strstr($x[$i], ":60")) {
				$d = explode(":", $x[$i]);
				$d[1] = "55";
				$x[$i] = $d[0].":".$d[1];
			}			
			$t = strtotime(date("Y-m-d", time()+$a*3600*24)." ".$x[$i].":00"); 
			if ($t < time()+$horizon*3600) {
				$slots = $db->query("SELECT count(id) as cnt from rabbit_posts where link like 'https://vk.com%' and group_name='".$grp."' and datestamp='".date("Y-m-d H:i:s", $t)."'");
				$slot = $slots->fetch(PDO::FETCH_ASSOC);
				if ($slot['cnt'] == 0 && $t > time()) $slot_sel.="<option value='".date("Y-m-d H:i:s", $t)."'>".date("Y-m-d H:i:s", $t);
			}
		}
		//if ($slot['cnt'] == 0 && $t > time()) break; 
	}
	if ($t >= time()+$horizon*3600) $t = 0;
		
	$m = $db->query("SELECT * from posts where deleted<1 and group_name='".$_GET['groups']."' order by datestamp desc LIMIT 50");	
	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		if ($t > 0) $tx = date("Y-m-d H:i:s", $t); else $tx = 'Нет слотов';
		$pics = explode(';', $row['picture']);
		$img = '';
		foreach ($pics as $picture) {
			//$picture = str_replace('https://', 'http://', $picture);
			if (strstr($picture, '.jpg')) $img.= "<a href=".$picture." onclick='return hs.expand(this)' class='highslide' title=''><img src=".$picture." width=180 class=fotoreport></a>"; else $img.= "";
		}
		if (!empty($row['text'])) $txt = $row['text']; else $txt = '<font color=#eeeeee>Нет текста</font>';
		echo "<tr><td><font style=\"font-size:9px;\">".$row['datestamp']."</font><br><B>Ссылка на источник:</B> <a href=".$row['link'].">".$row['link']."</a><P>".str_replace('развернуть', '', $txt);
		echo "<br><br><button class=\"btn btn-danger shiny\" onclick=\"document.location.href='poster.php?del=".$row['id']."&groups=".urlencode($_GET['groups'])."';return(false);\">Удалить</button>";
		echo "&nbsp;<button class=\"btn btn-warning shiny\" onclick=\"document.location.href='poster_edit.php?edit=".$row['id']."&groups=".urlencode($_GET['groups'])."';return(false);\">Редактировать</button>";		
		echo "<br><br><button class=\"btn btn-info shiny\" onclick=\"var s = getElementById('slots".$row['id']."'); document.location.href='poster.php?rabbit=".$row['id']."&slot=' + s.value + '&groups=".urlencode($_GET['groups'])."';return(false);\">В очередь &gt;&gt;</button> <select id='slots".$row['id']."'>".$slot_sel."</select>";	
		echo "<br><br><button class=\"btn btn-maroon shiny\" onclick=\"var s = getElementById('moveto".$row['id']."'); document.location.href='moveto.php?from=".$_GET['groups']."&rabbit=".$row['id']."&groups=' + s.value;return(false);\">Перенести в группу: </button> <select id=\"moveto".$row['id']."\">".$all_sel."</option></select>";
//		echo "<select name=time".$row['id'].">";
//		while ($slot = $slots->fetch(PDO::FETCH_ASSOC)) {
//			echo "<option value='".date("Y-m-d")."'></option></select>";
//		}
		echo "</td><td>$img</td></td></tr>";
	}
?>
					</table>
				</td><td>&nbsp;</td><td valign=top width=50%>Посты в очереди<br><br>
					<table class="table table-bordered table-hover dataTable no-footer">
						<thead><th><B>Текст<th><B>Картинка</th></thead>
<?PHP				
	$m = $db->query("SELECT * from rabbit_posts where link like 'https://vk.com%' and deleted<1 and group_name='".$_GET['groups']."' order by datestamp desc");	
	
	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		$pics = explode(';', $row['picture']);
		$img = '';
		foreach ($pics as $picture) {
			$picture = str_replace('pp.vk.me', 'pp.userapi.com', $picture);
			$picture = str_replace('cs7055.vk.me', 'pp.userapi.com', $picture);	
			$picture = str_replace('cs541603.vk.me', 'pp.userapi.com', $picture);
			if (strstr($picture, '.jpg')) $img.= "<a href=".$picture." onclick='return hs.expand(this)' class='highslide' title=''><img src=".$picture." width=70 class=fotoreport></a>"; else $img.= "";
		}

		if (!empty($row['text'])) $txt = $row['text']; else $txt = '<font color=#eeeeee>Нет текста</font>';
		echo "<tr><td><B>Ссылка на источник:</B> <a href=".$row['link'].">".$row['link']."</a><br><b>Время поста:</B> ".$row['datestamp']."<P>".str_replace('развернуть', '', $txt);
		echo "<br><br><button class=\"btn btn-danger shiny\" onclick=\"document.location.href='poster.php?rabbitdel=".$row['id']."&groups=".urlencode($_GET['groups'])."';return(false);\">Удалить</button>";
		echo "&nbsp;<button class=\"btn btn-warning shiny\" onclick=\"document.location.href='poster_edit_rb.php?edit=".$row['id']."&groups=".urlencode($_GET['groups'])."';return(false);\">Редактировать</button>";		
		echo "</td><td>$img</td></td></tr>";
	}
?>
					</table>
				</td></tr>
            </div>
        </div>
    </div>
	
	
</div>

</div>
</div>

</div>
</body></html>	