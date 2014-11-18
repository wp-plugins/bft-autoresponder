<?php
// sends immediate newsletter
function bft_newsletter() {
	global $wpdb;
	
	if(!empty($_POST['ok']) and !empty($_POST['subject']) and !empty($_POST['message'])) {
		// select all active users
		$users = $wpdb->get_results("SELECT * FROM ".BFT_USERS." WHERE status=1 ORDER BY id");
		$num_mails_sent = 0;
		
		$sender = get_option('bft_sender');
		$subject = stripslashes($_POST['subject']);
		$message = stripslashes($_POST['message']);
		
		$content_type = empty($_POST['content_type']) ? 'text/html' : $_POST['content_type'];
		
		$mail = (object) array("subject"=>$subject, "message"=>$message, "sender" => $sender, "content_type"=>$content_type);
		foreach($users as $user) {
			if(bft_customize($mail,$user)) $num_mails_sent++;
		}
	}
	
	require(BFT_PATH."/views/newsletter.html.php");
}