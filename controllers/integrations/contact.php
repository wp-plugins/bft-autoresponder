<?php
class BFTContactForm7 {
	static function signup($contactform) {
		global $wpdb;
		
		$data = $_POST;
		if(empty($data['bft_int_signup'])) return true;
		
		// field names default to 'your-name' and 'your-email' but user can change them.
		// in such case they need to setup this in the integrations page
		$custom_name_field_name = get_option('bft_cf7_name_field');
		$name_name = !empty($custom_name_field_name) ? $custom_name_field_name : 'your-name'; 
		$custom_email_field_name = get_option('bft_cf7_email_field');
		$email_name = !empty($custom_email_field_name) ? $custom_email_field_name : 'your-email';
		
		// signup activated, so let's get data
		$user = array('email' => "", "name"=>"");	
		$user['email'] = !empty( $data[$email_name] ) ? trim( $data[$email_name] ) : '';
	   $user['name'] = !empty( $data[$name_name] ) ? trim( $data[$name_name] ) : '';
	   
		BFTIntegrations :: signup($data, $user);		
	} // end signup
	
	static function shortcode_filter($form) {
		return do_shortcode( $form );
	}
	
	static function int_chk($atts) {
		// allow passing CSS, ID, onlick, default checked, etc		
		$html_id = $classes = $checked = '';
		if(!empty($atts['required']) and $atts['required'] == 'true') $classes .= ' wpcf7-validates-as-required ';
		if(!empty($atts['css_classes'])) $classes .= ' '.$atts['css_classes'].' ';
		if(!empty($atts['html_id'])) $html_id = $atts['html_id'];
		
		if(!empty($atts['checked']) and $atts['checked'] == 'true') $checked = ' checked="checked" ';
		
		// now output the checkbox
		return '<input type="checkbox" name="bft_int_signup" value="1" class="'.$classes.'" id="'.$html_id.'" '.$checked.'>';
   }
}