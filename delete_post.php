<?PHP
	function sign_server_server(array $request_params, $secret_key) {
	  ksort($request_params);
	  $params = '';
	  foreach ($request_params as $key => $value) {
		$params .= "$key=$value";
	  }
	  return md5($params . $secret_key);
	}

	function api_request($mail_post_id, $uid) {	
	
		$mx = mt_rand(1,4);
		if ($mx == 1) {
			$secret = 'c74d56d1c231930325f52ed32d4361b5';
			$app_id = 750524;
		}
		if ($mx == 2) {
			$secret = 'd33b5b49a3695130f626316561f65be6';
			$app_id = 750525;
		}
		if ($mx == 3) {
			$secret = '2ab41defaba6df3465af2448fb4a2612';
			$app_id = 750526;
		} 
		if ($mx == 4) {
			$secret = '552bae43287ce62f168ce8158e778b1a';
			$app_id = 750527;
		}

		$ch = curl_init();
		ob_start(); 		
	
		$params = array(
			'method'  => 'history.delete',
			'history_id' => $mail_post_id
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

		if (filesize('log_api.txt') < 1024*8096) {
			$x = file_put_contents('log_api.txt', date("Y-m-d H:i:s").":".$p.PHP_EOL, FILE_APPEND);
		} else {
			$x = file_put_contents('log_api.txt', date("Y-m-d H:i:s").":".$p.PHP_EOL);
		}
		
		curl_setopt($ch, CURLOPT_URL, $s);

		curl_exec($ch);
		$input = ob_get_contents(); 
		ob_end_clean();
		$x = file_put_contents('log_api.txt', "Ответ:".$input.PHP_EOL.PHP_EOL, FILE_APPEND);
		return trim($input);	
		curl_close ($ch);
	}
	
	$mail_post_id = '28080000f1c6d003';
	$uid = '17123411943328297813';
	
	$s = api_request($mail_post_id, $uid);
	
	echo $s;