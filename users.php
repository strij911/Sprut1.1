<?PHP
	include "./config.inc.php";
?>

<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="./index_files/bootstrap.css">
<link rel="stylesheet" href="./index_files/other.css">
<link rel="stylesheet" href="./index_files/font-awesome.css">
<link rel="stylesheet" href="./index_files/dataTables.bootstrap.css">
<script type="text/javascript" src="./index_files/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="./index_files/mask.js" charset="UTF-8"></script>
<title>SPRUT 1.1 - Пользователи</title>
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
                <span class="widget-caption">Пользователи</span>
            </div>
            <div class="widget-body">
<a href="create_user.php" class="btn btn-danger" <?PHP if (get_user_role() != 'admin') echo "disabled";?>><i class="menu-icon glyphicon glyphicon-book"></i><span class="menu-text"> Создать нового пользователя</span></a><br><br>
				<table>
<?PHP
		if (get_userid() > 1) $usr = "where id=".get_userid(); else $usr = '';
		
		$u = $db->query("SELECT * FROM users $usr ORDER BY login desc");

		while($list = $u->fetch(PDO::FETCH_ASSOC)) {

?>			
					<tr><td colspan=2><font size=+1><B>Пользователь: <?PHP echo $list['login'];?></B></td></tr>
					<tr><td valign=top>

					<br>Пароль: <?PHP echo $list['password']; ?>					
					<br><br>Роли: <?PHP echo $list['role']; ?>
					<br><br>Активен: <?PHP echo ($list['enabled'] == 1)?'да':'нет'; ?>
					
					<br><br><a style="width:150px;" class="btn btn-danger" href="edit_user.php?pid=<?PHP echo $list['id'];?>" <?PHP if ($list['login'] == 'admin') echo "disabled";?>><i class="menu-icon glyphicon glyphicon-edit " style="font-size:20px;"></i> Редактировать</a> &nbsp;&nbsp;<a style="width:150px;" class="btn btn-warning" href="del_user.php?pid=<?PHP echo $list['id'];?>" <?PHP if ($list['login'] == 'admin' || get_userid() == $list['id']) echo "disabled";?>><i class="menu-icon glyphicon glyphicon-trash " style="font-size:20px;"></i> Удалить</a></td><td>&nbsp;&nbsp;&nbsp;</td><td>

					<tr><td colspan=2><hr width=100%></td></tr>					
<?PHP } ?>					
				</table>
            </div>
        </div>
    </div>
	
</div>

</div>
</div>

</div>
</body></html>