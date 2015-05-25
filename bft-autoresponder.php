<?php
/*
Plugin Name: Arigato Autoresponder and Newsletter 
Plugin URI: http://calendarscripts.info/autoresponder-wordpress.html
Description: This is a sequential autoresponder that can send automated messages to your mailing list. For more advanced features check our <a href="http://calendarscripts.info/bft-pro">PRO Version</a>
Author: Kiboko Labs
Version: 2.2.7
Author URI: http://calendarscripts.info
License: GPL 2
Text domain: broadfast
*/ 

/*  Copyright 2012  Kiboko Labs

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'BFT_PATH', dirname( __FILE__ ) );
define( 'BFT_RELATIVE_PATH', dirname( plugin_basename( __FILE__ )));
include(BFT_PATH."/bft-lib.php");
include(BFT_PATH."/models/attachment.php");
include(BFT_PATH."/controllers/newsletter.php");
include(BFT_PATH."/controllers/help.php");
include(BFT_PATH."/controllers/config.php");
include(BFT_PATH."/controllers/list.php");
include(BFT_PATH."/controllers/integrations.php");
include(BFT_PATH."/controllers/integrations/contact.php");
include(BFT_PATH."/controllers/integrations/jetpack.php");
include(BFT_PATH."/controllers/log.php");
include(BFT_PATH."/controllers/messages.php");

// initialize plugin
function bft_init() {
	global $wpdb;	
	load_plugin_textdomain( 'broadfast', false, BFT_RELATIVE_PATH."/languages/" );
	if (!session_id()) @session_start();
	
	define( 'BFT_USERS', $wpdb->prefix. "bft_users" );
	define( 'BFT_MAILS', $wpdb->prefix. "bft_mails" );
	define( 'BFT_SENTMAILS', $wpdb->prefix. "bft_sentmails" );
	define( 'BFT_EMAILLOG', $wpdb->prefix. "bft_emaillog" );
	define( 'BFT_DEBUG', get_option('broadfast_debug'));
   define( 'BFT_ATTACHMENTS', $wpdb->prefix. "bft_attachments" );
   define( 'BFT_NLS', $wpdb->prefix. "bft_newsletters" );
   
   if(!defined('BFT_SENDER')) define('BFT_SENDER',get_option( 'bft_sender' ));
	
	// contact form 7 integration
	add_filter( 'wpcf7_form_elements', array('BFTContactForm7', 'shortcode_filter') );
	add_action( 'wpcf7_before_send_mail', array('BFTContactForm7', 'signup') );
	add_shortcode( 'bft-int-chk', array("BFTContactForm7", 'int_chk'));
	
	// jetpack contact form integration
	add_action('grunion_pre_message_sent', array('BFTJetPack', 'signup'));
	
	if($wpdb->get_var("SHOW TABLES LIKE '".BFT_EMAILLOG."'") == BFT_EMAILLOG) {
		$cleanup_raw_log = get_option('bft_cleanup_raw_log');
		if(empty($cleanup_raw_log)) $cleanup_raw_log = 7;
		$wpdb->query($wpdb->prepare("DELETE FROM ".BFT_EMAILLOG." WHERE date < CURDATE() - INTERVAL %d DAY", $cleanup_raw_log));				
	}
	
	$version = get_option('bft_db_version');
	if(empty($version) or $version < 2.15) bft_install(true);
	bft_hook_up();
}

/* Adds the menu items */
function bft_autoresponder_menu() {  
  add_menu_page(__('Arigato Light', 'broadfast'), __('Arigato Light', 'broadfast'), 'manage_options', 'bft_options', 'bft_options');
  add_submenu_page('bft_options',__('Settings', 'broadfast'), __('Settings', 'broadfast'), 'manage_options', 'bft_options', 'bft_options');
  add_submenu_page('bft_options',__('Your Mailing List', 'broadfast'), __('Mailing List', 'broadfast'), 'manage_options', "bft_list", "bft_list");
  add_submenu_page('bft_options',__('Import/Export Members', 'broadfast'), __('Import/Export', 'broadfast'), 'manage_options', "bft_import", "bft_import");
  add_submenu_page('bft_options',__('Manage Messages', 'broadfast'), __('Email Messages', 'broadfast'), 'manage_options', "bft_messages", "bft_messages");
  add_submenu_page('bft_options',__('Send Newsletter', 'broadfast'), __('Send Newsletter', 'broadfast'), 'manage_options', "bft_newsletter", "bft_newsletter");  
  add_submenu_page('bft_options',__('Raw Email Log', 'broadfast'), __('Raw Email Log', 'broadfast'), 'manage_options', "bft_log", "bft_log");  
  add_submenu_page('bft_options',__('Help', 'broadfast'), __('Help', 'broadfast'), 'manage_options', "bft_help", "bft_help");
  
  // not in the menu
  add_submenu_page(null, __('Configure Email Message', 'broadfast'), __('Configure Email Message', 'broadfast'), 'manage_options', 'bft_messages_config', 'bft_message_config');
  add_submenu_page(NULL,__('Integrate in Contact Form', 'broadfast'), __('Integrate in Contact Form', 'broadfast'), 'manage_options', "bft_integrate_contact", array("BFTIntegrations", "contact_form"));
}

/* Creates the mysql tables needed to store mailing list and messages */
$bft_msg="";

function bft_install($update = false) {
	 global $wpdb;
	 
	 if(!$update) bft_init();
    $bft_db_version="2.15";
	 
	  if($wpdb->get_var("SHOW TABLES LIKE '".BFT_USERS."'") != BFT_USERS) {        
			$sql = "CREATE TABLE " . BFT_USERS . " (
				  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  email VARCHAR(100) NOT NULL UNIQUE,	
				  name VARCHAR(255)	NOT NULL,		  
				  status TINYINT UNSIGNED NOT NULL,
				  date DATE NOT NULL,
                  ip VARCHAR(100) NOT NULL,
				  code VARCHAR(10) NOT NULL
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
	  
	  if($wpdb->get_var("SHOW TABLES LIKE '".BFT_MAILS."'") != BFT_MAILS) {
	  
			$sql = "CREATE TABLE `" . BFT_MAILS . "` (
				  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `subject` VARCHAR(255) NOT NULL,				  
				  `message` TEXT NOT NULL,				  
				  `days` INT UNSIGNED NOT NULL,
                  `send_on_date` TINYINT UNSIGNED NOT NULL,
                   `date` DATE NOT NULL
				) DEFAULT CHARSET=utf8;";			
			
			$wpdb->query($sql);
	  }
	  
	  if($wpdb->get_var("SHOW TABLES LIKE '".BFT_SENTMAILS."'") != BFT_SENTMAILS) {
	  
			$sql = "CREATE TABLE `" . BFT_SENTMAILS . "` (
				  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `mail_id` INT UNSIGNED NOT NULL,				  
				  `user_id` INT UNSIGNED NOT NULL,				  
				  `date` DATE NOT NULL
				) DEFAULT CHARSET=utf8;";
			$wpdb->query($sql);
	  }
	 
	  // this is email log of all the messages sent in the system 
	  if($wpdb->get_var("SHOW TABLES LIKE '".BFT_EMAILLOG."'") != BFT_EMAILLOG) {
	  
			$sql = "CREATE TABLE `" . BFT_EMAILLOG . "` (
				  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `sender` VARCHAR(255) NOT NULL DEFAULT '',
				  `receiver` VARCHAR(255) NOT NULL DEFAULT '',
				  `subject` VARCHAR(255) NOT NULL DEFAULT '',
				  `date` DATE,
				  `datetime` TIMESTAMP,
				  `status` VARCHAR(100) NOT NULL DEFAULT 'OK'				  
				) DEFAULT CHARSET=utf8;";
			$wpdb->query($sql);
	  }

       // attachments table      
        if($wpdb->get_var("SHOW TABLES LIKE '".BFT_ATTACHMENTS."'") != BFT_ATTACHMENTS) {             
            $sql = "CREATE TABLE IF NOT EXISTS `".BFT_ATTACHMENTS."` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `mail_id` int(10) unsigned NOT NULL DEFAULT 0,
              `nl_id` int(10) unsigned NOT NULL DEFAULT 0,
              `file_name` VARCHAR(255) NOT NULL DEFAULT '',
              `file_path` VARCHAR(255) NOT NULL DEFAULT '',
              `url` VARCHAR(255) NOT NULL DEFAULT '',
              PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8;";
            $wpdb->query($sql);
      } 
      
      // instant newsletters
	  if($wpdb->get_var("SHOW TABLES LIKE '".BFT_NLS."'") != BFT_NLS) {
	  
			$sql = "CREATE TABLE `" . BFT_NLS . "` (
				  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `subject` VARCHAR(255) NOT NULL,				  
				  `message` TEXT NOT NULL,				  
              `date` DATE NOT NULL,
              `num_sent` INT UNSIGNED NOT NULL DEFAULT 0,
              `email_type` VARCHAR(100) NOT NULL DEFAULT 'text/html'
				) DEFAULT CHARSET=utf8;";			
			
			$wpdb->query($sql);
	  }
	  
	  // add DB fields	  
	  bft_add_db_fields(array(
		  array("name"=>"content_type", "type"=>"VARCHAR(100) NOT NULL DEFAULT 'text/html'"),  		  
	  ), BFT_MAILS);
	  
	   bft_add_db_fields(array(
		  array("name"=>"nl_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"), /* newsletter ID */ 		  
	  ), BFT_SENTMAILS);
	  
	  $old_bft_db_version=get_option('bft_db_version');
	  
	  // DB version 1.2, plugin version 1.5
      if(!empty($old_bft_db_version) and $old_bft_db_version<1.2) {
         $sql="ALTER TABLE ".BFT_MAILS." ADD `send_on_date` TINYINT UNSIGNED NOT NULL,
            ADD `date` DATE NOT NULL";
         $wpdb->query($sql);
     }  
	  
	  update_option( 'bft_db_version', $bft_db_version);	 
}

/* Stores the autoresponder configuration */
function bft_options() {

  if(!empty($_POST['settings_ok'])) {
  	 // save autoresponder settings
		 update_option( 'bft_sender', $_POST['bft_sender'] );
		 update_option( 'bft_redirect', $_POST['bft_redirect'] );
		 update_option( 'bft_optin', $_POST['bft_optin'] );		 
		 update_option( 'bft_subscribe_notify', @$_POST['subscribe_notify'] );
		 update_option( 'bft_unsubscribe_notify', @$_POST['unsubscribe_notify'] );
		 update_option( 'bft_auto_subscribe', @$_POST['auto_subscribe'] );
		 update_option( 'bft_use_cron_job', @$_POST['use_cron_job'] );
		 update_option( 'bft_mails_per_run', $_POST['mails_per_run'] );
  }

  $bft_sender = stripslashes( get_option( 'bft_sender' ) );	
  $bft_redirect = stripslashes( get_option( 'bft_redirect' ) );	
  $bft_optin = stripslashes( get_option( 'bft_optin' ) );	
  
  // double opt-in message
  if(!empty($_POST['double_optin_ok'])) {
  	 update_option('bft_optin_subject', $_POST['optin_subject']);
  	 update_option('bft_optin_message', $_POST['optin_message']);
  	 update_option( 'bft_optin_redirect', $_POST['bft_optin_redirect'] );
  }
  
  $subscribe_notify = get_option('bft_subscribe_notify');
  $unsubscribe_notify = get_option('bft_unsubscribe_notify');
  $use_cron_job = get_option('bft_use_cron_job');
  $bft_optin_redirect = stripslashes( get_option( 'bft_optin_redirect' ) ); 	  
  
  require(BFT_PATH."/views/bft_main.html.php");
}

/* import/export */
function bft_import() {
	global $wpdb;

	if(!empty($_POST['import'])) {
		if(empty($_FILES["file"]["name"])) {
			die("Please upload file");
		}
		
		if(empty($_POST["delim"])) {
			die("There must be a delimiter");
		}
		
		$rows=file($_FILES["file"]["tmp_name"]);
	
		foreach($rows as $row) {
			//explode values
			$values=explode($_POST["delim"],$row);
			$position=$_POST['email_column']-1;
			$name_position=$_POST['name_column']-1;
			if($position<0 or !is_numeric($position)) $position=0;			
			if($name_position<0 or !is_numeric($name_position)) $name_position=1;			
			$email=trim($values[$position]);
			$name=trim($values[$name_position]);
			
			$sql="INSERT IGNORE INTO ".BFT_USERS." (date, name, email, status) 
			VALUES (CURDATE(), '$name','$email',1)";
			$wpdb->query($sql);
			
			bft_welcome_mail($wpdb->insert_id);
		}
		
		$success_text="<p style='color:red;'><b>".sizeof($rows)." members have been imported.</b></p>";
	}
	
	if(!empty($_POST['export'])) {
		$active_sql = '';
		if(!empty($_POST['active'])) {
			$active_sql=" AND status='1' ";
		}
	
		$sql="SELECT * FROM ".BFT_USERS." WHERE 1 $active_sql
		ORDER BY email";		
		$members=$wpdb->get_results($sql);
		
		$newline = broadfast_define_newline();
		
		$content=__('Email', 'broadfast').','.__('Name', 'broadfast').','.
			__('IP Address', 'broadfast').','.__('Date signed', 'broadfast').$newline;
			
		foreach($members as $member) {
			$content.="{$member->email},{$member->name},{$member->ip},{$member->date}".$newline;
		}
		
		// credit to http://yoast.com/wordpress/users-to-csv/	
		$now = gmdate('D, d M Y H:i:s') . ' GMT';
		
		$filename = 'subscribers.csv';
	
		header('Content-Type: ' . broadfast_get_mime_type());
		header('Expires: ' . $now);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Pragma: no-cache');
		echo $content;
		exit;
	}

	require(BFT_PATH."/views/bft_import.html.php");
}

/* sends the first welcome mail to newly registered or imported user
if such has been scheduled. Scheduling of those mails is done by setting "0" for "days" */
function bft_welcome_mail($uid) {
	global $wpdb;
	
	// select email
	$sql="SELECT * FROM ".BFT_MAILS." WHERE days=0";	
	$mail=$wpdb->get_row($sql);
		
	if(empty($mail->id)) return false;
	
	// select member
	$sql="SELECT * FROM ".BFT_USERS." WHERE id='$uid' AND status=1";
	$member=$wpdb->get_row($sql);
	if(empty($member->id)) return false;
	
	$attachments = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".BFT_ATTACHMENTS."
					WHERE mail_id = %d ORDER BY id", $mail->id));	
					
	// insert in sent
	$wpdb->query($wpdb->prepare("INSERT INTO ".BFT_SENTMAILS." SET
						mail_id=%d, user_id=%d, date=%s", $mail->id, $uid, date("Y-m-d", current_time('timestamp'))));									
	
	bft_customize($mail,$member, $attachments);
}

/* private function called to customize an email message and send it */
function bft_customize($mail,$member, $attachments = null) {
	// send mail to member
	$subject=$mail->subject;				
	$message=$mail->message;
	
	$subject=str_replace("{{name}}",$member->name,$subject);
	$subject=str_replace("{{email}}",$member->email,$subject);
	
	$message=str_replace("{{name}}",$member->name,$message);
	$message=str_replace("{{email}}",$member->email,$message);
	
	$content_type = empty($mail->content_type) ? 'text/html' : $mail->content_type;
				
	// add unsubscribe link
	$unsub_url = get_option('siteurl')."/?bft=bft_unsubscribe&email=".$member->email;
	if($content_type == 'text/html') {
		$message.= "<br><br>".__('To unsubscribe from our list visit the url below:', 'broadfast').
		"<br><a href='$unsub_url'>$unsub_url</a>";
		$message=str_replace("\t","",$message);
	}
	else {
		$message.= "\n\n".__('To unsubscribe from our list visit the url below:', 'broadfast')."\n".$unsub_url;		
	}	
	
	$message = do_shortcode($message);
	
	$sender = empty($mail->sender) ? BFT_SENDER : $mail->sender;
	
	return bft_mail($sender,$member->email,$subject,$message, $content_type, $attachments);
}

// handle all this stuff on template_redirect call so
// plugins and especially the possible WP MAIL SMTP are loaded!!
function bft_template_redirect() {
	global $wpdb;	
	
	//  subscribe user
	if(!empty($_REQUEST['bft']) and $_REQUEST['bft']=='register') {
		$email=esc_sql($_POST['email']);
		$name=esc_sql($_POST['user_name']);
		
		bft_subscribe($email, $name);
			
		add_filter('the_content', 'bft_screenmsg');
	}
	
	
	// unsubscribe user
	if(!empty($_REQUEST['bft']) and $_REQUEST['bft']=='bft_unsubscribe') {		
		//  notify admin?
		if(get_option('bft_unsubscribe_notify')) {
			// select this user
			$user = $wpdb->get_row( $wpdb->prepare(" SELECT * FROM ".BFT_USERS." WHERE email=%s", $_GET['email']));			
			
			bft_unsubscribe_notify($user);
		}
		
		$sql="DELETE FROM ".BFT_USERS." WHERE email=%s";
		$wpdb->query($wpdb->prepare($sql, $_GET['email']));
		
		echo "<script language='javascript'>
			alert('".__('You have been unsubscribed.', 'broadfast')."');
			window.location='".($bft_redirect?$bft_redirect:site_url())."';
			</script>";
			exit;
	}
	
	// confirm user registration
	if(!empty($_REQUEST['bft']) and $_REQUEST['bft']=='bft_confirm') {
		// select user
		$sql=$wpdb->prepare("SELECT * FROM ".BFT_USERS." WHERE id=%d AND code=%s", $_GET['id'], $_GET['code']);	
		$member=$wpdb->get_row($sql);	
		
		$bft_redirect = stripslashes( get_option( 'bft_optin_redirect' ) );
		if(empty($bft_redirect)) $bft_redirect = get_option('bft_redirect');	
		
		if(!empty($member->id)) {			
			$sql="UPDATE ".BFT_USERS." SET 
			code='".substr(md5($_GET['code'].time()), 0,8)."',
			status=1
			WHERE id='{$member->id}'";		
			$wpdb->query($sql);
			
			bft_welcome_mail($member->id);
			
			// notify admin?			
			if(get_option('bft_subscribe_notify')) {
				bft_subscribe_notify($member->id);
			}
		}
		
		// display success message
		echo "<script language='javascript'>
		alert('".__('Your email address has been confirmed!', 'broadfast')."');
		window.location='".($bft_redirect?$bft_redirect:site_url())."';
		</script>";
		exit;
	}
}

// the actual autoresponder hook - it's run when the index page is loaded
function bft_hook_up() {
  $use_cron_job = get_option('bft_use_cron_job');

	// If user chose to run cron job, execute this only when the GET param is present  
  if($use_cron_job == 1 and empty($_GET['bft_cron'])) return true;
  	 
  if(!defined('BFT_SENDER')) define('BFT_SENDER',get_option( 'bft_sender' ));
  require(BFT_PATH."/controllers/bft_hook.php");    

  // for real cron job exit here  
  if($use_cron_job == 1 and !empty($_GET['bft_cron'])) die(__('Running in cron job mode', 'broadfast'));
}

// handle shortcode
function bft_shortcode_signup($attr) {			
	ob_start();
	require_once(BFT_PATH."/views/signup-form.html.php");
	$contents = ob_get_contents();
	ob_end_clean();
	
	return $contents;
}

// function to conditionally add DB fields
function bft_add_db_fields($fields, $table) {
		global $wpdb;
		
		// check fields
		$table_fields = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
		$table_field_names = array();
		foreach($table_fields as $f) $table_field_names[] = $f->Field;		
		$fields_to_add=array();
		
		foreach($fields as $field) {
			 if(!in_array($field['name'], $table_field_names)) {
			 	  $fields_to_add[] = $field;
			 } 
		}
		
		// now if there are fields to add, run the query
		if(!empty($fields_to_add)) {
			 $sql = "ALTER TABLE `$table` ";
			 
			 foreach($fields_to_add as $cnt => $field) {
			 	 if($cnt > 0) $sql .= ", ";
			 	 $sql .= "ADD $field[name] $field[type]";
			 } 
			 
			 $wpdb->query($sql);
		}
}

register_activation_hook(__FILE__,'bft_install');
add_action('init', 'bft_init');
add_action('admin_menu', 'bft_autoresponder_menu');
add_action('template_redirect', 'bft_template_redirect');
add_shortcode( 'BFTWP', "bft_shortcode_signup" );
add_action('wp_login', 'bft_auto_subscribe', 10, 2);