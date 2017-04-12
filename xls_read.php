<?PHP
	include "config.inc.php";
	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
	require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

	function recursive_array_search($needle,$haystack) {
		for ($i = 0; $i < count($haystack); $i++) 
			for ($j = 0; $j < count($haystack[$i]); $j++) {
				if (strstr($haystack[$i][$j], $needle)) {
					return(array($j,$i));
				}
			}
	}
	
	function search_hours($xls, $j) {
		if ($j > 4 && $j < 17) return($xls[0][5]);
		while (!array_search($xls[0][$j], array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23)) && $j > 0) $j--;
		return($xls[0][$j]);
	}
	
    if (!empty($_FILES['userfile']['tmp_name'])) {
		$uploaddir = './xlsx/';
		
		$fn = '';
		$uploadfile = $uploaddir . basename(str_replace(' ', '', $fn.$_FILES['userfile']['name']));

		if (file_exists('./xlsx'))
		foreach (glob('./xlsx/*.xlsx') as $file)
		unlink($file);
		
		move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);

		log_event("Настройки $uploadfile успешно загружены!");
	
		$out = "";
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel = PHPExcel_IOFactory::load($uploadfile);

		$xls = $objPHPExcel->getActiveSheet()->toArray();
		$x = recursive_array_search('Автопостинг', $xls);
		
		$i = $x[1]+1;
		while (!empty($xls[$i][0])) {
			$out.="<font color=white><br>".$xls[$i][0].":";
			$sql.= "UPDATE sprut_group SET ";
			if (stristr($xls[$i][1], 'Да')) {
				$out.="Автопостинг: да;";
				$sql.='state=2';
			} else {
				$out.="Автопостинг: нет;";
				$sql.='state=1';
			}
			if (!empty($xls[$i][2])) {
				$out.="Горизонт: ".$xls[$i][2].";";		
				$sql.=',horizon='.$xls[$i][2];
			}
			if (!empty($xls[$i][3])) {
				$out.="Редактор: ".$xls[$i][3].";";
				$posters = $db->query("SELECT id from users where login='".$xls[$i][3]."'");
				$poster = $posters->fetch(PDO::FETCH_ASSOC);
				$post = $poster['id'];
			}
			if (!empty($xls[$i][3])) {
				$out.="Арбитражник: ".$xls[$i][4].";";				
				$posters = $db->query("SELECT id from users where login='".$xls[$i][4]."'");
				$poster = $posters->fetch(PDO::FETCH_ASSOC);
				$advert = $poster['id'];
			}
			
			$shedule = '';
			$shedule_ads = '';
			
			for ($j = 5; $j < 15*24; $j++) {
				if (stristr($xls[$i][$j], 'C')) {
					$s = search_hours($xls, $j).":".$xls[1][$j].';';
					$out.= $s;
					$shedule.=$s;
				}
				if (stristr($xls[$i][$j], 'A')) {
					$s = search_hours($xls, $j).":".$xls[1][$j].';';
					$out.= $s;
					$shedule_ads.=$s;
				}
				
			}
			
			$sql.=",shedule='".$shedule."',ad_shedule='".$shedule_ads."', poster_id='".$post."', group_admin_id='".$advert."' where name='".$xls[$i][0]."';";
			echo $sql.PHP_EOL;
			//$slots = $db->query($sql);
			$sql = '';
			$i++;
		} 
	} //if		
?>

<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="./index_files/bootstrap.css">
<link rel="stylesheet" href="./index_files/other.css">
<link rel="stylesheet" href="./index_files/font-awesome.css">
<link rel="stylesheet" href="./index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="./index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/mask.js" charset="UTF-8"></script>
<title>SPRUT 1.1 - Настройки групп</title>
</head>
<body>

<div class="main-container container-fluid">

<?PHP include "sidebar.inc";?>

<div class="page-content">
<div class="page-body">

<div class="row">
    <div class="col-lg-9 col-xs-12 col-md-12">
        <div class="widget radius-bordered">
		<button class="btn btn-success shiny" onclick="document.location.href='load_from_google.php';">Загрузить из Google docs</button><br><br>
            <div class="widget-header bg-danger">

			<form name="alias" method="post" ENCTYPE="multipart/form-data">
                        &nbsp;               <div class="row">
<?PHP echo $out; ?>						
                  <div class="col-sm-6">
                     <div class="form-group">
<br><br><INPUT TYPE="hidden" name="MAX_FILE_SIZE" value=5000000>
<input class="form-control" placeholder="Файл" TYPE="file"  id="userfile" NAME="userfile" value="" autocomplete="off" style="height:40px;"><span class="input-group-addon">Укажите файл в формате xlsx
<button type="submit" class="btn btn-default shiny" name="submit">Добавить</button>
&nbsp;&nbsp;&nbsp;&nbsp;<a href=https://docs.google.com/spreadsheets/d/1s0Q2FvxtLwwFdPxNvrqcnvcoyIgdkQvkZpMqG-hCqYo/export?format=xlsx&id=1s0Q2FvxtLwwFdPxNvrqcnvcoyIgdkQvkZpMqG-hCqYo>Скачать текущие настройки</a>
                     </div>
                  </div>
               
            </form>
			
			
            </div>
        </div>
    </div>
	
</div>

</div>
</div>

</div></div>
</body></html>