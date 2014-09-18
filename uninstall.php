<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
	    exit();
global $wpdb;		
	 
delete_option('bft_sender');
delete_option('bft_db_version');
delete_option('bft_redirect');
delete_option('bft_optin');

$users_table = $wpdb->prefix. "bft_users";
$mails_table = $wpdb->prefix . "bft_mails";
$sentmails_table = $wpdb->prefix . "bft_sentmails";
$log_table = $wpdb->prefix . "bft_emaillog";

$sql="DROP TABLE $users_table";
$wpdb->query($sql);

$sql="DROP TABLE $mails_table";
$wpdb->query($sql);

$sql="DROP TABLE $sentmails_table";
$wpdb->query($sql);

$sql="DROP TABLE $log_table";
$wpdb->query($sql);