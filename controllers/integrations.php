<?php
class BFTIntegrations {
	// currently integrates with contact form 7
	static function contact_form() {
		global $wpdb;		
		
		$shortcode_atts = '';
		if(!empty($_POST['checked_by_default'])) {
			$shortcode_atts .= ' checked="true" ';
		}
		if(!empty($_POST['required'])) {
			$shortcode_atts .= ' required="true" ';
		}
		if(!empty($_POST['classes'])) {
			$shortcode_atts .= ' css_classes="'.$_POST['classes'].'" ';
		}
		if(!empty($_POST['html_id'])) {
			$shortcode_atts .= ' html_id="'.$_POST['html_id'].'" ';
		}
		
		// change your-name and your-email to custom field names
		if(!empty($_POST['change_defaults'])) {
			update_option('bft_cf7_name_field', $_POST['cf7_name_field']);
			update_option('bft_cf7_email_field', $_POST['cf7_email_field']);
		}
		
		// load default field names
		$custom_name_field_name = get_option('bft_cf7_name_field');
		$name_name = !empty($custom_name_field_name) ? $custom_name_field_name : 'your-name'; 
		$custom_email_field_name = get_option('bft_cf7_email_field');
		$email_name = !empty($custom_email_field_name) ? $custom_email_field_name : 'your-email';
		
		require(BFT_PATH."/views/integration-contact-form.html.php");
	}
	
	// signup user from contact form 7 or jetpack
	// $data - $_POST data
	static function signup($data, $user) {
		global $wpdb;
		
		$data = $_POST;
		if(empty($data['bft_int_signup'])) return true;
				
		bft_subscribe($user['email'], $user['name'], true);
	}
}