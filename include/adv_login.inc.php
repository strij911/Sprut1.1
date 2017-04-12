<?PHP
	function mr_login($email, $passw, $i) {
	  
		$fields = array(
			'_username' => $email,
			'_password' => $passw
		);

		$ch = curl_init();
	   
		curl_setopt($ch, CURLOPT_URL, 'http://advertmania.com/login_check');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie_ad1.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie_ad1.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
	   
		$res = curl_exec($ch);

		curl_setopt($ch, CURLOPT_URL, 'http://advertmania.com/group/'.$i.'/show');
		$result = curl_exec($ch);
		$h_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		
		curl_close($ch);
	 
		return substr($result, $h_len, strlen($result));
	}
	 
	function mr_geturl($email, $passw, $url) {
	  
		$fields = array(
			'_username' => $email,
			'_password' => $passw
		);

		$ch = curl_init();
	   
		curl_setopt($ch, CURLOPT_URL, 'http://advertmania.com/login_check');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie_ad1.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie_ad1.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
	   
		$res = curl_exec($ch);

		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		$h_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		
		curl_close($ch);
	 
		return substr($result, $h_len, strlen($result));
	}
	 	 