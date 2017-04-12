<?PHP
	function show_number($x) {
	    if (is_numeric($x)) return(round($x, 2)); else return(0);
	}

	function show_age($id) {
		switch ($id) {
			case 1: $txt = '0-10'; break;
			case 2: $txt = '11-15'; break;
			case 3: $txt = '16-20'; break;
			case 4: $txt = '21-25'; break;
			case 5: $txt = '26-30'; break;
			case 6: $txt = '31-35'; break;
			case 7: $txt = '36-40'; break;
			case 8: $txt = '41-45'; break;
			case 9: $txt = '46-50'; break;
			case 10: $txt = '51-55'; break;
			case 11: $txt = '56-60'; break;
			case 12: $txt = '61-65'; break;			
			case 13: $txt = '66+'; break;

		}
		return($txt);
	}

	function show_country($s) {
		if (strstr($s, 'fCountry0')) $txt.= 'BL ';
		if (strstr($s, 'fCountry1')) $txt.= 'KZ ';		
		if (strstr($s, 'fCountry2')) $txt.= 'Rus ';				
		if (strstr($s, 'fCountry3')) $txt.= 'UKR ';	
		if ($txt == 'BL KZ Rus UKR ') $txt = 'All';
		return($txt);
	}
	
	function show_sex($id) {
		switch ($id) {
			case 1: $txt = 'Male'; break;
			case 2: $txt = 'Female'; break;
		}
		return($txt);
	}

	function show_pic($id) {
		global $db;
		$sql = "select max(pictures.picture) AS pic from publications, products, pictures where pictures.product_id=publications.product_id AND publications.product_id=products.id AND publications.id=$id";
			  
		$data = $db->query($sql);	  
		$row = $data->fetch(PDO::FETCH_ASSOC);

		$buf = "<a href=".$row['pic']." onclick='return hs.expand(this)' class='highslide' title=''><img src=".$row['pic']." height=30 class=fotoreport></a>";

		return($buf);
	}

	function show_user($id) {
		global $db;
		$sql = "select login from users, products, publications where products.id=publications.product_id and products.user_id=users.id and publications.id=$id";
			  
		$data = $db->query($sql);	  
		$row = $data->fetch(PDO::FETCH_ASSOC);
		return($row['login']);
	}
	
	if (!empty($_GET['sb'])) {
		include "../config_wo.inc.php";
		
		$sql = "select *, cluster_stat.datestamp as cl_date from cluster_stat, publications, urls where publications.url=urls.id and publications.id=cluster_stat.publication_id and cluster_stat.datestamp between '".$_GET['bdate']."' and '".$_GET['edate']."' ORDER BY urls.id, cluster_age, cluster_sex";
		
		$data = $db->query($sql);	  

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

		$objPHPExcel = new PHPExcel();
		
		$objPHPExcel->setActiveSheetIndex(0);

		$mans = '<table id=tbsort class="table table-bordered table-hover dataTable no-footer"><thead><tr><th><b>ID<th><b>Promo<th><b>URL<th><B>Age<th><B>Sex<th><B>Geo<th><B>Partner<th><B>User<th><B>Views<th><B>Clicks<th><B>Gross<th><B>Approved<th><B>Leads Gross<th><B>Leads Approved<th><B>AV.lead<th><B>%Aprv<th><B>CTR<th><B>RPM</tr>';

		$i = 1;
		
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "Promo");
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, "Age");
//			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "Date");			
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "Sex");
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "Geo");
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "Partner");
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "User");	
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, "Views");
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "Clicks");
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "Clicks L");			
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "Gross");
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "Approved");
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "Leads Gross");
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "Leads Approved");
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, "AV.lead");
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, "%Aprv");
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, "CTR");
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "RPM");
			$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFont()->setBold(true);			
		$i++;
		
		while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
			
			$x = json_decode($row['external_data'], true);
			
			if (empty($x[0]['USER'])) {
				$title = substr($row['title'], 0, strpos($row['title'], '.'));
				
				$sql = "select * from cluster_stat where title like '".$title."%' and external_data <>'' order by datestamp desc";
				$data1 = $db->query($sql);	  				
				$rowx = $data1->fetch(PDO::FETCH_ASSOC);
				$xs = json_decode($rowx['external_data'], true);
				$x[0]['USER'] = $xs[0]['USER'];
				//$x[0]['USER'] = $sql;
			}
			$x[0]['USER'] = str_replace('amozgo_', 'Amozgo', $x[0]['USER']);
			$x[0]['USER'] = str_replace('DmVlgdnsk', 'Volgon', $x[0]['USER']);			
			$x[0]['USER'] = str_replace('VlKochetkov', 'Kochetkov', $x[0]['USER']);
			$x[0]['USER'] = str_replace('Test', 'Arb4', $x[0]['USER']);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['title']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, show_age($row['cluster_age']));
//			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, date("d.m.Y", strtotime($row['cl_date'])));			
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, show_sex($row['cluster_sex']));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, show_country($row['target']));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $x[0]['PARTNERKA']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $x[0]['USER']		);	
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['views']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['clicks']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, 0);			
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, round($row['gross_rub'],2));
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, round($row['accept_rub'],2));
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, round($row['leads_gross'],2));
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, round($row['leads_approved'],2));
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, ($row['leads_approved'] > 0?show_number($row['accept_rub']/$row['leads_approved']):0));
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, ($row['gross_rub'] > 0?show_number($row['accept_rub']/$row['gross_rub']*100):0)."%");
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, round($row['clicks']/$row['views']*100,2)."%");
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, round($row['accept_rub']/$row['views']*1000,2));
			
			$i++;
		}			

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.date("Y-m-d").'.xlsx"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

	}
