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

	if ($_GET['sb'] == 1) {
		$uploaddir = './xlsx/';
		
		$fn = '';
		$uploadfile = $uploaddir . basename(str_replace(' ', '', $fn.$_FILES['userfile']['name']));

		if (file_exists('./xlsx'))
		foreach (glob('./xlsx/*.xlsx') as $file)
		unlink($file);
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel = PHPExcel_IOFactory::load($uploadfile);

		$xls = $objPHPExcel->getActiveSheet()->toArray();
		$x = recursive_array_search('Автопостинг', $xls);
		
		$i = $x[1]+1;
		while (!empty($xls[$i][0])) {
			for ($j = 5; $j < 15*24; $j++) {
				if (stristr($xls[$i][$j], 'C')) {
					$s = search_hours($xls, $j).":".$xls[1][$j].';';
				}
				if (stristr($xls[$i][$j], 'A')) {
					$s = search_hours($xls, $j).":".$xls[1][$j].';';
				}
				
			}
			
			//$sql.=",shedule='".$shedule."',ad_shedule='".$shedule_ads."', poster_id='".$post."', group_admin_id='".$advert."' where name='".$xls[$i][0]."'";
			//echo $sql.PHP_EOL;
			//$slots = $db->query($sql);
			//$sql = '';
			$i++;
		} 
	} //if		
