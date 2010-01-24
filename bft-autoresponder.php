<?php
/*
Plugin Name: BFT Light
Plugin URI: http://calendarscripts.info/autoresponder-wordpress.html
Description: This is a sequential autoresponder that can send automated messages to your mailing list. For more advanced stand-alone script check our <a href="http://calendarscripts.info/php-auto-responder.html">PHP Autoresponder</a>
Author: Bobby Handzhiev
Version: 1.0
Author URI: http://pimteam.net/
*/ 

/*  Copyright 2008  Bobby Handzhiev (email : admin@pimteam.net)

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

require_once(ABSPATH . 'wp-includes/pluggable.php');

/* Adds the menu items */

function bft_autoresponder_menu() {  
  add_menu_page('BFT Autoresponder', 'BFT Autoresponder', 7, __FILE__, 'bft_options');
  add_submenu_page(__FILE__, 'Autoresponder', 'Options', 7, __FILE__, 'bft_options');
  add_submenu_page(__FILE__,'Your Mailing List', 'Mailing List', 7, "bft_list", "bft_list");
  add_submenu_page(__FILE__,'Import/Export Members', 'Import/Export', 7, "bft_import", "bft_import");
  add_submenu_page(__FILE__,'Manage Messages', 'Email Messages', 7, "bft_messages", "bft_messages");
  
}


/* Creates the mysql tables needed to store mailing list and messages */
$bft_db_version="1.0";
$users_table= $wpdb->prefix. "bft_users";
$mails_table= $wpdb->prefix . "bft_mails";
$sentmails_table= $wpdb->prefix . "bft_sentmails";
$bft_msg="";
define('BFT_SENDER',get_option( 'bft_sender' ));

function bft_install()
{
	 global $wpdb, $bft_db_version;    
	 
	 $users_table= $wpdb->prefix."bft_users";
	 $mails_table= $wpdb->prefix . "bft_mails";
	 $sentmails_table= $wpdb->prefix . "bft_sentmails";
	 
	  if($wpdb->get_var("SHOW TABLES LIKE '$users_table'") != $users_table) {
	  		
			$sql = "CREATE TABLE " . $users_table . " (
				  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  email VARCHAR(255) NOT NULL UNIQUE,	
				  name VARCHAR(255)	NOT NULL,		  
				  status TINYINT UNSIGNED NOT NULL,
				  date DATE NOT NULL,
				  code VARCHAR(10) NOT NULL
				);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');				
			dbDelta($sql);
	  }
	  
	  if($wpdb->get_var("SHOW TABLES LIKE '$mails_table'") != $mails_table) {
	  
			$sql = "CREATE TABLE `" . $mails_table . "` (
				  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `subject` VARCHAR(255) NOT NULL,				  
				  `message` TEXT NOT NULL,				  
				  `days` INT UNSIGNED NOT NULL
				);";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
	  }
	  
	  if($wpdb->get_var("SHOW TABLES LIKE '$sentmails_table'") != $sentmails_table) {
	  
			$sql = "CREATE TABLE `" . $sentmails_table . "` (
				  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `mail_id` INT UNSIGNED NOT NULL,				  
				  `user_id` INT UNSIGNED NOT NULL,				  
				  `date` DATE NOT NULL
				);";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
	  }
	  
	   add_option("bft_db_version", $bft_db_version);
}

/* Stores the autoresponder configuration */
function bft_options() {

  if(!empty($_POST['settings_ok']))
  {
  	 // save autoresponder settings
	 update_option( 'bft_sender', $_POST['bft_sender'] );
	 update_option( 'bft_redirect', $_POST['bft_redirect'] );
	 update_option( 'bft_optin', $_POST['bft_optin'] );
  }

  $bft_sender = stripslashes( get_option( 'bft_sender' ) );	
  $bft_redirect = stripslashes( get_option( 'bft_redirect' ) );	
  $bft_optin = stripslashes( get_option( 'bft_optin' ) );	
  
  echo '<div class="wrap">';
  require("bft_main.html");
  echo '</div>';
}

/* Manages the mailing list */
function bft_list(){
	global $wpdb, $users_table;
	
	if(isset($_POST['email'])) $email=$wpdb->escape($_POST['email']);
	if(isset($_POST['name'])) $name=$wpdb->escape($_POST['name']);
	if(isset($_POST['id'])) $id=$wpdb->escape($_POST['id']);
	$status=$_POST['status'];
	
	if(!empty($_POST['add_user']))
	{
		$sql="INSERT IGNORE INTO $users_table (name,email,status,date)
		VALUES (\"$name\",\"$email\",\"$status\",CURDATE())";		
		$wpdb->query($sql);
		
		if($status) bft_welcome_mail($wpdb->insert_id);
	}
	
	if(!empty($_POST['save_user']))
	{
		$sql="UPDATE $users_table SET 
		date=CURDATE(),
		name=\"$name\",
		email=\"$email\",
		status=\"$status\"
		WHERE id='$id'";
		$wpdb->query($sql);
	}
	
	if(!empty($_POST['del_user']))
	{
		$sql="DELETE FROM $users_table WHERE id='$id'";
		$wpdb->query($sql);
	}
	
	// select users from the mailing list
	$sql="SELECT * FROM $users_table ORDER BY email";
	$users=$wpdb->get_results($sql);
	
	require("bft_list.html");	
}

/* Manages the messages */
function bft_messages()
{
	global $wpdb, $mails_table;
	
	if(isset($_POST['subject'])) $subject=$wpdb->escape($_POST['subject']);
	if(isset($_POST['message'])) $message=$wpdb->escape($_POST['message']);
	if(isset($_POST['days'])) $days=$wpdb->escape($_POST['days']);
	if(isset($_POST['id'])) $id=$wpdb->escape($_POST['id']);

	if(!empty($_POST['add_message']))
	{
		$sql="INSERT INTO $mails_table (subject,message,days)
		VALUES (\"$subject\",\"$message\",\"$days\")";
		$wpdb->query($sql);
	}
	
	if(!empty($_POST['save_message']))
	{
		$sql="UPDATE $mails_table SET
		subject=\"$subject\",
		message=\"$message\",
		days=\"$days\"
		WHERE id='$id'";		
		$wpdb->query($sql);
	}
	
	if(!empty($_POST['del_message']))
	{
		$sql="DELETE FROM $mails_table WHERE id='$id'";		
		$wpdb->query($sql);
	}
	
	// select all messages ordered by days
	$sql="SELECT * FROM $mails_table ORDER BY days";
	$mails=$wpdb->get_results($sql);
	
	require("bft_messages.html");
}

/* import/export */
function bft_import()
{
	global $wpdb, $users_table;

	if(!empty($_POST['import']))
	{
		if(empty($_FILES["file"]["name"]))
		{
			die("Please upload file");
		}
		
		if(empty($_POST["delim"]))
		{
			die("There must be a delimiter");
		}
		
		$rows=file($_FILES["file"]["tmp_name"]);
	
		foreach($rows as $row)
		{
			//explode values
			$values=explode($_POST["delim"],$row);
			$position=$_POST['email_column']-1;
			$name_position=$_POST['name_column']-1;
			if($position<0 or !is_numeric($position)) $position=0;			
			if($name_position<0 or !is_numeric($name_position)) $name_position=1;			
			$email=trim($values[$position]);
			$name=trim($values[$name_position]);
			
			$sql="INSERT IGNORE INTO $users_table (date, name, email, status,date) 
			VALUES (CURDATE(), '$name','$email',1,CURDATE())";
			$wpdb->query($sql);
			
			bft_welcome_mail($wpdb->insert_id);
		}
		
		$success_text="<p style='color:red;'><b>".sizeof($rows)." members have been imported.</b></p>";
	}
	
	if(!empty($_POST['export']))
	{
		if($_POST['active'])
		{
			$active_sql=" AND status='1' ";
		}
	
		$sql="SELECT * FROM $users_table
		WHERE 1 $active_sql
		ORDER BY email";		
		$members=$wpdb->get_results($sql);
		
		$content="";
			
		foreach($members as $member)
		{
			$content.="{$member->email},{$member->name}\n";
		}
	}

	require("bft_import.html");
}

/* sends the first welcome mail to newly registered or imported user
if such has been scheduled. Scheduling of those mails is done by setting "0" for "days" */
function bft_welcome_mail($uid)
{
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
function bft_customize($mail,$member)
{
	// send mail to member
	$subject=$mail->subject;				
	$message=$mail->message;
	
	$subject=str_replace("{{name}}",$member->name,$subject);
	$subject=str_replace("{{email}}",$member->email,$subject);
	
	$message=str_replace("{{name}}",$member->name,$message);
	$message=str_replace("{{email}}",$member->email,$message);
				
	// add unsubscribe link
	$message.="<br><br>
	To unsubscribe from our list visit the url below:<br>".get_option('siteurl')
						."?bft=bft_unsubscribe&email=".$member->email;
	$message=str_replace("\t","",$message);
	
	bft_mail(BFT_SENDER,$member->email,$subject,$message);
}

/* wrapper for mail() function */
function bft_mail($from,$to,$subject,$message)
{   	
   $mail_mime .= "MIME-Version: 1.0\n";
   $mail_mime .= "Content-Type: text/html; charset=UTF-8\n";
   $mail_mime .= "Message-ID: <".md5($to).time()."@".$_SERVER['HTTP_HOST'].">\n";   
   
   mail($to, $subject, $message,"Reply-to: $from\nFrom: $from\n".$mail_mime, "-f $from");
}


//  subscribe user
if($_REQUEST['bft']=='register')
{
	$status=!get_option( 'bft_optin' );
	
	$email=$wpdb->escape($_POST['email']);
	$name=$wpdb->escape($_POST['name']);
	
	$code=substr(md5($email.microtime()),0,8);
	
	$sql="INSERT IGNORE INTO $users_table (name,email,status,code,date)
	VALUES (\"$name\",\"$email\",'$status','$code',CURDATE())";		
	$wpdb->query($sql);
	
	if($status)
	{
		bft_welcome_mail($wpdb->insert_id);
	
		// display success message
		echo "<script language='javascript'>
		alert('You have been subcribed!');
		window.location='index.php';
		</script>";
		exit;
	}
	else
	{
		// send confirmation email
		$url=get_option('siteurl')."/index.php?bft=bft_confirm&code=$code&email=$email";
		
		$subject="Please confirm your email";				
		$message="Please click on the link below or copy and paste it in the browser address bar:<br><br>
		$url";		
		bft_mail(BFT_SENDER,$_POST['email'],$subject,$message);
		
		echo "<script language='javascript'>
		alert('Please check your email. A confirmation link is sent to it.');
		window.location='index.php';
		</script>";
		exit;
	}
		
	add_filter('the_content', 'bft_screenmsg');
}


// unsubscribe user
if($_REQUEST['bft']=='bft_unsubscribe')
{
	$email=$wpdb->escape($_GET['email']);
	$sql="DELETE FROM $users_table WHERE email=\"$email\"";
	$wpdb->query($sql);
	
	wp_redirect(get_option('siteurl'));
}

// confirm user registration
if($_REQUEST['bft']=='bft_confirm')
{
	// select user
	$email=$wpdb->escape($_GET['email']);
	$code=$wpdb->escape($_GET['code']);
	$sql="SELECT * FROM $users_table WHERE email='$email' AND code='$code'";	
	$member=$wpdb->get_row($sql);	
	
	if($member->id)
	{
		$sql="UPDATE $users_table SET 
		code='".substr(0,8,md5($code.time()))."',
		status=1
		WHERE id='{$member->id}'";		
		$wpdb->query($sql);
		
		bft_welcome_mail($member->id);
	}
	
	wp_redirect(get_option('siteurl'));
}

// the actual autoresponder hook - it's run when the index page is loaded
require("bft_hook.php");

register_activation_hook(__FILE__,'bft_install');
add_action('admin_menu', 'bft_autoresponder_menu');
?>