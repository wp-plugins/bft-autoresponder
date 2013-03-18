<?php
/*
Plugin Name: BFT Light
Plugin URI: http://calendarscripts.info/autoresponder-wordpress.html
Description: This is a sequential autoresponder that can send automated messages to your mailing list. For more advanced features check our <a href="http://calendarscripts.info/bft-pro">PRO Version</a>
Author: Kiboko Labs
Version: 1.9
Author URI: http://calendarscripts.info
License: GPL 2
*/ 

/*  Copyright 2012  Bobby Handzhiev (email : admin@pimteam.net)

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
require_once(ABSPATH . 'wp-includes/pluggable.php');
include(BFT_PATH."/bft-lib.php");
$wpdb->show_errors=true;

// initialize plugin
function bft_init() {
	global $wpdb;
	load_plugin_textdomain( 'broadfast', false, BFT_RELATIVE_PATH."/languages/" );
	
	define( 'BFT_USERS', $wpdb->prefix. "bft_users" );
	define( 'BFT_MAILS', $wpdb->prefix. "bft_mails" );
	define( 'BFT_SENTMAILS', $wpdb->prefix. "bft_sentmails" );
}

/* Adds the menu items */
function bft_autoresponder_menu() {  
  add_menu_page(__('BFT Autoresponder', 'broadfast'), __('BFT Autoresponder', 'broadfast'), 'manage_options', 'bft_options', 'bft_options');
  add_submenu_page('bft_options',__('Your Mailing List', 'broadfast'), __('Mailing List', 'broadfast'), 'manage_options', "bft_list", "bft_list");
  add_submenu_page('bft_options',__('Import/Export Members', 'broadfast'), __('Import/Export', 'broadfast'), 'manage_options', "bft_import", "bft_import");
  add_submenu_page('bft_options',__('Manage Messages', 'broadfast'), __('Email Messages', 'broadfast'), 'manage_options', "bft_messages", "bft_messages");
  
}

/* Creates the mysql tables needed to store mailing list and messages */
$users_table= $wpdb->prefix. "bft_users";
$mails_table= $wpdb->prefix . "bft_mails";
$sentmails_table= $wpdb->prefix . "bft_sentmails";
$bft_msg="";

function bft_install() {
	 global $wpdb;
	 
	 $users_table= $wpdb->prefix."bft_users";
	 $mails_table= $wpdb->prefix . "bft_mails";
	 $sentmails_table= $wpdb->prefix . "bft_sentmails";
     $bft_db_version="1.2";
	 
	  if($wpdb->get_var("SHOW TABLES LIKE '$users_table'") != $users_table) {        
			$sql = "CREATE TABLE " . $users_table . " (
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
	  
	  if($wpdb->get_var("SHOW TABLES LIKE '$mails_table'") != $mails_table) {
	  
			$sql = "CREATE TABLE `" . $mails_table . "` (
				  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `subject` VARCHAR(255) NOT NULL,				  
				  `message` TEXT NOT NULL,				  
				  `days` INT UNSIGNED NOT NULL,
                  `send_on_date` TINYINT UNSIGNED NOT NULL,
                   `date` DATE NOT NULL
				) DEFAULT CHARSET=utf8;";			
			
			$wpdb->query($sql);
	  }
	  
	  if($wpdb->get_var("SHOW TABLES LIKE '$sentmails_table'") != $sentmails_table) {
	  
			$sql = "CREATE TABLE `" . $sentmails_table . "` (
				  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `mail_id` INT UNSIGNED NOT NULL,				  
				  `user_id` INT UNSIGNED NOT NULL,				  
				  `date` DATE NOT NULL
				) DEFAULT CHARSET=utf8;";
			$wpdb->query($sql);
	  }
	  
	  $old_bft_db_version=get_option('bft_db_version');
	  
	  // DB version 1.2, plugin version 1.5
      if(empty($old_bft_db_version) or $old_bft_db_version<1.2)
     {
         $sql="ALTER TABLE ".$mails_table." ADD `send_on_date` TINYINT UNSIGNED NOT NULL,
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
		 update_option( 'bft_subscribe_notify', $_POST['subscribe_notify'] );
		 update_option( 'bft_unsubscribe_notify', $_POST['unsubscribe_notify'] );
  }

  $bft_sender = stripslashes( get_option( 'bft_sender' ) );	
  $bft_redirect = stripslashes( get_option( 'bft_redirect' ) );	
  $bft_optin = stripslashes( get_option( 'bft_optin' ) );	
  
  echo '<div class="wrap">';
  require(BFT_PATH."/views/bft_main.html");
  echo '</div>';
}

/* Manages the mailing list */
function bft_list(){
	global $wpdb, $users_table;
	
	if(isset($_POST['email'])) $email=$wpdb->escape($_POST['email']);
	if(isset($_POST['user_name'])) $name=$wpdb->escape($_POST['user_name']);
	if(isset($_POST['id'])) $id=$wpdb->escape($_POST['id']);
	$status=$_POST['status'];
	
    $error=false;

	if(!empty($_POST['add_user'])) {
        // user exists?
        $exists=$wpdb->get_row($wpdb->prepare("SELECT *
                FROM $users_table WHERE email=%s", $email));

        if(empty($exists->id)) {
            $sql="INSERT IGNORE INTO $users_table (name,email,status,date,ip)
            VALUES (\"$name\",\"$email\",\"$status\",CURDATE(),'$_SERVER[REMOTE_ADDR]')";       
            $wpdb->query($sql);
            
            if($status) bft_welcome_mail($wpdb->insert_id);    
        }		
        else {
            $error=true;
            $err_msg="User with this email address already exists.";
        }
	}
	
	if(!empty($_POST['save_user'])) {
		$sql="UPDATE $users_table SET 
		date=CURDATE(),
		name=\"$name\",
		email=\"$email\",
		status=\"$status\"
		WHERE id='$id'";
		$wpdb->query($sql);
	}
	
	if(!empty($_POST['del_user'])) {
		$sql="DELETE FROM $users_table WHERE id='$id'";
		$wpdb->query($sql);
	}
	
	// select users from the mailing list
	$ob = in_array($_GET['ob'], array("email","name","ip","date","status,email"))? $_GET['ob'] : 'email';
	$sql="SELECT * FROM $users_table ORDER BY $ob";
	$users=$wpdb->get_results($sql);
	
	require(BFT_PATH."/views/bft_list.html");	
}

/* Manages the messages */
function bft_messages() {
	global $wpdb, $mails_table;
	
	if(isset($_POST['subject'])) $subject=$_POST['subject'];
	if(isset($_POST['message'])) $message=$_POST['message'];
	if(isset($_POST['days'])) $days=$_POST['days'];
	if(isset($_POST['id'])) $id=$_POST['id'];
    if(isset($_POST['send_on_date'])) $send_on_date=$_POST['send_on_date'];
    
    // prepare date
    $date=$_POST['dateyear']."-".$_POST['datemonth']."-".$_POST['dateday'];
    $date=$wpdb->escape($date);

	if(!empty($_POST['add_message'])) {
		$sql=$wpdb->prepare("INSERT INTO $mails_table (subject,message,days,send_on_date,date)
		VALUES (%s, %s, %d, %d, %s)", $subject, $message, $days, $send_on_date, $date);
		$wpdb->query($sql);
	}
	
	if(!empty($_POST['save_message'])) {
		$sql=$wpdb->prepare("UPDATE $mails_table SET
		subject=%s,
		message=%s,
		days=%d,
   	send_on_date=%d,
    date=%s
		WHERE id=%d", $subject, $message, $days, $send_on_date, $date, $id);		
		$wpdb->query($sql);
	}
	
	if(!empty($_POST['del_message'])) {
		$sql="DELETE FROM $mails_table WHERE id='$id'";		
		$wpdb->query($sql);
	}
	
	// select all messages ordered by days
	$sql="SELECT * FROM $mails_table ORDER BY days";
	$mails=$wpdb->get_results($sql);
	
	require(BFT_PATH."/views/bft_messages.html");
}

/* import/export */
function bft_import() {
	global $wpdb, $users_table;

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
			
			$sql="INSERT IGNORE INTO $users_table (date, name, email, status) 
			VALUES (CURDATE(), '$name','$email',1)";
			$wpdb->query($sql);
			
			bft_welcome_mail($wpdb->insert_id);
		}
		
		$success_text="<p style='color:red;'><b>".sizeof($rows)." members have been imported.</b></p>";
	}
	
	if(!empty($_POST['export'])) {
		if($_POST['active']) {
			$active_sql=" AND status='1' ";
		}
	
		$sql="SELECT * FROM $users_table
		WHERE 1 $active_sql
		ORDER BY email";		
		$members=$wpdb->get_results($sql);
		
		$content="";
			
		foreach($members as $member) {
			$content.="{$member->email},{$member->name}\n";
		}
	}

	require(BFT_PATH."/views/bft_import.html");
}

/* sends the first welcome mail to newly registered or imported user
if such has been scheduled. Scheduling of those mails is done by setting "0" for "days" */
function bft_welcome_mail($uid) {
	global $wpdb, $users_table, $mails_table;
	
	// select email
	$sql="SELECT * FROM $mails_table WHERE days=0";	
	$mail=$wpdb->get_row($sql);
		
	if(!$mail->id) return false;
	
	// select member
	$sql="SELECT * FROM $users_table WHERE id='$uid' AND status=1";
	$member=$wpdb->get_row($sql);
	if(!$member->id) return false;
	
	bft_customize($mail,$member);
}

/* private function called to customize an email message and send it */
function bft_customize($mail,$member) {
	// send mail to member
	$subject=$mail->subject;				
	$message=$mail->message;
	
	$subject=str_replace("{{name}}",$member->name,$subject);
	$subject=str_replace("{{email}}",$member->email,$subject);
	
	$message=str_replace("{{name}}",$member->name,$message);
	$message=str_replace("{{email}}",$member->email,$message);
				
	// add unsubscribe link
	$unsub_url = get_option('siteurl')."/?bft=bft_unsubscribe&email=".$member->email;
	$message.="<br><br>
	To unsubscribe from our list visit the url below:<br>
	<a href='$unsub_url'>$unsub_url</a>";
	$message=str_replace("\t","",$message);
	
	$message = do_shortcode($message);
	
	bft_mail(BFT_SENDER,$member->email,$subject,$message);
}

/* wrapper for wp_mail() function */
function bft_mail($from,$to,$subject,$message) {   	
   $headers=array();
	 $headers[] = "Content-Type: text/html";
	 $headers[] = 'From: '.$from;
	 $headers[] = 'sendmail_from: '.$from;
   
   $subject=stripslashes($subject);
   $message=stripslashes($message);   
   $message=wpautop($message);
   wp_mail($to, $subject, $message, $headers);
}

// handle all this stuff on template_redirect call so
// plugins and especially the possible WP MAIL SMTP are loaded!!
function bft_template_redirect() {
	global $wpdb, $users_table;	
	
	//  subscribe user
	if($_REQUEST['bft']=='register') {
		$status=!get_option( 'bft_optin' );
		
		$email=$wpdb->escape($_POST['email']);
		$name=$wpdb->escape($_POST['user_name']);
		
		$code=substr(md5($email.microtime()),0,8);
		
		// user exists
		$sql=$wpdb->prepare("SELECT id FROM $users_table WHERE email=%s", $email);
		$id = $wpdb->get_var($sql);		
		
		if(!$id) {			
			$sql="INSERT IGNORE INTO $users_table (name,email,status,code,date,ip)
			VALUES (\"$name\",\"$email\",'$status','$code',CURDATE(),'$_SERVER[REMOTE_ADDR]')";		
			$wpdb->query($sql);
			$id = $wpdb->insert_id;
		}
		else {
			$sql=$wpdb->prepare("UPDATE $users_table SET code=%s WHERE id=%d", $code, $id);
			$wpdb->query($sql);
		}
		$bft_redirect = stripslashes( get_option( 'bft_redirect' ) );	
		
		if($status) {
			$mid = $wpdb->insert_id;
			bft_welcome_mail($mid);
					
			// notify admin?
			if(get_option('bft_subscribe_notify')) {
				bft_subscribe_notify($mid);
			}	
			
			// display success message
			echo "<script language='javascript'>
			alert('".__('You have been subcribed!', 'broadfast')."');
			window.location='".($bft_redirect?$bft_redirect:site_url())."';
			</script>";
			exit;
		}
		else {
			// send confirmation email
			$url=site_url("?bft=bft_confirm&code=$code&id=$id");
			
			$subject=__("Please confirm your email", 'broadfast');				
			$message=__("Please click on the link below or copy and paste it in the browser address bar:<br><br>", 'broadfast').
			$url;		
			bft_mail(BFT_SENDER,$_POST['email'],$subject,$message);
			
			echo "<script language='javascript'>
			alert('".__('Please check your email. A confirmation link is sent to it.', 'broadfast')."');
			window.location='".($bft_redirect?$bft_redirect:site_url())."';
			</script>";
			exit;
		}
			
		add_filter('the_content', 'bft_screenmsg');
	}
	
	
	// unsubscribe user
	if($_REQUEST['bft']=='bft_unsubscribe') {
		$email=$wpdb->escape($_GET['email']);
		$sql="DELETE FROM $users_table WHERE email=\"$email\"";
		$wpdb->query($sql);
		
		//  notify admin?
		if(get_option('bft_unsubscribe_notify')) {
			bft_unsubscribe_notify($email);
		}
		
		wp_redirect(get_option('siteurl'));
	}
	
	// confirm user registration
	if($_REQUEST['bft']=='bft_confirm') {
		// select user
		$sql=$wpdb->prepare("SELECT * FROM $users_table WHERE id=%d AND code=%s", $_GET['id'], $_GET['code']);	
		$member=$wpdb->get_row($sql);	
		
		$bft_redirect = stripslashes( get_option( 'bft_redirect' ) );	
		
		if($member->id) {
			$sql="UPDATE $users_table SET 
			code='".substr(0,8,md5($code.time()))."',
			status=1
			WHERE id='{$member->id}'";		
			$wpdb->query($sql);
			
			bft_welcome_mail($member->id);
			
			// notify admin?
			if(get_option('bft_subscribe_notify')) {
				bft_subscribe_notify($member->id);
			}
		}
		
		wp_redirect($bft_redirect?$bft_redirect:get_option('siteurl'));
	}
}

// the actual autoresponder hook - it's run when the index page is loaded
function bft_hook_up() { 
	define('BFT_SENDER',get_option( 'bft_sender' ));
  require(BFT_PATH."/bft_hook.inc");    
}

// handle shortcode
function bft_shortcode_signup($attr) {			
	ob_start();
	require_once(BFT_PATH."/views/signup-form.html");
	$contents = ob_get_contents();
	ob_end_clean();
	
	return $contents;
}

// handle this properly
add_action('plugins_loaded', "bft_hook_up");

register_activation_hook(__FILE__,'bft_install');
add_action('plugins_loaded', 'bft_init');
add_action('admin_menu', 'bft_autoresponder_menu');
add_action('template_redirect', 'bft_template_redirect');
add_shortcode( 'BFTWP', "bft_shortcode_signup" );