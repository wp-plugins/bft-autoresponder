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