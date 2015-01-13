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
		
		// save this newsletter
		if(empty($_GET['id'])) {
			// add new
			$wpdb->query($wpdb->prepare("INSERT INTO ".BFT_NLS." SET
				subject=%s, message=%s, date=CURDATE(), num_sent=%d, email_type=%s", 
				$subject, $message, $num_mails_sent, $content_type));
		}
		else {
			// edit newsletter
			$wpdb->query($wpdb->prepare("UPDATE ".BFT_NLS." SET
				subject=%s, message=%s, date=CURDATE(), num_sent=%d, email_type=%s
				WHERE id=%d", $subject, $message, $num_mails_sent, $content_type, $_GET['id']));
		}
		
		$_SESSION['bft_flash'] = sprintf(__('%d emails were sent.', 'broadfast'), $num_mails_sent);
		bft_redirect("admin.php?page=bft_newsletter");
	}
	
	// delete newsletter?
	if(!empty($_GET['del'])) {
		$wpdb->query($wpdb->prepare("DELETE FROM ".BFT_NLS." WHERE id=%d", $_GET['del']));
		bft_redirect("admin.php?page=bft_newsletter");
	}
	
	// select existing newsletters
	$newsletters = $wpdb->get_results("SELECT id, subject, date, num_sent FROM ".BFT_NLS." ORDER BY date DESC, id DESC");
	$dateformat = get_option('date_format');
	
	if(!empty($_GET['id'])) {
		$nl = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".BFT_NLS." WHERE id=%d", $_GET['id']));
	}
	
	require(BFT_PATH."/views/newsletter.html.php");
}