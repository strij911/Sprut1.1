<?PHP
	include "./config.inc.php";
	header('Content-Type: text/html; charset=utf-8');

	function show_img($id) {
		include "./config.inc.php";
		$buf = '';

		$u = $db->query("SELECT * FROM pictures where product_id=".$id);
		while($list = $u->fetch(PDO::FETCH_ASSOC)) {
			$buf.= "<img src=http://88.198.139.146/sprut1".$list['picture']." height=30>";
		}	
		return($buf);
	}
	
	function show_pics($id) {
		include "./config.inc.php";
		$buf = '';

		$u = $db->query("SELECT * FROM pictures where product_id=".$id);
		while($list = $u->fetch(PDO::FETCH_ASSOC)) {
			$buf.= "<table align=left><tr><td bgcolor=#efefef><a href=http://88.198.139.146/sprut1".$list['picture']." onclick='return hs.expand(this)' class='highslide' title=''><img src=http://88.198.139.146/sprut1".$list['picture']." height=70 class=fotoreport></a><br> <label><input name=\"fPic$id\" type=\"radio\" value=\"".$list['id']."\"><span class=\"text\"><font style=\"font-size:9px;\">Отметить</font></span></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=del_pic.php?pid=".$list['id']." title='Удалить'><i class=\"menu-icon glyphicon glyphicon-trash\"></i></td><td>&nbsp;</td></tr></table>";
		}	
		return($buf);
	}

	function show_links($id) {
		include "./config.inc.php";
		$buf = '';

		$u = $db->query("SELECT * FROM urls where product_id=".$id);
		while($list = $u->fetch(PDO::FETCH_ASSOC)) {
			$buf.= "<label><input name=\"fLink$id\" type=\"radio\" value=\"".$list['id']."\"> <a href=\"edit_link.php?id=".$list['id']."\" class=\"btn btn-info\"><span class=\"menu-text\"> Редактировать</span></a> <span class=\"text\">&nbsp;".substr($list['url'],0,200)."</span></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=del_link.php?pid=".$list['id']." title='Удалить'><i class=\"menu-icon glyphicon glyphicon-trash\"></i></a> <p style=\"margin-top:2px;\">";
		}	
		return($buf);
	}

	function show_only_txt($id) {
		include "./config.inc.php";
		$buf = '';

		$u = $db->query("SELECT * FROM texts where product_id=".$id);
		while($list = $u->fetch(PDO::FETCH_ASSOC)) {
			$buf.= mb_substr($list['text'],0,230)."";
		}	
		return($buf);
	}
	
	function show_txt($id) {
		include "./config.inc.php";
		$buf = '';

		$u = $db->query("SELECT * FROM texts where product_id=".$id);
		while($list = $u->fetch(PDO::FETCH_ASSOC)) {
			$buf.= "<label><input name=\"fText$id\" type=\"radio\" value=\"".$list['id']."\"> <a href=\"edit_txt.php?id=".$list['id']."\" class=\"btn btn-info\"><span class=\"menu-text\"> Редактировать</span></a>  <span class=\"text\">&nbsp;".mb_substr($list['text'],0,230)."</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=del_txt.php?pid=".$list['id']."  title='Удалить'><i class=\"menu-icon glyphicon glyphicon-trash\"></i></a> <p style=\"margin-top:2px;\">";
		}	
		return($buf);
	}
	
?>

<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="./index_files/bootstrap.css">
<link rel="stylesheet" href="./index_files/other.css">
<link rel="stylesheet" href="./index_files/font-awesome.css">
<link rel="stylesheet" href="./index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="./index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/mask.js" charset="UTF-8"></script>
<title>SPRUT 1.1 - Видео продукты</title>
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
                <span class="widget-caption">Мои видео продукты</span>
            </div>
            <div class="widget-body">
				<table class="table table-bordered table-hover dataTable no-footer">
				<a href="create_prod_vid.php" class="btn btn-danger"><i class="menu-icon glyphicon glyphicon-book"></i><span class="menu-text"> Создать новый видео продукт</span></a> <br>	<br>			
<?PHP
		
		if (get_userid() > 1) $usr = "user_id=".get_userid(); else $usr = '1=1';
		$u = $db->query("SELECT * FROM products where prod_type='vid' and $usr ORDER BY datestamp desc");
		while($list = $u->fetch(PDO::FETCH_ASSOC)) {
			if (!empty($_GET['pid']) && $list['id'] == $_GET['pid']) {
				echo '<tr><td colspan=2><font size=+1><B>'.$list['title'].'</B></td></tr><tr><td colspan=3><br></td></tr><tr><td valign=top colspan=3>';
				
				echo "<P><br><a href=\"add_txt_vid.php?pid=".$list['id']."\" class=\"btn btn-success\"><i class=\"menu-icon glyphicon glyphicon-book\"></i><span class=\"menu-text\"> Добавить текст</span></a>";			
				echo '<p><div style="width: 1200px; height: 150px; overflow-y: scroll;">';

				echo show_txt($list['id']);
				
				echo "</div><input type=hidden name=pid value=".$list['id'].">";
				echo '</form>';
				echo "</td><tr><td colspan=3><hr width=100%></td></tr>";
			} else {
				echo '<tr><td width=20%><a href=products_vid.php?pid='.$list['id'].'#active><B>'.$list['title'].'</B></a></td></tr>';
			}	
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