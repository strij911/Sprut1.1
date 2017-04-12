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
<title>SPRUT 1.1 - статистика групп</title>
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
	if (!isset($_GET['date'])) $date = date("Y-m-d", time()-1*24*3600); else $date = $_GET['date'];
?>

<div class="page-content">
<div class="page-body">

<div class="row">
    <div class="col-lg-12 col-xs-12 col-md-12">
        <div class="widget radius-bordered">
            <div class="widget-header bg-danger">
                <span class="widget-caption">Статистика - <?PHP echo $date;?></span>
				<select name=date onchange="document.location.href='/sprut1/o2.php?date='+this.value;">
<?PHP 

	for ($i = 1; $i < 10; $i++) {
		$date1 = date("Y-m-d", time()-$i*24*3600);
		echo "<option value=\"".$date1."\">".$date1;
	}

?></option></select>
            </div>
            <div class="widget-body">
<?PHP

	$out = '';
	
	$m = file_get_contents('http://bigdataforall.ru/get_o6.php?hid='.$date);
	$m = json_decode($m, true);

	$promos = $db->query("select count(events.post_id) as clicks, events.post_id, max(rabbit_posts.datestamp) as datestamp, max(text) as text, max(rabbit.mail_post_id) as mid from rabbit_posts,events,rabbit where rabbit.post_id=rabbit_posts.id and rabbit_posts.id=events.post_id and events.datestamp>='".$date." 00:00:00' and events.datestamp<='".$date." 23:59:59' group by events.post_id");
			
	while ($promo = $promos->fetch(PDO::FETCH_ASSOC)) {
		$mid[] = $promo['mid'];
	}
			
	$fields = array(
		'mid' => implode('|', $mid),
		'date' => $date,
	);

	$ch = curl_init();
	   
	curl_setopt($ch, CURLOPT_URL, 'http://77.232.23.74/bigdataforall/get_o16.php');
	curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.62 (Windows NT 6.0; U; ru) Presto/2.1.1');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
	   
	$input = curl_exec($ch);
	$mx = json_decode($input, true);
	
	$u = $db->query("select id, login from users where role like '%advert%'");

	while ($user = $u->fetch(PDO::FETCH_ASSOC)) {
		$user_clicks = 0;
		$user_views = 0;
		$user_all = 0;
		$user_accept = 0;			
		$user_rub_all = 0;		
		$user_rub_accept = 0;
			
		$out.= "<table class=\"table table-bordered table-hover dataTable no-footer\">";		
		$out.= '<tr><td width=30%><font size=+1><a href=# onclick="'."var z=document.getElementById('pl_".$user['id']."'); if (z.style.display == '') z.style.display = 'none'; else z.style.display = '';return(false);\"><i class=\"menu-icon glyphicon glyphicon-plus-sign \"></i></a> <B>".$user['login'].'</B></td><td width=10%> views: <B>_'.$user['login'].'|views_</B></td><td width=10%> clicks: <B>_'.$user['login'].'|clicks_</B></td><td width=10%> total_leads: <B>_'.$user['login'].'|totall_</B></td><td width=10%> accept_leads: <B>_'.$user['login'].'|accl_</B></td><td width=10%> total_rubs: <B>_'.$user['login'].'|totalr_</B></td><td width=10%> accept_rub: <B>_'.$user['login'].'|accr_</b></font></td></tr></table><br><div id=pl_'.$user['id'].' style="display:none;">';
		
 		$m1 = $db->query("SELECT id as group_id, name as group_name, myworld_group as myword from sprut_group where group_admin_id='".$user['id']."' order by name");
		while ($group = $m1->fetch(PDO::FETCH_ASSOC)) {
			$out.= "<div style=\"display:none;\" id=\"plx_".$group['group_id']."\"><table class=\"table table-bordered table-hover dataTable no-footer\"><thead><tr><th><B>Группа</th><th>Слот</th><th>Промо</th><th>Показы</th><th>Клики</th><th>Лиды всего</th><th>Лиды принято</th><th>Деньги всего</th><th>Деньги принято</th><th>RPM Gross</th><th>RPM Net</th><th>CTR, %</th></tr></thead>";		
			
			$total_clicks = 0;		
			$total_views = 0;
			$total_all = 0;
			$total_accept = 0;			
			$total_rub_all = 0;		
			$total_rub_accept = 0;
		
			$promos = $db->query("select count(distinct identity) as clicks, events.post_id, max(rabbit_posts.datestamp) as datestamp, max(text) as text, max(rabbit.mail_post_id) as mid from rabbit_posts,events,rabbit where rabbit.post_id=rabbit_posts.id and rabbit_posts.id=events.post_id and events.datestamp>='".$date." 00:00:00' and events.datestamp<='".$date." 23:59:59' and group_name='".$group['group_name']."' group by events.post_id order by 3 desc");

			$j = 0;
			while ($promo = $promos->fetch(PDO::FETCH_ASSOC)) {

				$views = 0;
				$money_total = 0;
				$money_accept = 0;			
				$lead_accept = 0;
				$lead_total = 0;

				for ($i = 0; $i < count($m); $i++) {
					if ($m[$i]['post_id'] == $promo['post_id']) {
						$money_total = $money_total + $m[$i]['total_rub'];
						$money_accept = $money_accept + $m[$i]['accept_rub'];					
						$lead_accept = $lead_accept + $m[$i]['accept_clk'];
						$lead_total = $lead_total + $m[$i]['total_clk'];
						
					}
				}
				$out.= "<tr><td>". $group['group_name'] ."</td><td>".$promo['datestamp']."</td><td><a href=". $group['myword'] ."/multipost/".strtoupper($promo['mid']).".html>".substr($promo['text'], 0, 65)."</a></td><td>".$mx[$promo['mid']]."</td><td>".$promo['clicks']."</td><td>$lead_total</td><td>$lead_accept</td><td>$money_total</td><td>$money_accept</td><td>".round($money_total/$mx[$promo['mid']]*1000, 2).'</td><td>'.round($money_accept/$mx[$promo['mid']]*1000, 2).'</td><td>'.round($promo['clicks']*100/$mx[$promo['mid']], 2).'</td></tr>';
				
				$total_clicks = $total_clicks + $promo['clicks'];		
				$total_views = $total_views + $mx[$promo['mid']];
				$total_rub_accept = $total_rub_accept + $money_accept;			
				$total_rub_all = $total_rub_all + $money_total;
				$total_all = $total_all + $lead_total;
				$total_accept = $total_accept + $lead_accept;			

				$user_clicks = $user_clicks + $promo['clicks'];
				$user_views = $user_views + $mx[$promo['mid']];				
				$user_all = $user_all + $lead_total;
				$user_accept = $user_accept + $lead_accept;			
				$user_rub_all = $user_rub_all + $money_total;		
				$user_rub_accept = $user_rub_accept + $money_accept;				
				$j++;
			}					
		
			$out.= "</table></div>";
			$out.= "<table class=\"table table-bordered table-hover dataTable no-footer\">";						
			$out.= "<tr><td colspan=3 width=40%><B><font size=+1><a href=# onclick=\"var z=document.getElementById('plx_".$group['group_id']."'); if (z.style.display == '') z.style.display = 'none'; else z.style.display = ''; return(false); \"><i class=\"menu-icon glyphicon glyphicon-plus-sign \"></i></a> Итого по ". $group['group_name'] ."</td><td width=8%><B>".$total_views."</td><td width=8%><B>".$total_clicks."</td><td width=8%><B>".$total_all."</td><td width=8%><B>".$total_accept."</td><td width=8%><B>".$total_rub_all."</td><td width=8%><B>".$total_rub_accept."</td><td width=8%><B>".round($total_rub_all/$mx[$promo['mid']]*1000, 2).'</td><td width=8%><B>'.round($total_rub_accept/$total_views*1000, 2).'</td><td width=8%><B>'.round($total_clicks*100/$total_views, 2).'</td></tr>';
			$out.= "</table>";			
		}
		$out.= "</table></div>";

		$out = str_replace('_'.$user['login'].'|clicks_', $user_clicks, $out);
		$out = str_replace('_'.$user['login'].'|views_', $user_views, $out);		
		$out = str_replace('_'.$user['login'].'|totall_', $user_all, $out);		
		$out = str_replace('_'.$user['login'].'|accl_', $user_accept, $out);				
		$out = str_replace('_'.$user['login'].'|accr_', $user_rub_accept, $out);		
		$out = str_replace('_'.$user['login'].'|totalr_', $user_rub_all, $out);		
		
		
	}
	
	echo $out;
?>					
            </div>
        </div>
    </div>
	
</div>

</div>
</div>

</div>
</body></html>