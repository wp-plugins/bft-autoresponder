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
		
		foreach($users as $user) {
			if(bft_mail($sender, $user->email, $subject, $message)) $num_mails_sent++;
		}
	}
	
	require(BFT_PATH."/views/newsletter.html.php");
}