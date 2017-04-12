<?PHP
	include "./config.inc.php";
		
	if (isset($_POST['id'])) {
			$m = $db->query("update rabbit_posts set text = '".$_POST['txt']."' where id = ".$_POST['id']);
			$m = $db->query("update posts set text = '".$_POST['txt']."' where id = ".$_POST['id']);			
			header("Location: poster.php?groups=".$_GET['groups']);
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
	
	$m = $db->query("SELECT * from rabbit_posts where id=".$_GET['edit']);	
	
	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		if (!empty($row['text'])) $txt = "<textarea name=txt cols=120 rows=25>".$row['text']."</textarea>"; else $txt = '<font color=#eeeeee>Нет текста</font>';
		echo $txt;

		$pics = explode(';', $row['picture']);
		$img = '<table><tr>';
		foreach ($pics as $picture) {
			if (strstr($picture, '.jpg')) $img.= "<td><table><tr><td><a href=".$picture." onclick='return hs.expand(this)' class='highslide' title=''><img src=".$picture." width=70 class=fotoreport></a></td></tr><tr><td align=center><a href=del_picture_rb.php?id=".$_GET['edit']."&p=".urlencode($picture)."&groups=".urlencode($_GET['groups']).">Удалить</a></td></tr></table></td>"; else $img.= "";
		}
		$img.= '</tr></table>';
		
		echo "<br><br><B>Ссылка на источник:</B> <a href=".$row['link'].">".$row['link']."</a><br><b>Запланированное время выхода поста:</B> ".$row['datestamp']."<P>";
		echo "<br><br>$img<br><br>";
		echo "&nbsp;<button type=submit id=\"btn123\" class=\"btn btn-warning shiny\">Изменить</button></form>";	
		$datestamp = $row['datestamp'];
	}
?>
            </div>
        </div>
    </div>
	
<script type="text/javascript">
	function func() {
		var startDate = new Date();

		var endDate   = new Date(<?PHP echo date("Y", strtotime($datestamp));?>, <?PHP echo date("m", strtotime($datestamp))-1;?>, <?PHP echo date("d", strtotime($datestamp));?>, <?PHP echo date("H", strtotime($datestamp));?>, <?PHP echo date("i", strtotime($datestamp));?>, <?PHP echo date("s", strtotime($datestamp));?>, 0);
		var seconds = Math.round((endDate.getTime() - startDate.getTime()) / 1000);
		
		var s = document.getElementById('btn123');
  	    if (seconds < 0) {
			s.innerHTML = 'Пост уже отправлен!';
			s.className = 'btn btn-danger shiny';
			s.disabled = true;
		} else s.innerHTML = 'Осталось: '+(seconds) + ' секунд';
		setTimeout(func, 1000);
	}
	
	setTimeout(func, 1000);
</script>
	
</div>

</div>
</div>

</div>
</body></html>	