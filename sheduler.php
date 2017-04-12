<?PHP
	include "./config.inc.php";
		
	$m = $db->query("SELECT * from rabbit order by shedule");
	
?><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="./index_files/bootstrap.css">
<link rel="stylesheet" href="./index_files/other.css">
<link rel="stylesheet" href="./index_files/font-awesome.css">
<link rel="stylesheet" href="./index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="./index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/mask.js" charset="UTF-8"></script>
<title>SPRUT 1 - Очередь публикации</title>
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
                <span class="widget-caption">Очередь публикации</span>
            </div>
            <div class="widget-body">
				<table class="table table-bordered table-hover dataTable no-footer">			
				<thead><th><B>ID<th><B>Текст<th><B>Картинка<th><B>Группа<th><B>Время публикации</th></thead>
<?PHP				
	while ($row = $m->fetch(PDO::FETCH_ASSOC)) {
		$m1 = $db->query("SELECT * from posts where id=".$row['post_id']);
		$row1 = $m1->fetch(PDO::FETCH_ASSOC);
		if (!empty($row1['picture'])) $img = "<img src=http://advertmania.com/".$row1['picture']." height=50>"; else $img = "";
		if (!empty($row1['text'])) $txt = $row1['text']; else $txt = '<font color=#eeeeee>Нет текста</font>';
		echo "<tr><td>".$row['ID']."</td><td>".$txt."</td><td>$img</td><td nowrap>".$row1['group_name']."</td><td nowrap>".$row['shedule']."</td></tr>";
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