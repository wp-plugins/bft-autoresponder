<?php
/*
Plugin Name: BFT Light
Plugin URI: http://calendarscripts.info/autoresponder-wordpress.html
Description: This is an include file called when the plugin is loaded from the front end. It checks if any mails need to be sent today and sends them. 
Author: Bobby Handzhiev
Version: 1.5
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

global $wpdb;

$last_date=get_option("bft_date");
$mails_table= $wpdb->prefix . "bft_mails";
$users_table= $wpdb->prefix. "bft_users";

// run only once per day
if($last_date!=date("Y-m-d"))
{
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
	
	update_option( 'bft_date', date("Y-m-d") );
}	