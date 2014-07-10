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
	global $wpdb;   	
	if(empty($from)) $from = get_option('admin_email');

   $headers=array();
	 $headers[] = "Content-Type: text/html";
	 $headers[] = 'From: '.$from;
	 $headers[] = 'sendmail_from: '.$from;
   
   $subject=stripslashes($subject);
   $message=stripslashes($message);   
   $message=wpautop($message);
   if(BFT_DEBUG) echo( "From: $from To: $to<br>".$subject.'<br>'.$message."<br>");  
   $result = wp_mail($to, $subject, $message, $headers);
   
   // insert into the email log
   $status = $result ? 'OK' : 'Error';
   $wpdb->query($wpdb->prepare("INSERT INTO ".BFT_EMAILLOG." SET
   	sender=%s, receiver=%s, subject=%s, date=CURDATE(), status=%s",
   	$from, $to, $subject, $status));
   
   return $result;
}

function bft_subscribe($email, $name) {
	global $wpdb;
	
	$status=!get_option( 'bft_optin' );
	
	if(empty($email) or !strstr($email, '@')) wp_die(__("Please enter valid email address", 'broadfast'));
		
		$code=substr(md5($email.microtime()),0,8);
		
		// user exists
		$sql=$wpdb->prepare("SELECT id FROM ".BFT_USERS." WHERE email=%s", $email);
		$id = $wpdb->get_var($sql);		
		
		if(!$id) {			
			$sql="INSERT IGNORE INTO ".BFT_USERS." (name,email,status,code,date,ip)
			VALUES (\"$name\",\"$email\",'$status','$code',CURDATE(),'$_SERVER[REMOTE_ADDR]')";		
			$wpdb->query($sql);
			$id = $wpdb->insert_id;
		}
		else {
			$sql=$wpdb->prepare("UPDATE ".BFT_USERS." SET code=%s WHERE id=%d", $code, $id);
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
			alert('".__('You have been subscribed!', 'broadfast')."');
			window.location='".($bft_redirect?$bft_redirect:site_url())."';
			</script>";
			exit;
		}
		else {
			// send confirmation email
			$url=site_url("?bft=bft_confirm&code=$code&id=$id");
			
			$subject = get_option('bft_optin_subject');
			if(empty($subject)) $subject=__("Please confirm your email", 'broadfast');
			$subject = str_replace('{{name}}', $name, $subject);
			
			$message = get_option('bft_optin_message');	
			if(empty($message)) {							
				$message=__("Please click on the link below or copy and paste it in the browser address bar:<br><br>", 'broadfast').
				'<a href="'.$url.'">'.$url.'</a>';
			} else {
				if(strstr($message, '{{url}}')) $message = str_replace('{{url}}', $url, $message);
				else $message .= '<br><br><a href="'.$url.'">'.$url.'</a>';
			}
			$message = str_replace('{{name}}', $name, $message);

			// send the optin email			
			bft_mail(BFT_SENDER,$email,$subject,$message);
			
			echo "<script language='javascript'>
			alert('".__('Please check your email. A confirmation link is sent to it.', 'broadfast')."');
			window.location='".($bft_redirect?$bft_redirect:site_url())."';
			</script>";
			exit;
		}
}