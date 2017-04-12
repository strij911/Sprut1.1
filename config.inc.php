<?PHP
// Подключение к БД 
try{
$db = new PDO('pgsql:dbname=sprut1;host=localhost', 'postgres', '');
}catch(PDOException  $e ){
echo "Error: ".$e;
}

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="SPRUT 1 Auth"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Необходима авторизация';
    exit;
} else {
	$s = $db->query("select id,role from users where login='".$_SERVER['PHP_AUTH_USER']."' and password='".$_SERVER['PHP_AUTH_PW']."'");
	$s1 = $s->fetch(PDO::FETCH_ASSOC);
	$sum = $s1['id'];
	$role = $s1['role'];
	if (empty($sum)) {
	    header('WWW-Authenticate: Basic realm="SPRUT 1 Auth"');
		header('HTTP/1.0 401 Unauthorized');
		echo "Необходима авторизация!";
		exit;
	}
}
 
 $current_user = $sum;
 $current_user_role = $role; 
//  $secret = '40bb3d64fe4f9cea18b079b7a589c490';
//  $secret = '05c97a7cf12bd790360b52bfdf01154e';
  
if (!function_exists('get_userid')) {
	function get_userid(){
	GLOBAL $current_user;
	return $current_user;
	}
}

if (!function_exists('get_user_role')) {
	function get_user_role(){
	GLOBAL $current_user_role;
	return $current_user_role;
	}
}
 
if (!function_exists('log_event')) {
	function log_event($event) {
		if (filesize('log.txt') < 1024*4096) {
			file_put_contents('log.txt', date('Y-m-d H:i:s')."|".get_userid()."|".$event.PHP_EOL , FILE_APPEND);
		} else {
			file_put_contents('log.txt', date('Y-m-d H:i:s')."|".get_userid()."|".$event.PHP_EOL);
		}
	 return;
	}
}
