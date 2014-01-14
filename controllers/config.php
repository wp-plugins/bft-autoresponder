<?php
// configure email message on subscribe/unsubscribe
function bft_message_config() {
	$type = ($_GET['message'] == 'subscribe_notify') ? $_GET['message'] : 'unsubscribe_notify';	
	// user friendly text
	$friendly_type = ($type == 'subscribe_notify') ? __('Subscribe Notification', 'broadfast') : __('Unsubscribe Notification', 'broadfast');
	
	if(!empty($_POST['ok'])) {
		update_option('bft_'.$type.'_subject', $_POST['subject']);
		update_option('bft_'.$type.'_message', $_POST['message']);
	}	
	
	$subject = get_option('bft_'.$type.'_subject');
	$message = get_option('bft_'.$type.'_message');
	
	include(BFT_PATH."/views/message-config.html.php");
}