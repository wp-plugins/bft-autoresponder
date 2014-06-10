<?php
class BFTContactForm7 {
	static function signup($contactform) {
		global $wpdb;
		
		$data = $contactform->posted_data;
		if(empty($data['bft_int_signup'])) return true;
		
		// signup activated, so let's get data
		$user = array('email' => "", "name"=>"");	
		$user['email'] = !empty( $data['your-email'] ) ? trim( $data['your-email'] ) : '';
	   $user['name'] = !empty( $data['your-name'] ) ? trim( $data['your-name'] ) : '';
	   
		if ( !empty( $data['your-first-name'] ) and !empty( $data['your-last-name'] ) ) {
			$user['name'] = trim( $data['your-first-name']).' '.trim($data['your-last-name']) ;
		}
		
		bft_subscribe($user['email'], $user['name']);			
	} // end signup
	
	static function shortcode_filter($form) {
		return do_shortcode( $form );
	}
	
	static function int_chk($atts) {
		// allow passing CSS, ID, onlick, default checked, etc		
		$html_id = $classes = $chedked = '';
		if(!empty($atts['required']) and $atts['required'] == 'true') $classes .= ' wpcf7-validates-as-required ';
		if(!empty($atts['css_classes'])) $classes .= ' '.$atts['css_classes'].' ';
		if(!empty($atts['html_id'])) $html_id = $atts['html_id'];
		
		if(!empty($atts['checked']) and $atts['checked'] == 'true') $checked = ' checked="checked" ';
		
		// now output the checkbox
		return '<input type="checkbox" name="bft_int_signup" value="1" class="'.$classes.'" id="'.$html_id.'" '.$checked.'>';
   }
}