	<?php
// Adapted code from the MIT licensed QuickDD class
// created also by me
function BFTquickDD_date($name, $date=NULL, $format=NULL, $markup=NULL, $start_year=1900, $end_year=2100) {
   // normalize params
   if(empty($date) or !preg_match("/\d\d\d\d\-\d\d-\d\d/",$date)) $date=date("Y-m-d");
    if(empty($format)) $format="YYYY-MM-DD";
    if(empty($markup)) $markup=array();

    $parts=explode("-",$date);
    $html="";

    // read the format
    $format_parts=explode("-",$format);

    $errors=array();
    
    // let's output
    foreach($format_parts as $cnt=>$f) {
        if(preg_match("/[^YMD]/",$f)) 
        { 
            $errors[]="Unrecognized format part: '$f'. Skipped.";
            continue;
        }

        // year
        if(strstr($f,"Y"))
        {
            $extra_html="";
            if(isset($markup[$cnt]) and !empty($markup[$cnt])) $extra_html=" ".$markup[$cnt];
            $html.=" <select name=\"".$name."year\"".$extra_html.">\n";

            for($i=$start_year;$i<=$end_year;$i++)
            {
                $selected="";
                if(!empty($parts[0]) and $parts[0]==$i) $selected=" selected";
                
                $val=$i;
                // in case only two digits are passed we have to strip $val for displaying
                // it's either 4 or 2, everything else is ignored
                if(strlen($f)<=2) $val=substr($val,2);        
                
                $html.="<option value='$i'".$selected.">$val</option>\n";
            }

            $html.="</select>";    
        }

        // month
        if(strstr($f,"M"))
        {
            $extra_html="";
            if(isset($markup[$cnt]) and !empty($markup[$cnt])) $extra_html=" ".$markup[$cnt];
            $html.=" <select name=\"".$name."month\"".$extra_html.">\n";

            for($i=1;$i<=12;$i++)
            {
                $selected="";
                if(!empty($parts[1]) and intval($parts[1])==$i) $selected=" selected";
                
                $val=sprintf("%02d",$i);
                    
                $html.="<option value='$val'".$selected.">$val</option>\n";
            }

            $html.="</select>";    
        }

        // day - we simply display 1-31 here, no extra intelligence depending on month
        if(strstr($f,"D")) {
            $extra_html="";
            if(isset($markup[$cnt]) and !empty($markup[$cnt])) $extra_html=" ".$markup[$cnt];
            $html.=" <select name=\"".$name."day\"".$extra_html.">\n";

            for($i=1;$i<=31;$i++) {
                $selected="";
                if(!empty($parts[2]) and intval($parts[2])==$i) $selected=" selected";
                
                if(strlen($f)>1) $val=sprintf("%02d",$i);
                else $val=$i;
                    
                $html.="<option value='$val'".$selected.">$val</option>\n";
            }

            $html.="</select>";    
        }
    }

    // that's it, return dropdowns:
    return $html;
}

// send notice when someone subscribes
function bft_subscribe_notify($mid) {
	global $wpdb;	
	$member = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".BFT_USERS." WHERE id=%d", $mid));

	$subject = get_option('bft_subscribe_notify_subject');
	if(empty($subject)) $subject = __("New user subscribed to the mailing list at", 'broadfast').' '.get_option('blogname');
	$message = get_option('bft_subscribe_notify_message');
	if(empty($message)) $message = __('User details:', 'broadfast')."<br><p>".__('Name:', 'broadfast').' '.$member->name.'</p><p>'.__('Email:', 'broadfast').' '.$member->email;
	
	// replace variables if any
	$message = str_replace('{{{blog-name}}}', get_option('blogname'), $message);
	$message = str_replace('{{{subscriber-name}}}', $member->name, $message);
	$message = str_replace('{{{subscriber-email}}}', $member->email, $message);
	$message = str_replace('{{{date}}}', date(get_option('date_format'), time()), $message);
	
	$subject = stripslashes($subject);
	$message = stripslashes($message);
	
	$admin_email = get_option('bft_sender');
	
	bft_mail($admin_email, $admin_email, $subject, $message);	 
}

// send notice when someone unsubscribes
function bft_unsubscribe_notify($user) {
	
	 $subject = get_option('bft_unsubscribe_notify_subject');
	 if(empty($subject)) $subject = __("An user unsubscribed from the mailing list at", 'broadfast').' '.get_option('blogname');	
	 
	 $message = get_option('bft_unsubscribe_notify_message');
	 if(empty($message)) {
		 $message = __('User name:', 'broadfast').' <b>'.$user->name."</b><br>";
		 $message .= __('User email:', 'broadfast').' <b>'.$user->email."</b><br>";
		 $message .= __('Registration date:', 'broadfast').' <b>'.date(get_option('date_format', strtotime($user->date)))."</b>";
 	 }
 	 
 	 // replace variables if any
	$message = str_replace('{{{blog-name}}}', get_option('blogname'), $message);
	$message = str_replace('{{{subscriber-name}}}', $user->name, $message);
	$message = str_replace('{{{subscriber-email}}}', $user->email, $message);
	$message = str_replace('{{{date}}}', date(get_option('date_format'), strtotime($user->date)), $message);
 	 
 	 $subject = stripslashes($subject);
	 $message = stripslashes($message);
	 
	 $admin_email = get_option('bft_sender');	
	 bft_mail($admin_email, $admin_email, $subject, $message);	 
}

/* wrapper for wp_mail() function */
function bft_mail($from,$to,$subject,$message) {   	
	if(empty($from)) $from = get_option('admin_email');

   $headers=array();
	 $headers[] = "Content-Type: text/html";
	 $headers[] = 'From: '.$from;
	 $headers[] = 'sendmail_from: '.$from;
   
   $subject=stripslashes($subject);
   $message=stripslashes($message);   
   $message=wpautop($message);
   // echo( $subject.'<br>'.$message);  
   return wp_mail($to, $subject, $message, $headers);
}