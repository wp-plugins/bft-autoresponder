<?php
/* Manages the mailing list */
function bft_list(){
	global $wpdb;
	
	$per_page = 20;
	$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);		
	
	if(isset($_POST['email'])) $email=esc_sql($_POST['email']);
	if(isset($_POST['user_name'])) $name=esc_sql($_POST['user_name']);
	if(isset($_POST['id'])) $id=esc_sql($_POST['id']);
	$status=@$_POST['status'];
	
    $error=false;

	if(!empty($_POST['add_user'])) {
        // user exists?
        $exists=$wpdb->get_row($wpdb->prepare("SELECT *
                FROM ".BFT_USERS." WHERE email=%s", $email));

        if(empty($exists->id)) {
            $sql="INSERT IGNORE INTO ".BFT_USERS." (name,email,status,date,ip)
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
		$sql="UPDATE ".BFT_USERS." SET 
		date=CURDATE(),
		name=\"$name\",
		email=\"$email\",
		status=\"$status\"
		WHERE id='$id'";
		$wpdb->query($sql);
	}
	
	if(!empty($_POST['del_user'])) {
		$sql="DELETE FROM ".BFT_USERS." WHERE id='$id'";
		$wpdb->query($sql);
	}
	
	// mass delete
	if(!empty($_POST['mass_delete']) and !empty($_POST['del_ids'])) {		
		$wpdb->query("DELETE FROM ".BFT_USERS." WHERE id IN (".$_POST['del_ids'].")");
	}
	
	// select users from the mailing list
	$ob = in_array(@$_GET['ob'], array("email","name","ip","date","status,email"))? $_GET['ob'] : 'email';
	$sql="SELECT SQL_CALC_FOUND_ROWS * FROM ".BFT_USERS." ORDER BY $ob LIMIT $offset, $per_page";
	$users=$wpdb->get_results($sql);
	
	$count = $wpdb->get_var("SELECT FOUND_ROWS()");
	
	require(BFT_PATH."/views/bft_list.html.php");	
}
