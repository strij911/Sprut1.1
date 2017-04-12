<?PHP
	$x = file_get_contents('log_api.txt');
	echo json_encode(array('APP' => substr_count($x, 'Application is not installed for this user'),
						   'RES' => substr_count($x, 'Please resubmit the request'),
						   'IMG' => substr_count($x, 'Image is incorrect or inaccessible'),
						   'LIM' => substr_count($x, 'ext_url_ratelimit'),
						   'PRM' => substr_count($x, 'Permission error:')
	));