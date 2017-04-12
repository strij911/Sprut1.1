<?PHP
	include "./config.inc.php";
		
	if (isset($_POST['id'])) {
			$m = $db->query("update posts set text = '".$_POST['txt_area']."' where id = ".$_POST['id']);
			header("Location: poster.php?groups=".$_GET['groups']);
	}

	if (get_userid()>1) $sql = "where poster_id='".get_userid()."'";
	$m = $db->query("SELECT id as group_id, name as group_name, myworld_group as myword from sprut_group ".$sql." order by name");

	$all_sel = '';
	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		if ($row['group_name'] == $_COOKIE["mv_group"]) {
			$sel = 'selected';
			$slots = $db->query("SELECT shedule, horizon from sprut_group where myworld_group='".$row['myword']."'");
			$slot = $slots->fetch(PDO::FETCH_ASSOC);
			$horizon = $slot['horizon'];
			$shedule = $slot['shedule'];
			$grp = $_GET['groups'];
		} else $sel = '';

		$all_sel.="<option $sel value='".$row['group_name']."'>".$row['group_name'];
	}

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
			if ($t > time() && $t < time()+$horizon*3600) {
				$slots = $db->query("SELECT count(id) as cnt from rabbit_posts where link like 'https://vk.com%' and group_name='".$grp."' and datestamp='".date("Y-m-d H:i:s", $t)."'");
				$slot = $slots->fetch(PDO::FETCH_ASSOC);
				if ($slot['cnt'] == 0 && $t > time()) $slot_sel.="<option value='".date("Y-m-d H:i:s", $t)."'>".date("Y-m-d H:i:s", $t);
			}
		}
		//if ($slot['cnt'] == 0 && $t > time()) break; 
	}
	
?><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="./index_files/bootstrap.css">
<link rel="stylesheet" href="./index_files/other.css">
<link rel="stylesheet" href="./index_files/font-awesome.css">
<link rel="stylesheet" href="./index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="./index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/mask.js" charset="UTF-8"></script>
<title>SPRUT 1.1 - Редактирование поста</title>
</head>

<script type="text/javascript" src="/gallery/hwg.js"></script>
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

<link href="/gallery/highslide.css" rel="stylesheet" type="text/css" />
<link href="/gallery/style1.css" rel="stylesheet" type="text/css" />

<body>

<div class="main-container container-fluid">

<?PHP include "sidebar.inc";?>

<div class="page-content">
<div class="page-body">

<div class="row">
    <div class="col-lg-12 col-xs-12 col-md-12">
        <div class="widget radius-bordered">
            <div class="widget-header bg-danger">
                <span class="widget-caption">Редактирование поста</span>
            </div>
            <div class="widget-body">

<form method=POST>
<input type=hidden name=groups value="<?PHP echo $_GET['groups']; ?>">
<input type=hidden name=id value="<?PHP echo $_GET['edit']; ?>">
<?PHP				
	
	$m = $db->query("SELECT * from posts where id=".$_GET['edit']);	
	
	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		if (!empty($row['text'])) $txt = "<textarea name=txt_area cols=120 rows=25>".$row['text']."</textarea>"; else $txt = '<font color=#eeeeee>Нет текста</font>';
		echo $txt;

		$pics = explode(';', $row['picture']);
		$img = '<table><tr>';
		foreach ($pics as $picture) {
			if (strstr($picture, '.jpg')) $img.= "<td><table><tr><td><a href=".$picture." onclick='return hs.expand(this)' class='highslide' title=''><img src=".$picture." width=70 class=fotoreport></a></td></tr><tr><td align=center><a href=del_picture.php?id=".$_GET['edit']."&p=".urlencode($picture)."&groups=".urlencode($_GET['groups']).">Удалить</a></td></tr></table></td>"; else $img.= "";
		}
		$img.= '</tr></table>';
		
		echo "<br><br><B>Ссылка на источник:</B> <a href=".$row['link'].">".$row['link']."</a><br><b>Время поста:</B> ".$row['datestamp']."<P>";
		echo "<br><br>$img<br><br>";

		echo "<br><br><button class=\"btn btn-info shiny\" onclick=\"var t = eval('txt_area'); var s = getElementById('slots".$row['id']."'); document.location.href='moveto.php?txt='+t.value+'&from=".urlencode($_GET['groups'])."&rabbit=".$row['id']."&slot=' + s.value + '&groups=".urlencode($_GET['groups'])."';return(false);\">В очередь &gt;&gt;</button> <select id='slots".$row['id']."'>".$slot_sel."</select>";	
		echo "<br><br><button class=\"btn btn-maroon shiny\" onclick=\"var t = eval('txt_area'); var s = getElementById('moveto'); document.location.href='moveto.php?txt='+t.value+'&from=".urlencode($_GET['groups'])."&rabbit=".$row['id']."&groups=' + s.value;return(false);\">Перенести в группу: </button> <select id=\"moveto\">".$all_sel."</option></select>";
		echo "<br><br><button type=submit class=\"btn btn-warning shiny\">Изменить</button></form>";
	}
?>
            </div>
        </div>
    </div>
	
	
</div>

</div>
</div>

</div>
</body></html>	