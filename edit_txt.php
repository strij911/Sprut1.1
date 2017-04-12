<?PHP

	include "./config.inc.php";

	$over = "";
	
    if (!empty($_POST['link'])) {
		
		$m = $db->query("UPDATE texts SET text='".$_POST['link']."' WHERE id=".$_POST['pid']);
		log_event("Текст ".substr($_POST['old_link'],0,120)." изменен на ".substr($_POST['link'],0,120));

		$m = $db->query("SELECT product_id FROM texts WHERE id=".$_POST['pid']);	
		$prod = $m->fetch(PDO::FETCH_ASSOC);		
		header('Location: products.php?pid='.$prod['product_id'].'#active');	

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
<title>SPRUT 1.1 - Редактирование текста к продукту</title>
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
<?PHP
		$m = $db->query("SELECT * FROM texts where id=".$_GET['id']);
		$list = $m->fetch(PDO::FETCH_ASSOC);
?>					 
<br><br><INPUT TYPE="hidden" name="pid" value=<?PHP echo $_GET['id']; ?>>
<input class="form-control" placeholder="http://" TYPE="hidden" id="old_link" NAME="old_link" value="<?PHP echo htmlspecialchars(substr($list['text'],0,120)); ?>" autocomplete="off">
<textarea class="form-control" placeholder="текст" id="link" NAME="link" value="" autocomplete="off" cols=40 rows=10><?PHP echo $list['text']; ?></textarea>
<span class="input-group-addon">
<button type="submit" class="btn btn-default shiny" name="submit">Изменить</button>
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