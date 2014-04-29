<?php
global $wpdb;

$last_date=get_option("bft_date");

// don't start again if there is currently running cron job
// or if there is one, it should be at least 5 minutes old.
// used to avoid simultaneously running cron jobs
$last_start = get_option('bft_last_start');
if(!empty($last_start) and $last_start > (time() - 300)) return true;

// run only once per day
if($last_date!=date("Y-m-d")) {
	update_option( 'bft_date', date("Y-m-d") );	
	update_option( 'bft_last_start', time() );	
	
    // sequential mails
	$sql="SELECT * FROM {$wpdb->prefix}bft_mails 
    WHERE days>0 AND send_on_date=0 ORDER BY id";
	$mails=$wpdb->get_results($sql);
        			
	foreach($mails as $mail) {
		// get users who need to get this mail sent today and send it
		$sql="SELECT * FROM {$wpdb->prefix}bft_users
		WHERE date=CURDATE() - INTERVAL $mail->days DAY
		AND status=1
		ORDER BY id";			
		$members=$wpdb->get_results($sql);
        		
		if(sizeof($members))	{
			foreach($members as $member)	{
				bft_customize($mail,$member);
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
                bft_customize($mail,$member);
            }
        }
    }
    
    update_option( 'bft_last_start', '' ); // finished successfully
}	