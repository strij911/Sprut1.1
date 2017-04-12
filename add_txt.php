<?PHP

	include "./config.inc.php";

	$over = "";
	
    if (!empty($_POST['txt'])) {
		
		$m = $db->query("INSERT INTO texts (product_id, text) VALUES ('".$_POST['pid']."', '".$_POST['txt']."')");
		$sid = $db->lastInsertId('texts_id_seq');
		log_event("Текст ".str_replace("\r\n", ' ', substr($_POST['txt'],0,120))." ($sid) успешно добавлен!");
		header('Location: products.php?pid='.$_POST['pid'].'#active');	
	} //if	
	
	header('Content-Type: text/html; charset=utf-8');
?>

<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="./index_files/bootstrap.css">
<link rel="stylesheet" href="./index_files/other.css">
<link rel="stylesheet" href="./index_files/font-awesome.css">
<link rel="stylesheet" href="./index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="./index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/mask.js" charset="UTF-8"></script>
<title>SPRUT 1 - Добавление текста к продукту</title>
</head>
<script language="javascript">
function strstr(haystack, needle, bool) {
    var pos = 0;

    haystack += "";
    pos = haystack.indexOf(needle); if (pos == -1) {
        return false;
    } else {
        if (bool) {
            return haystack.substr(0, pos);
        } else {
            return haystack.slice(pos);
        }
    }
}
function check_txt() {
	var s = document.getElementById('txt_area'); 
	if (strstr(s.value, 'http')) {
		alert('Для вставки ссылок используйте шаблон [%link%]');
		return(false);
	} else return(true);
}
</script>
<body>

<div class="main-container container-fluid">

<?PHP include "sidebar.inc";?>

<div class="page-content">
<div class="page-body">

<div class="row">
    <div class="col-lg-9 col-xs-12 col-md-12">
        <div class="widget radius-bordered">
            <div class="widget-header bg-danger">

			<form name="alias" method="post">
                        &nbsp;               <div class="row">
                  <div class="col-sm-6">
                     <div class="form-group">
<br><br><INPUT TYPE="hidden" name="pid" value=<?PHP echo $_GET['pid']; ?>>
<textarea class="form-control" placeholder="текст" id="txt_area" NAME="txt" value="" autocomplete="off" cols=40 rows=10></textarea>
<span class="input-group-addon">Введите текст
<button type="submit" onclick="return(check_txt());" class="btn btn-default shiny" name="submit">Добавить</button>
                     </div>
                  </div>
               
            </form>
			
			
            </div>
        </div>
    </div>
	
</div>

</div>
</div>

</div></div>
</body></html>