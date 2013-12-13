<div class="wrap">

	<div class="postbox-container" style="width:73%;margin-right:2%;">	

			<div style="width:300px;float:left;padding:10px;">
				<h2><?php _e('Main Settings', 'broadfast')?></h2>
				<form method="post">
				<input type="hidden" name="settings_ok" value="Y">
				<p><label><?php _e('Sender of all emails:', 'broadfast')?></label> <input type="text" name="bft_sender" value="<?php echo $bft_sender?>"><br>
				<?php _e('Fill valid email address or name/email like this:</br> "Name &lt;email@domain.com&gt;"', 'broadfast')?></p>
				<p><label><?php _e('URL to redirect to after registration (optional):', 'broadfast')?></label> <input type="text" name="bft_redirect" value="<?php echo $bft_redirect?>" size="40"></p>
				<p><label><?php _e('Double opt-in:', 'broadfast')?></label> 
				<select name="bft_optin" onchange="if(this.value == '1') { jQuery('#bftOptinConfig').show();} else {jQuery('#bftOptinConfig').hide();}">
				<option value="0" <?if(empty($bft_optin)) echo "selected";?>><?php _e('No', 'broadfast')?></option>
				<option value="1" <?if($bft_optin) echo "selected";?>><?php _e('Yes', 'broadfast')?></option>
				</select></p>
				
				<p><input type="checkbox" name="subscribe_notify" value="1" <?php if(get_option('bft_subscribe_notify')) echo 'checked'?>> <?php _e('Notify me when someone subscribes/activates', 'broadfast')?></p>
				
				<p><input type="checkbox" name="unsubscribe_notify" value="1" <?php if(get_option('bft_unsubscribe_notify')) echo 'checked'?>> <?php _e('Notify me when someone unsubscribes', 'broadfast')?></p>			
				
				<p><input type="submit" value="<?php _e('Save Settings', 'broadfast')?>"></p>
				</form>
			</div>
			
			<div style="width:300px;float:left;padding:10px;">
				<h2><?php _e('Signup Form', 'broadfast')?></h2>
				<p><?php _e('Registration form HTML code <br>(Copy and paste in a post, page or your wordpress template):', 'broadfast')?></p>
				<textarea rows="10" cols="40" onclick="this.select();"><?php require(BFT_PATH."/views/signup-form.html.php");?></textarea>
				
				<p><?php _e('Or use Wordpress shortcode', 'broadfast')?> <strong>[BFTWP]</strong> <?php _e('inside a post or page.', 'broadfast')?></p>
			</div>
			
			
			<div id="bftOptinConfig" style="clear:both;display:<?php echo $bft_optin ? 'block': 'none';?>;">
				<hr />
				
				<form method="post">
					<h2><?php _e('Double Opt-in Email Message Configuration', 'broadfast')?></h2>
					<p><?php _e('Feel free to leave this empty - in such case a default message will be used.', 'broadfast')?></p>
					
					<p><label><?php _e('Message subject:', 'broadfast')?></label> <input type="text" size="60" name="optin_subject" value="<?php echo get_option('bft_optin_subject')?>"></p>
					<p><?php wp_editor(stripslashes(get_option('bft_optin_message')), 'optin_message')?></p>
					<p><?php _e('Please use the variable {{url}} to provide the confirmation link. If you do not provide it, it will be attached at the end of the message.', 'broadfast')?></p>
					<p><input type="submit" name="double_optin_ok" value="<?php _e('Save Double Opt-in Message', 'broadfast')?>"></p>
				</form>
			</div>
			
	</div>		
	<div id="bft-sidebar">
				<?php require(BFT_PATH."/views/sidebar.html.php");?>
	</div>	  
</div>			