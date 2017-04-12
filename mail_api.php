<?PHP
	function sign_server_server(array $request_params, $secret_key) {
	  ksort($request_params);
	  $params = '';
	  foreach ($request_params as $key => $value) {
		$params .= "$key=$value";
	  }
	  return md5($params . $secret_key);
	}

	function api_request($txt, $photo, $uid2, $uid, $aid) {	

		include "include/application.inc.php";	
		
		$ch = curl_init();
		ob_start(); 		

		$photo = trim(file_get_contents("http://88.198.139.146/sprut1/upload_photo.php?picture=".$photo.'&aid='.$aid.'&uid='.$uid));
		
		$dprm = array(
			'method' => 'multipost.send',
			'uid2'   => $uid2,
			'text'	 => $txt,
			'photo'	 => $photo
		);		
		
		$params = array( 'app_id' => $app_id,
						 'secure' => 1,
						 'uid'    => $uid 
		);

		$params = array_merge($params, $dprm);
		//print_r($params);
		$sig = sign_server_server($params, $secret);

		$p = '';

		foreach ($params as $key => $value) {
			$p.= "$key=".urlencode($value)."&";
		}

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
		
		$x = file_put_contents('log_api.txt', date("Y-m-d H:i:s").":".$p.PHP_EOL, FILE_APPEND);
		
		$s = "http://www.appsmail.ru/platform/api?sig=".$sig;

		curl_setopt($ch, CURLOPT_URL, $s);
		
		if (!stristr($photo, 'error') && !empty($photo)) {
			curl_exec($ch);
			$input = ob_get_contents(); 
			ob_end_clean(); 
			
			$x = file_put_contents('log_api.txt', date("Y-m-d H:i:s").":"."Ответ:".$input.PHP_EOL.PHP_EOL, FILE_APPEND);		
			$a = json_decode($input, true);
		} else {
			$x = file_put_contents('log_api.txt', "Постинг отменён - ошибка загрузки фото".PHP_EOL.PHP_EOL, FILE_APPEND);		
			$a = array('sent' => '1');
		}
			
		$a['photo'] = $photo;
		return (json_encode($a));	
		curl_close ($ch);
	}
	
	function api_request_commercial($txt, $photo, $url, $uid2, $uid, $aid) {	

		include "include/application.inc.php";	

		$ch = curl_init();
		ob_start(); 		

		$txt = str_replace('[%link%]', $url, $txt);
		
		$photo = trim(file_get_contents("http://88.198.139.146/sprut1/upload_photo.php?picture=".$photo.'&aid='.$aid.'&uid='.$uid));
		
		$dprm = array(
			'method' => 'multipost.send',
			'uid2'   => $uid2,
			'text'	 => $txt,
			'photo'	 => $photo,
			'url'	 => $url
		);		
		
		$params = array( 'app_id' => $app_id,
						 'secure' => 1,
						 'uid'    => $uid,
						 'commercial' => 1,
						 'wo_comment' => 1
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

		$x = file_put_contents('log_api.txt', date("Y-m-d H:i:s").":".$p.PHP_EOL, FILE_APPEND);
		
		curl_setopt($ch, CURLOPT_URL, $s);
		if (!stristr($photo, 'error') && !empty($photo)) {
			curl_exec($ch);
			$input = ob_get_contents(); 
			ob_end_clean(); 
		
			$x = file_put_contents('log_api.txt', date("Y-m-d H:i:s").":"."Ответ:".$input.PHP_EOL.PHP_EOL, FILE_APPEND);		
		} else {
			$x = file_put_contents('log_api.txt', "Постинг отменён - ошибка загрузки фото".PHP_EOL.PHP_EOL, FILE_APPEND);		
			$a = json_encode(array('sent' => '0'));
		}
		return trim($input);	
		curl_close ($ch);
	}	

	function api_request_delete($history_id, $uid) {	

		include "include/application.inc.php";
		
		$ch = curl_init();
		ob_start(); 		

		$dprm = array(
			'method' 	=> 'multipost.delete',
			'post' => $history_id
		);		
		
		$params = array( 'app_id' => $app_id,
						 'secure' => 1,
						 'uid'    => $uid 
		);

		$params = array_merge($params, $dprm);

		$sig = sign_server_server($params, $secret);

		$p = '';

		foreach ($params as $key => $value) {
			$p.= "$key=".urlencode($value)."&";
		}

//		curl_setopt($ch, CURLOPT_POST, 1);
//		curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
		
		$s = "http://www.appsmail.ru/platform/api?".$p."sig=".$sig;
		$x = file_put_contents('log_api.txt', date("Y-m-d H:i:s").":".$s.PHP_EOL, FILE_APPEND);		

		curl_setopt($ch, CURLOPT_URL, $s);
		
		curl_exec($ch);
		$input = ob_get_contents(); 
		ob_end_clean(); 
			
		$x = file_put_contents('log_api.txt',  date("Y-m-d H:i:s").":"."Ответ:".$input.PHP_EOL.PHP_EOL, FILE_APPEND);		
			
		return ($input);	
		curl_close ($ch);
	}
		