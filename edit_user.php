<?PHP

	include "./config.inc.php";

    if (!empty($_POST['login'])) {
		$roles = '';

		if (isset($_POST['role1'])) $roles.='poster,';
		if (isset($_POST['role2'])) $roles.='advert,';		
		if (isset($_POST['active'])) $enabled='true'; else $enabled='false';
		
		$m = $db->query("UPDATE users SET login='".$_POST['login']."', password='".$_POST['password']."', role='".$roles."', enabled=".$enabled." WHERE id=".$_POST['pid']);
		log_event("Пользователь ".$_POST['login']." изменен! Список: ".$_POST['link'].":".$_POST['role']);
		header('Location: users.php');	
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
<title>SPRUT 1.1 - Редактирование пользователя</title>
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
		$m = $db->query("SELECT * FROM users where id=".$_GET['pid']);
		$list = $m->fetch(PDO::FETCH_ASSOC);
?>					 
<br><br><INPUT TYPE="hidden" name="pid" value=<?PHP echo $_GET['pid']; ?>>

<span class="input-group-addon">Логин</span>
<input class="form-control" placeholder="Логин" TYPE="text"  id="login" NAME="login" value="<?PHP echo $list['login'];?>" autocomplete="off" style="height:40px;">

<span class="input-group-addon">Пароль</span>
<input class="form-control" placeholder="Пароль" TYPE="text"  id="password" NAME="password" value="<?PHP echo $list['password'];?>" autocomplete="off" style="height:40px;">

<span class="input-group-addon">Роли</span>
<label><input name="role1" type="checkbox" <?PHP if (strstr($list['role'], 'poster')) echo "checked";?>><span class="text"> <font color=white>Редактор</font></span></label>
<label><input name="role2" type="checkbox" <?PHP if (strstr($list['role'], 'advert')) echo "checked";?>><span class="text"> <font color=white>Арбитражник</font></span></label>

<span class="input-group-addon">Пользователь активен?</span>
<label><input name="active" type="checkbox" <?PHP if ($list['enabled'] == 1) echo "checked";?>><span class="text"> <font color=white>Активен</font></span></label>

<br><br><button type="submit" class="btn btn-default shiny" name="submit">Изменить</button>
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