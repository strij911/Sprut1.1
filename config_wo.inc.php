<?PHP
// Подключение к БД 
try{
$db = new PDO('pgsql:dbname=sprut1;host=localhost', 'postgres', '');
}catch(PDOException  $e ){
echo "Error: ".$e;
}

if (!function_exists('log_event')) {
	function log_event($event) {
		if (filesize('log.txt') < 1024*4096) {
			file_put_contents('log.txt', date('Y-m-d H:i:s')."|system|".$event.PHP_EOL , FILE_APPEND);
		} else {
			file_put_contents('log.txt', date('Y-m-d H:i:s')."|system|".$event.PHP_EOL);
		}
	 return;
	}
}
