<?php
// output raw email log
function bft_log() {
	global $wpdb;
	$date = empty($_POST['date']) ? date('Y-m-d') : $_POST['date'];
	if(!empty($_POST['cleanup'])) update_option('bft_cleanup_raw_log', $_POST['cleanup_days']);
	
	$cleanup_raw_log = get_option('bft_cleanup_raw_log');
	if(empty($cleanup_raw_log)) $cleanup_raw_log = 7;
	
	$emails = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".BFT_EMAILLOG." WHERE date=%s ORDER BY id", $date));
	
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	require(BFT_PATH."/views/raw-email-log.html.php"); 
}