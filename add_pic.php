<?PHP
	include "./config.inc.php";

	$over = "";
	
    if (!empty($_FILES['userfile']['tmp_name'])) {
		$uploaddir = './pics/';
		
		$fn = mt_rand(88888,8888888);
		$uploadfile = $uploaddir . basename(str_replace(' ', '', $fn.$_FILES['userfile']['name']));
		
		$m = $db->query("INSERT INTO pictures (product_id, picture) VALUES ('".$_POST['pid']."', '"."/pics/".str_replace(' ', '', $fn.$_FILES['userfile']['name'])."')");
		$sid = $db->lastInsertId('pictures_id_seq');
		move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
		
		log_event("Картинка ".str_replace(' ', '', $fn.$_FILES['userfile']['name'])." ($sid) успешно добавлена!");
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
<title>SPRUT 1 - Добавление картинки к продукту</title>
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

			<form name="alias" method="post" ENCTYPE="multipart/form-data">
                        &nbsp;               <div class="row">
                  <div class="col-sm-6">
                     <div class="form-group">
<br><br><INPUT TYPE="hidden" name="MAX_FILE_SIZE" value=5000000>
<INPUT TYPE="hidden" name="pid" value=<?PHP echo $_GET['pid']; ?>>
<input class="form-control" placeholder="Файл" TYPE="file"  id="userfile" NAME="userfile" value="" autocomplete="off" style="height:40px;" accept="image/x-png,image/gif,image/jpeg"><span class="input-group-addon">Укажите файл в формате JPG
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