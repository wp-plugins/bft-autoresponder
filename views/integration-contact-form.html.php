<div class="wrap">
	<h1><?php _e('Mailing List to Contact Form Integration', 'broadfast')?></h1>
		
	<p><a href="admin.php?page=bft_options"><?php _e('Back to settings', 'broadfast')?></a></p>
	
	<p><?php printf(__('Using the shortcode below will display a checkbox for subscribing in the mailing list inside your contact form. We currently support integration with the most popular contact form plugins - <a href="%s" target="_blank">Contact Form 7</a> and JetPack.', 'broadfast'),'http://wordpress.org/plugins/contact-form-7/');?></p>
	
	<table cellspacing="10">
		<tr><td width="50%" valign="top">
			<div class="postbox wp-admin" style="padding:10px;">
				<form method="post" class="broadfast">					
					<p><input type="checkbox" name="checked_by_default" value="1" <?php if(!empty($_POST['checked_by_default'])) echo 'checked'?>> <?php _e('Checked by default', 'broadfast')?></p>
				
					<p><label><?php _e('CSS classes (optional):', 'broadfast')?></label> <input type="text" name="classes" value="<?php echo @$_POST['classes']?>"></p>
					<p><label><?php _e('HTML ID (optional):', 'broadfast')?></label> <input type="text" name="html_id" value="<?php echo @$_POST['html_id']?>"></p>
					<p><input type="submit" value="<?php _e('Refresh Shortcode', 'broadfast')?>"></p>
					
					<p><?php _e('Shortcode to use in contact form:', 'broadfast')?> 
					<textarea readonly="readonly" onclick="this.select()" rows="2" cols="40">[bft-int-chk<?php echo $shortcode_atts?>] <?php _e('Subscribe for the mailing list', 'broadfast')?></textarea></p>
					
					<h3><?php _e('Contact Form 7 Integration', 'broadfast')?></h3>
					<p><b><?php _e('Place this shortcode inside your Contact Form 7 contact form - right where you want the checkbox to appear.', 'broadfast')?></b></p>
					<p style="color:red;font-weight:bold;"><?php _e('Do not change the default contact form field names for name and email when editing your Contact Form 7 contact form. They should remain "your-name" and "your-email", otherwise the integration will not work.', 'broadfast');?></p>
					
					<h3><?php _e('Jetpack Contact Form Integration', 'broadfast')?></h3>
					<p><b><?php _e('Place this shortcode inside the published shortcode of your contact form - somewhere before the closing "[/contact-form]" shortcode.', 'broadfast')?></b></p>
				</form> 
			</div>
		</td><td width="50%" valign="top">
			<h3><?php printf(__('In the <a href="%s" target="_blank">PRO Version</a> you can also include all custom fields directly in your contact form.', 'broadfast'), 'http://calendarscrips.info/bft-pro/')?>	</h3>		
		</td></tr>	
	</table>	 
</div>