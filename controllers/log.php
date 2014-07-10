<?php
// output raw email log
function bft_log() {
	global $wpdb;
	$date = empty($_POST['date']) ? date('Y-m-d') : $_POST['date'];
	
	$emails = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".BFT_EMAILLOG." WHERE date=%s ORDER BY id", $date));
	
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	require(BFT_PATH."/views/raw-email-log.html.php"); 
}