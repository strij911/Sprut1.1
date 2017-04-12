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
//left(title, strpos(title, '.')-1) as		
		$sql = "select cluster_age,cluster_sex, sum(clicks) as clicks, sum(views) as views, sum(accept_rub) as accept_rub, sum(gross_rub) as gross_rub, sum(leads_approved)as leads_approved, sum(leads_gross) as leads_gross, title, max(external_data) as external_data from cluster_stat where cluster_stat.datestamp between '".$_GET['bdate']."' and '".$_GET['edate']."' GROUP BY cluster_age, cluster_sex, title ORDER BY cluster_age, cluster_sex, title";
		
		$data = $db->query($sql);	  

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

		$objPHPExcel = new PHPExcel();
		
		$objPHPExcel->setActiveSheetIndex(0);

		$i = 1;
		
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "");
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, "Age");
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "Sex");
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "User");	
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "Views");
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "Clicks");
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, "Clicks L");			
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "Gross");
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "Approved");
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "Leads Gross");
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "Leads Approved");
			$objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);			
		$i++;

		$views = 0;
		$clicks = 0;
		$gross = 0;
		$approv = 0;
		$leads_gross = 0;
		$leads_approved = 0;

		while ($row = $data->fetch(PDO::FETCH_ASSOC)) {

			//$row['title'] = substr($row['title'], 0, strpos($row['title'], '.'));
			if ($i == 2) {
				$title = $row['title'];
				$cluster = $row['cluster_age'].$row['cluster_sex'];
		
			}
			if ($title == $row['title'] && $cluster == $row['cluster_age'].$row['cluster_sex']) {
				$views = $views + $row['views'];
				$clicks = $clicks + $row['clicks'];
				$gross = $gross + $row['gross_rub'];
				$approv = $approv + $row['accept_rub'];
				$leads_gross = $leads_gross + $row['leads_gross'];
				$leads_approved = $leads_approved + $row['leads_approved'];	
				$prev_cluster_age = $row['cluster_age'];
				$prev_cluster_sex = $row['cluster_sex'];				
				$prev_title = $row['title'];
			} else {
/*				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $prev_title );
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, show_age($prev_cluster_age));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, show_sex($prev_cluster_sex));
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $x[0]['USER']		);	
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $views);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $clicks);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, 0);			
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, round($gross,2));
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, round($approv,2));
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, round($leads_gross,2));
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, round($leads_approved,2));
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getFont()->setBold(true);			
				$i++; */
				
				$views = $row['views'];
				$clicks = $row['clicks'];
				$gross = $row['gross_rub'];
				$approv = $row['accept_rub'];
				$leads_gross = $row['leads_gross'];
				$leads_approved = $row['leads_approved'];	
				$prev_cluster_age = $row['cluster_age'];
				$prev_cluster_sex = $row['cluster_sex'];				
				$prev_title = $row['title'];
				
				$title = $row['title'];
				$cluster = $row['cluster_age'].$row['cluster_sex'];
				$t = $row['title'];
			}
			$x = json_decode($row['external_data'], true);
			$x[0]['USER'] = str_replace('amozgo_', 'Amozgo', $x[0]['USER']);
			$x[0]['USER'] = str_replace('DmVlgdnsk', 'Volgon', $x[0]['USER']);			
			$x[0]['USER'] = str_replace('VlKochetkov', 'Kochetkov', $x[0]['USER']);
			$x[0]['USER'] = str_replace('Test', 'Arb4', $x[0]['USER']);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['title']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, show_age($row['cluster_age']));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, show_sex($row['cluster_sex']));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $x[0]['USER']		);	
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row['views']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row['clicks']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, 0);			
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, round($row['gross_rub'],2));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, round($row['accept_rub'],2));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, round($row['leads_gross'],2));
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, round($row['leads_approved'],2));
			
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
