<?php
global $wpdb;

$last_date=get_option("bft_date");
$mails_table= $wpdb->prefix . "bft_mails";
$users_table= $wpdb->prefix. "bft_users";

// run only once per day
if($last_date!=date("Y-m-d")) {
	update_option( 'bft_date', date("Y-m-d") );	
	
    // sequential mails
	$sql="SELECT * FROM $mails_table 
    WHERE days>0 AND send_on_date=0 ORDER BY id";
	$mails=$wpdb->get_results($sql);
        			
	foreach($mails as $mail)
	{
		// get users who need to get this mail sent today and send it
		$sql="SELECT * FROM $users_table
		WHERE date=CURDATE() - INTERVAL $mail->days DAY
		AND status=1
		ORDER BY id";			
		$members=$wpdb->get_results($sql);
        		
		if(sizeof($members))
		{
			foreach($members as $member)
			{
				bft_customize($mail,$member);
			}
		}
	}

    // now date mails
    $sql="SELECT * FROM $mails_table
    WHERE send_on_date=1 AND date=CURDATE()";
    $mails=$wpdb->get_results($sql);

    // select all users
    $sql="SELECT * FROM $users_table
    WHERE status=1
    ORDER BY id";           
    $members=$wpdb->get_results($sql);

    foreach($mails as $mail)
    {        
        if(sizeof($members))
        {
            foreach($members as $member)
            {
                bft_customize($mail,$member);
            }
        }
    }
}	