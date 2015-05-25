<?php
global $wpdb;
$last_date=get_option("bft_date");

// this array of mail_id-user_id pairs will ensure no duplicates are sent on the same function run
$currently_sent = array();

// don't start again if there is currently running cron job
// or if there is one, it should be at least 5 minutes old.
// used to avoid simultaneously running cron jobs
$last_start = get_option('bft_last_start');
if(!empty($last_start) and $last_start > (time() - 300)) {
	if($use_cron_job == 1) die(__('Wait 5 minutes before the next cron job.', 'broadfast'));
	else return true;
}

update_option( 'bft_date', date("Y-m-d") );	// no longer used to limit the cron job but still keep the info when it ran
update_option( 'bft_last_start', time() );	

 // sequential mails
$sql="SELECT * FROM {$wpdb->prefix}bft_mails 
 WHERE days>0 AND send_on_date=0 ORDER BY id";
$mails=$wpdb->get_results($sql);

// total mail limit for this run
$mail_limit = get_option('bft_mails_per_run');
$total_emails = 0;
     			
foreach($mails as $mail) {
	$attachments = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".BFT_ATTACHMENTS."
				WHERE mail_id = %d ORDER BY id", $mail->id));	
			
	// get users who need to get this mail sent today and send it
	$sql="SELECT * FROM {$wpdb->prefix}bft_users
	WHERE date='".date("Y-m-d")."' - INTERVAL $mail->days DAY
	AND status=1
	ORDER BY id";			
	$members=$wpdb->get_results($sql);
     		
	if(sizeof($members))	{
		foreach($members as $member)	{
			if(in_array($mail->id.'--'.$member->id, $currently_sent)) continue;				
			
			// don't send the same email twice
			$already_sent = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}bft_sentmails
				WHERE mail_id=%d AND date=%s AND user_id=%d", $mail->id, date("Y-m-d"), $member->id));
			
			if(!$already_sent) {
				$total_emails++;
				if(!empty($mail_limit) and $total_emails > $mail_limit) {
					update_option( 'bft_last_start', time() ); // finished successfully
					return true;
				}				
				
				// insert in sent mails
				$wpdb->query($wpdb->prepare("INSERT INTO ".BFT_SENTMAILS." SET
					mail_id=%d, user_id=%d, date=%s", $mail->id, $member->id, date("Y-m-d", current_time('timestamp'))));					
				
				$currently_sent[] = $mail->id.'--'.$member->id;
				bft_customize($mail,$member, $attachments);
			}
		}
	}
}

 // now date mails
 $sql="SELECT * FROM {$wpdb->prefix}bft_mails
 WHERE send_on_date=1 AND date=CURDATE()";
 $mails=$wpdb->get_results($sql);

 // select all users
 $sql="SELECT * FROM {$wpdb->prefix}bft_users
 WHERE status=1
 ORDER BY id";           
 $members=$wpdb->get_results($sql);

 foreach($mails as $mail) {        
     if(sizeof($members)) {
         foreach($members as $member) {
         	 if(in_array($mail->id.'--'.$member->id, $currently_sent)) continue;
         	 
         	 // don't send the same email twice
				 $already_sent = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}bft_sentmails
					WHERE mail_id=%d AND date=%s AND user_id=%d", $mail->id, date("Y-m-d"), $member->id));					
         		
             if(!$already_sent) {
             	$total_emails++;
					if(!empty($mail_limit) and $total_emails > $mail_limit) {
						update_option( 'bft_last_start', time() ); // finished successfully
						return true;
					}	
				
             	$wpdb->query($wpdb->prepare("INSERT INTO ".BFT_SENTMAILS." SET
						mail_id=%d, user_id=%d, date=%s", $mail->id, $member->id, date("Y-m-d", current_time('timestamp'))));
					
             	$currently_sent[] = $mail->id.'--'.$member->id;
             	bft_customize($mail,$member, $attachments);
             }
         }
     }
 }
 
 update_option( 'bft_last_start', time() ); // finished successfully
