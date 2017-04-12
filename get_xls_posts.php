<?PHP
exit;
	//include "config.inc.php";
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
	
		$uploaddir = './xlsx/';
		
		if (file_exists('./xlsx'))
		foreach (glob('./xlsx/*.xlsx') as $file) $uploadfile = $file;

		$objPHPExcel = new PHPExcel();
		$objPHPExcel = PHPExcel_IOFactory::load($uploadfile);

		$xls = $objPHPExcel->getActiveSheet()->toArray();
		$x = recursive_array_search('Автопостинг', $xls);
		
		
		$i = $x[1]+1;
		while (!empty($xls[$i][0])) 
		
		{
			for ($j = 5; $j < 15*24; $j++) {
				if (stristr($xls[$i][$j], 'C')) {
					$xls[$i][$j] = '';
				}
				if (stristr($xls[$i][$j], 'A')) {
					$xls[$i][$j] = '';
				}
				
			}
			
		} 
	
		$objPHPExcel->getActiveSheet()->fromArray($xls, NULL);
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.date("Y-m-d").'.xlsx"');
		header('Cache-Control: max-age=0');
		
		header('Cache-Control: max-age=1');

		
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save("2201.xlsx");
		exit;		
