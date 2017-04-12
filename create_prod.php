<?PHP

	include "./config.inc.php";

	$over = "";
	
    if (!empty($_POST['title'])) {
		
		$m = $db->query("INSERT INTO products (user_id, title, datestamp) VALUES ('".get_userid()."', '".$_POST['title']."', '".date("Y-m-d H:i:s")."')");
		$sid = $db->lastInsertId('products_id_seq');
		log_event("Продукт ".$_POST['title']." ($sid) успешно добавлен!");
		header('Location: products.php?pid='.$sid);	
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
<title>SPRUT 1 - Добавление продукта</title>
</head>
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
<input class="form-control" placeholder="Название" TYPE="text"  id="title" NAME="title" value="" autocomplete="off" style="height:40px;"><span class="input-group-addon">Укажите название продукта
<button type="submit" class="btn btn-default shiny" name="submit">Добавить</button>
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