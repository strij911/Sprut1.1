<?php

	include "./config.inc.php";
	
	$posts = $db->query("SELECT sprut_group.myworld_group, rabbit.shedule, mail_post_id from rabbit,rabbit_posts,sprut_group,users_groups where
						  rabbit.post_id=rabbit_posts.id
						  and sprut_group.id=rabbit_posts.group_id
						  and sprut_group.admin_uid=users_groups.uid
						  and rabbit.shedule<='".date("Y-m-d")." 23:59:59'
						  and rabbit.shedule>='".date("Y-m-d")." 00:00:00'
						  and sprut_group.id = ".$_GET['id']."
						  and (post_type = 'adv' or post_type = 'block' or post_type = 'not')
						  and rabbit.mail_post_id<>'ext_url_ratelimit'
						order by rabbit.shedule");

	while ($post = $posts->fetch(PDO::FETCH_ASSOC)) {
		$x = file_get_contents(str_replace('http://', 'https://', $post['myworld_group'])."/multipost/".strtoupper($post['mail_post_id']).".html");

		if (strstr($x, '<title>Группа заблокирована</title>')) $sql = "UPDATE rabbit SET post_type = 'block' where mail_post_id='".$post['mail_post_id']."'"; else 
			if (strstr($x, '<title>Мой Мир@Mail.Ru</title>')) $sql = "UPDATE rabbit SET post_type = 'not' where mail_post_id='".$post['mail_post_id']."'"; else	{
				echo $post['shedule'].":".$post['mail_post_id'].":OK<Br>";
				$sql = "UPDATE rabbit SET post_type = 'adv' where mail_post_id='".$post['mail_post_id']."'";
			}
		
		$m = $db->query($sql);
		
	}	