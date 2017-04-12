<?PHP
	function sign_server_server(array $request_params, $secret_key) {
	  ksort($request_params);
	  $params = '';
	  foreach ($request_params as $key => $value) {
		$params .= "$key=$value";
	  }
	  return md5($params . $secret_key);
	}

	function api_request($photo, $uid, $aid) {	
	
		include "include/application.inc.php";	
		
		$ch = curl_init();
		ob_start(); 		

	
		$params = array(
			'method'  => 'photos.upload',
			'aid'	  => "$aid",
			'img_url' => $photo
		);		
		
		$dprm = array( 'app_id' => $app_id,
						 'secure' => 1,
						 'uid'    => $uid 
		);

		$params = array_merge($params, $dprm);

		$sig = sign_server_server($params, $secret);

		$p = '';

		foreach ($params as $key => $value) {
			$p.= "$key=".urlencode($value)."&";
		}

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
		
		$s = "http://www.appsmail.ru/platform/api?sig=".$sig;

		if (filesize('log_api.txt') < 1024*8192) {
			$x = file_put_contents('log_api.txt', date("Y-m-d H:i:s").":".$p.PHP_EOL, FILE_APPEND);
		} else {
			copy('log_api.txt.old1', 'log_api.txt.old2');
			copy('log_api.txt.old', 'log_api.txt.old1');
			copy('log_api.txt', 'log_api.txt.old');
			$x = file_put_contents('log_api.txt', date("Y-m-d H:i:s").":".$p.PHP_EOL);
		}
		
		curl_setopt($ch, CURLOPT_URL, $s);

		curl_exec($ch);
		$input = ob_get_contents(); 
		ob_end_clean();
		$x = file_put_contents('log_api.txt',  date("Y-m-d H:i:s").":"."Ответ:".$input.PHP_EOL.PHP_EOL, FILE_APPEND);
		return trim($input);	
		curl_close ($ch);
	}
	
	$picture = str_replace('pp.vk.me', 'pp.userapi.com', $_GET['picture']);
	$picture = str_replace('cs7055.vk.me', 'pp.userapi.com', $picture);	
	$picture = str_replace('cs541603.vk.me', 'pp.userapi.com', $picture);
	$p = explode(";", $picture);
	
	$all = array();
	
	for ($i = 0; $i < min(count($p), 2); $i++) { // максимальное количесво фото в посте
		if (!empty(trim($p[$i]))) {
			$s = api_request($p[$i], $_GET['uid'], $_GET['aid']);
			if (strstr($s, 'incorrect')) {
				$all[0] = 'error';
				break;
			}
			$x = json_decode($s, true);
			if (!empty($x['owner'])) $all[] = $x['owner'].':'.$x['pid'];
		}
	}
	
	echo implode(",", $all);