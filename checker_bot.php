<?php

	include "./config_wo.inc.php";
	
	function mr_auth() {
		$ch = curl_init();
	   
		$url = 'http://win.mail.ru/cgi-bin/start';
	   
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie_mail.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie_mail.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	   
		curl_exec($ch);
	   
		$last = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	   
		curl_close($ch);
	   
		return ($url == $last);
	}
	 
	function mr_login($email, $passw, $url) {
	   
		$params = explode('@', $email);
	 
		if(count($params) != 2 || empty($passw))
			return false;
	 
		$fields = array(
			'Login' => $params[0],
			'Domain' => strtolower($params[1]),
			'Password' => $passw);

		$ch = curl_init();
	   
		curl_setopt($ch, CURLOPT_URL, 'https://auth.mail.ru/cgi-bin/auth');
		curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.62 (Windows NT 6.0; U; ru) Presto/2.1.1');
		curl_setopt($ch, CURLOPT_REFERER, "https://mail.ru/");	
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie_mail.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie_mail.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
	   
		$input = curl_exec($ch);
		
		curl_setopt($ch, CURLOPT_URL, 'https://my.mail.ru');
		$input = curl_exec($ch);
		$input = substr($input, strpos($input, '"mna":'));
		$input = '{'.substr($input, 0, strpos($input, ',"myhost":')).'}';
		$mn = json_decode($input, true);

		$params = [
		   'ajax_call' => 1,
		   'func_name' => 'groups.join',
		   'arg_ref' => '',
		   'mna' => $mn['mna'],
		   'mnb' => $mn['mnb'],
		   '_' => time()
		];

		$url = $url.'/ajax?'.http_build_query($params);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest"));

		$result = curl_exec($ch);

		$h_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		
		curl_close($ch);
	 
		return substr($result, $h_len, strlen($result));
	}
	 
	$posts = $db->query("SELECT myworld_group from sprut_group");

	while ($post = $posts->fetch(PDO::FETCH_ASSOC)) {

	for ($i = 1; $i < 3; $i++)
		for ($j = 1; $j < 14; $j++) {

			if ($i == 1) $sex = 'M'; else $sex = 'F';
			$age = $j;
			$email = 'sprut2'.$sex.$age.'@mail.ru';

			$z = mr_login($email, 'Qwerty123', str_replace('http://', 'https://', $post['myworld_group']));
			$x = json_decode($z, true);
			echo '<br>'.$z;
		}
	}	