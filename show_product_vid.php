<?PHP
	include "./config.inc.php";

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
			$buf.= "<table align=left><tr><td bgcolor=#efefef><a href=http://88.198.139.146/sprut1".$list['picture']." onclick='return hs.expand(this)' class='highslide' title=''><img src=http://88.198.139.146/sprut1".$list['picture']." height=70 class=fotoreport></a><br> <label><input id=\"fPic$id\" name=\"fPic$id\" type=\"radio\" value=\"".$list['id']."\"><span class=\"text\"><font style=\"font-size:9px;\">Отметить</font></span></label></td><td>&nbsp;</td></tr></table>";
		}	
		return($buf);
	}

	function show_links($id) {
		include "./config.inc.php";
		$buf = '';

		$u = $db->query("SELECT * FROM urls where product_id=".$id);
		while($list = $u->fetch(PDO::FETCH_ASSOC)) {
			$buf.= "<label><input name=\"fLink$id\" type=\"radio\" value=\"".$list['id']."\"> <span class=\"text\">&nbsp;".substr($list['url'],0,200)."</span></label> <p style=\"margin-top:2px;\">";
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
			$buf.= "<label><input id=\"fText$id\" name=\"fText$id\" type=\"radio\" value=\"".$list['id']."\"> <span class=\"text\">&nbsp;".mb_substr($list['text'],0,230)."</span><p style=\"margin-top:2px;\">";
		}	
		return($buf);
	}
	
		$u = $db->query("SELECT * FROM products where id=".$_GET['pid']);
		while($list = $u->fetch(PDO::FETCH_ASSOC)) {
				
				echo '<p><B>Текст:</B><div style="width: 1200px; height: 150px; overflow-y: scroll;">';
				echo show_txt($list['id']);
				echo "</div>";
		}
