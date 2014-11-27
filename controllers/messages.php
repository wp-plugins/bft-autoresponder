<?php
/* Manages the messages */
function bft_messages() {
	global $wpdb;
	$_att = new BFTAttachmentModel();
	
	$send_on_date='';
	if(isset($_POST['subject'])) $subject=$_POST['subject'];
	if(isset($_POST['message'])) $message=$_POST['message'];
	if(isset($_POST['days'])) $days=$_POST['days'];
	if(isset($_POST['id'])) $id=$_POST['id'];
   if(isset($_POST['send_on_date'])) $send_on_date=$_POST['send_on_date'];
    
    // prepare date
    if(!empty($_POST['dateyear'])) $date=$_POST['dateyear']."-".$_POST['datemonth']."-".$_POST['dateday'];
    else $date = date("Y-m-d");
    $date=esc_sql($date);

	if(!empty($_POST['add_message'])) {
		$sql=$wpdb->prepare("INSERT INTO ".BFT_MAILS." (subject,message,days,send_on_date,date, content_type)
		VALUES (%s, %s, %d, %d, %s, %s)", $subject, $message, @$days, $send_on_date, $date, $_POST['content_type']);
		$wpdb->query($sql);
		$id = $wpdb->insert_id;
		$_att->save_attachments($id, 'mail');
	}
	
	if(!empty($_POST['save_message'])) {
		$sql=$wpdb->prepare("UPDATE ".BFT_MAILS." SET
		subject=%s,
		message=%s,
		days=%d,
   	send_on_date=%d,
      date=%s,
      content_type = %s
		WHERE id=%d", $subject, $message, $days, $send_on_date, $date, $_POST['content_type'], $id);		
		$wpdb->query($sql);
		$_att->save_attachments($id, 'mail');
	}
	
	if(!empty($_POST['del_message'])) {
		$sql="DELETE FROM ".BFT_MAILS." WHERE id='$id'";		
		$wpdb->query($sql);
		$_att->delete_attachments($id, 'mail');
	}
	
	// select all messages ordered by days
	$sql="SELECT * FROM ".BFT_MAILS." ORDER BY days";
	$mails=$wpdb->get_results($sql);
	
	foreach($mails as $cnt=>$mail) {
		$attachments = $_att->select("mail", $mail->id);
		$mails[$cnt]->attachments = $attachments;
	}
	
	require(BFT_PATH."/views/bft_messages.html.php");
}