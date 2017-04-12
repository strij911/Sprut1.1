<div class="page-sidebar" id="sidebar" style="position:fixed;">

<ul class="nav sidebar-menu">
                    <li class="">
                        <a href="/sprut1/index.php">
                            <i class="menu-icon glyphicon glyphicon-home "></i>
                            <span class="menu-text"> Главная</span> 
                        </a>
                    </li>

<?PHP 
	if (strstr(get_user_role(), 'poster') || get_user_role() == 'admin') {
?>
                    <li class="">
                        <a href="/sprut1/poster.php">
                            <i class="menu-icon glyphicon glyphicon-eye-open "></i>
                            <span class="menu-text"> Публикация постов</span> 
                        </a>
                    </li>
<?PHP } ?>	
<?PHP 
	if (strstr(get_user_role(), 'advert') || get_user_role() == 'admin') {
?>
                    <li class="">
                        <a href="/sprut1/promo.php">
                            <i class="menu-icon glyphicon glyphicon-eye-open "></i>
                            <span class="menu-text"> Публикация рекламы</span> 
                        </a>
                    </li>
                    <li class="">
                        <a href="/sprut1/products.php">
                            <i class="menu-icon glyphicon glyphicon-gift "></i>
                            <span class="menu-text"> Продукты</span>
                        </a>
                    </li>
<?PHP } ?>
<?PHP 
	if (get_user_role() == 'admin') {
?>
                    <li class="">
                        <a href="/sprut1/o2.php">
                            <i class="menu-icon glyphicon glyphicon-eye-open "></i>
                            <span class="menu-text"> Отчет по группам</span> 
                        </a>
                    </li>
<?PHP } ?>
<?PHP 
	if (get_user_role() == 'admin') {
?>
                    <li class="">
                        <a href="/sprut1/errors.php">
                            <i class="menu-icon glyphicon glyphicon-eye-open "></i>
                            <span class="menu-text"> Уведомления</span> 
                        </a>
                    </li>
<?PHP } ?>	
<?PHP 
	if (strstr(get_user_role(), 'video') || get_user_role() == 'admin') {
?>
                    <li class="">
                        <a href="/sprut1/video.php">
                            <i class="menu-icon glyphicon glyphicon-film "></i>
                            <span class="menu-text"> Публикация видео</span> 
                        </a>
                    </li>
                    <li class="">
                        <a href="/sprut1/products_vid.php">
                            <i class="menu-icon glyphicon glyphicon-film "></i>
                            <span class="menu-text"> Видео продукты</span>
                        </a>
                    </li>
<?PHP } ?>					

<?PHP 
	if (get_user_role() == 'admin') {
?>
                    <li class="">
                        <a href="/sprut1/xls_read.php">
                            <i class="menu-icon glyphicon glyphicon-warning-sign "></i>
                            <span class="menu-text"> Настройки постинга</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="/sprut1/users.php">
                            <i class="menu-icon glyphicon glyphicon-user "></i>
                            <span class="menu-text"> Пользователи</span>
                        </a>
                    </li>		
<?PHP } ?>					
                    <li>
                        <a href="http://logout@77.232.23.74/index.php">
                            <i class="menu-icon glyphicon glyphicon-log-out "></i>
                            <span class="menu-text"> Выход </span>
                        </a>
                    </li>
                    <li style="height:235px;margin-top:10px;">
						<a style="border:0px;" href="#"><img src="./i/79861.jpg" width=200></a>
					</li>
					
                </ul>
</div>