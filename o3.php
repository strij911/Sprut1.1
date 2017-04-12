<?PHP
	include "./config.inc.php";

?>

<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="./index_files/bootstrap.css">
<link rel="stylesheet" href="./index_files/other.css">
<link rel="stylesheet" href="./index_files/font-awesome.css">
<link rel="stylesheet" href="./index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="./js/ajax_func.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/mask.js" charset="UTF-8"></script>
<title>SPRUT 1.1 - статистика промо</title>
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

	$date = date("Y-m-d", time()-1*24*3600);
?>

<div class="page-content">
<div class="page-body">

<div class="row">
    <div class="col-lg-12 col-xs-12 col-md-12">
        <div class="widget radius-bordered">
            <div class="widget-header bg-danger">
                <span class="widget-caption">Статистика - <?PHP echo $date;?></span>
            </div>
            <div class="widget-body">
			
			<table class="table table-bordered table-hover dataTable no-footer">
			<thead><tr><th><B>Группа</th><th colspan=2><B>Промо</th><th>Арбитражник</th><th>Просмотры</th><th>Клики</th><th>Лиды всего</th><th>Лиды принято</th><th>Деньги всего</th><th>Деньги принято</th><th>Дата</th></tr></thead>
<?PHP
		if (get_userid() > 1) $sql = "where group_admin_id='".get_userid()."'";

		
		$v = file_get_contents('http://bigdataforall.ru/get_o5.php?hid='.$date);
		if (empty($v)) $v = file_get_contents('http://bigdataforall.ru/get_o5.php?hid='.$date);	
		$v = json_decode($v, true);

		$m = file_get_contents('http://bigdataforall.ru/get_o6.php?hid='.$date);
		if (empty($m)) $m = file_get_contents('http://bigdataforall.ru/get_o6.php?hid='.$date);	
		$m = json_decode($m, true);
		
		$promos = $db->query("select post_id, mail_post_id, picture, text, link, shedule, group_name, group_id from rabbit_posts, rabbit where rabbit.post_id=rabbit_posts.id and post_type='adv' and rabbit.shedule<='".$date." 23:59:59' and rabbit.shedule>='".$date." 00:00:00' order by group_name, text, shedule desc");
		while ($promo = $promos->fetch(PDO::FETCH_ASSOC)) {
			
			$views = 0;
			for ($i = 0; $i < count($v); $i++) {
				if (stristr($v[$i]['history_id'], $promo['mail_post_id'])) $views = $v[$i]['views'];
			}

			$money_total = 0;
			$money_accept = 0;			
			$lead_accept = 0;
			$lead_total = 0;

			for ($i = 0; $i < count($m); $i++) {
				if ($m[$i]['post_id'] == $promo['post_id']) {
					$money_total = $m[$i]['total_rub'];
					$money_accept= $m[$i]['accept_rub'];					
					$lead_accept = $m[$i]['accept_clk'];
					$lead_total = $m[$i]['total_clk'];					
					
				}
			}
			
			$u = $db->query("select login from sprut_group,users where group_admin_id=to_char(users.id, 'FM9999') and sprut_group.id=".$promo['group_id']);
			$user = $u->fetch(PDO::FETCH_ASSOC);
			
			$clicks = $db->query("select count(*) as clicks from events where datestamp>='".$date." 00:00:00' and datestamp<='".$date." 23:59:59' and post_id=".$promo['post_id']);
			$click = $clicks->fetch(PDO::FETCH_ASSOC);
			
			echo "<tr><td>".$promo['group_name']."</td><td><img src=".$promo['picture']." height=50></td><td>".substr($promo['text'], 0, 150)."</td><td>".$user['login']."</td><td>".$views."</td><td>".$click['clicks']."</td><td>$lead_total</td><td>$lead_accept</td><td>$money_total</td><td>$money_accept</td><td>".$promo['shedule'].'</td></tr>';
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