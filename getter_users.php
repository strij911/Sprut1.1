<?php
	include './parser/simple_html_dom.php';
	include "./config_wo.inc.php";

		$fields = array(
			'Login' => 'dunia.sborschikova2',
//			'Login' => 'dunia.gruppova1',
			'Domain' => 'mail.ru',
//			'Password' => 'Qwerty123');			
			'Password' => 'Qwerty123456');

		$ch = curl_init();
	   
		curl_setopt($ch, CURLOPT_URL, 'https://auth.mail.ru/cgi-bin/auth');
		curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.62 (Windows NT 6.0; U; ru) Presto/2.1.1');
		curl_setopt($ch, CURLOPT_REFERER, "https://mail.ru/");	
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie_mail.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie_mail.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
	   
		$input = curl_exec($ch);

	$buf = '';		
	$posts = $db->query("SELECT myworld_group, name from sprut_group where group_admin_id<>'1' and name like '%Мото-охота%' or name like '%Интересные факты%'");
	while ($post = $posts->fetch(PDO::FETCH_ASSOC)) {

		curl_setopt($ch, CURLOPT_URL, str_replace('//communityaccess', '/communityaccess', str_replace('http://', 'https://', $post['myworld_group']).'/communityaccess'));
		$input = curl_exec($ch);
		$letter = str_get_html(substr($input, 0, 490000));

		foreach($letter->find('span.mr20') as $e) {
			$buf.=$post['name']."|".trim(str_replace('Участники: ', '', $e->plaintext))."|";
			break;
		}		

		curl_setopt($ch, CURLOPT_URL, str_replace('//community-stat', '/community-stat', str_replace('http://', 'https://', $post['myworld_group']).'/community-stat?report=demography_gender_subscribes&from=2017-02-16&to=2017-03-16'));
		$input = curl_exec($ch);
		
		$s1 = 0.0;
		$s2 = 0.0;
		
		$sex_subs = explode("\n", $input);
		for ($i = 1; $i < count($sex_subs); $i++) {
			$sx = explode(',', $sex_subs[$i]);
			if (isset($sx[1])) {
				$s1 = $s1 + $sx[1];
				$s2 = $s2 + $sx[2];
			}
		}
		
		$buf.="Пол подписки - мужчины: ".round($s1/(count($sex_subs)-2),2)."|";
		$buf.="Пол подписки - женщины: ".round($s2/(count($sex_subs)-2),2)."|";		

		
		curl_setopt($ch, CURLOPT_URL, str_replace('//community-stat', '/community-stat', str_replace('http://', 'https://', $post['myworld_group']).'/community-stat?report=demography_gender_visits&from=2017-02-16&to=2017-03-16'));
		$input = curl_exec($ch);
		
		$s1 = 0.0;
		$s2 = 0.0;
		
		$sex_subs = explode("\n", $input);
		for ($i = 1; $i < count($sex_subs); $i++) {
			$sx = explode(',', $sex_subs[$i]);
			if (isset($sx[1])) {
				$s1 = $s1 + $sx[1];
				$s2 = $s2 + $sx[2];
			}
		}
		
		$buf.="Пол охват - мужчины: ".round($s1/(count($sex_subs)-2),2)."|";
		$buf.="Пол охват - женщины: ".round($s2/(count($sex_subs)-2),2)."|";		


		curl_setopt($ch, CURLOPT_URL, str_replace('//community-stat', '/community-stat', str_replace('http://', 'https://', $post['myworld_group']).'/community-stat?report=demography_age_subscribes&from=2017-02-16&to=2017-03-16'));
		$input = curl_exec($ch);
		
		$s1 = 0.0;
		$s2 = 0.0;
		$s3 = 0.0;
		$s4 = 0.0;
		$s5 = 0.0;
		$s6 = 0.0;
		
		$sex_subs = explode("\n", $input);
		for ($i = 1; $i < count($sex_subs); $i++) {
			$sx = explode(',', $sex_subs[$i]);
			if (isset($sx[1])) {
				$s1 = $s1 + $sx[1];
				$s2 = $s2 + $sx[2];
				$s3 = $s3 + $sx[3];				
				$s4 = $s4 + $sx[4];				
				$s5 = $s5 + $sx[5];				
				$s6 = $s6 + $sx[6];				
			}
		}
		
		$buf.="не указан: ".round($s1/(count($sex_subs)-2),2)."|";
		$buf.="до 17 лет: ".round($s2/(count($sex_subs)-2),2)."|";		
		$buf.="от 18 до 23 лет: ".round($s3/(count($sex_subs)-2),2)."|";				
		$buf.="от 24 до 34 лет: ".round($s4/(count($sex_subs)-2),2)."|";				
		$buf.="от 35 до 43 лет: ".round($s5/(count($sex_subs)-2),2)."|";						
		$buf.="44 и старше: ".round($s6/(count($sex_subs)-2),2)."\n";								
		
	}	
	$x = file_put_contents('redirect.log', $buf, FILE_APPEND);