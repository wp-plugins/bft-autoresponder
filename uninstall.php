<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
	    exit();
	 
delete_option('bft_sender');
delete_option('bft_db_version');
delete_option('bft_redirect');
delete_option('bft_optin');
?>