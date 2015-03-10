<div class="wrap">

	<div class="postbox-container" style="width:73%;margin-right:2%;">	

			<div style="width:500px;float:left;padding:10px;">
				<h2><?php _e('Main Settings', 'broadfast')?></h2>
				<form method="post">
				<input type="hidden" name="settings_ok" value="Y">
				<p><label><?php _e('Sender of all emails:', 'broadfast')?></label> <input type="text" name="bft_sender" value="<?php echo $bft_sender?>" size="30"><br>
				<span style="color:red;"><?php _e('Fill valid email address or name/email like this:</br> <b>Name &lt;email@domain.com&gt;</b></span>', 'broadfast')?></p>
				<p><label><?php _e('URL to redirect to after registration (optional):', 'broadfast')?></label> <input type="text" name="bft_redirect" value="<?php echo $bft_redirect?>" size="50"></p>
				<p><label><?php _e('Double opt-in:', 'broadfast')?></label> 
				<select name="bft_optin" onchange="if(this.value == '1') { jQuery('#bftOptinConfig').show();} else {jQuery('#bftOptinConfig').hide();}">
				<option value="0" <?if(empty($bft_optin)) echo "selected";?>><?php _e('No', 'broadfast')?></option>
				<option value="1" <?if($bft_optin) echo "selected";?>><?php _e('Yes', 'broadfast')?></option>
				</select></p>
				
				<p><input type="checkbox" name="subscribe_notify" value="1" <?php if($subscribe_notify) echo 'checked'?> onclick="this.checked ? jQuery('#subscribeNotify').show() : jQuery('#subscribeNotify').hide();"> <?php _e('Notify me when someone subscribes/activates', 'broadfast')?></p>
				<div id="subscribeNotify" style="display:<?php echo $subscribe_notify ? 'block' : 'none';?>">
					[<a href="admin.php?page=bft_messages_config&message=subscribe_notify"><?php _e('Configure this message', 'broadfast')?></a>]				
				</div>
				
				<p><input type="checkbox" name="unsubscribe_notify" value="1" <?php if($unsubscribe_notify) echo 'checked'?>  onclick="this.checked ? jQuery('#unsubscribeNotify').show() : jQuery('#unsubscribeNotify').hide();"> <?php _e('Notify me when someone unsubscribes', 'broadfast')?></p>			
				
				<div id="unsubscribeNotify" style="display:<?php echo $unsubscribe_notify ? 'block' : 'none';?>">
					[<a href="admin.php?page=bft_messages_config&message=unsubscribe_notify"><?php _e('Configure this message', 'broadfast')?></a>]				
				</div>
				
				<p><input type="checkbox" name="auto_subscribe" <?php if(get_option('bft_auto_subscribe') == 1) echo 'checked'?> value="1"> <?php _e('Automatically subscribe to the mailing list all new users who register in my blog. (To avoid spam this will happen when users login for the first time)', 'broadfast')?></p>
				
				<p><input type="checkbox" name="use_cron_job" value="1" <?php if($use_cron_job == 1) echo 'checked'?> onclick="this.checked ? jQuery('#bftCronJob').show() : jQuery('#bftCronJob').hide();"> <?php _e('I will set up a cron job to send my autoresponder emails.', 'watupro')?><br>
				<?php _e("(If you don't select this option the email sending will be done the first time when someone (or a bot) visits your site for the day.)", 'watupro')?></p>
				
				<div id="bftCronJob" style="display:<?php echo ($use_cron_job == 1) ? 'block': 'none';?>">
					<?php _e('Cron jobs are scheduled tasks that run on your server. This is the preferred setting but you will need to set up a cron job through your web host control panel. To handle this I recommend to set up a cron job on your server. It needs to run once per day, at a time chosen by you. Here is a <a href="http://calendarscripts.info/cron-jobs-tutorial.html" target="_blank">quick and easy guide</a> how to do it. The exact command you need to set is:', 'broadfast')?></p>	  
				   <p><input type="text" size="80" value="curl <?php echo site_url("?bft_cron=1");?>" readonly="readonly" onclick="this.select()"></p>				  
				   <p><?php _e('In case the above does not work on your host please try:', 'broadfast')?></p>				  
				   <p><input type="text" size="80" value="wget <?php echo site_url("?bft_cron=1");?>" readonly="readonly" onclick="this.select()"></p>				  
				   <p><?php printf(__('You can also run the cron job manually by visiting <a href="%s" target="_blank">the link</a> in your browser. If there are no errors you will see just a blank page with text "Running in cron job mode".', 'broadfast'), site_url("?bft_cron=1"))?></p>						
				</div>
				
				<p><input type="submit" value="<?php _e('Save Settings', 'broadfast')?>"></p>
				</form>
			</div>
			
			<div style="width:400px;float:left;padding:10px;">
				<h2><?php _e('Signup Form', 'broadfast')?></h2>
				<p><?php _e('Registration form HTML code <br>(Copy and paste in a post, page or your wordpress template):', 'broadfast')?></p>
				<textarea rows="10" cols="40" onclick="this.select();"><?php require(BFT_PATH."/views/signup-form.html.php");?></textarea>
				
				<p><?php _e('Or use Wordpress shortcode', 'broadfast')?> <strong>[BFTWP]</strong> <?php _e('inside a post or page.', 'broadfast')?></p>
				
				<p align="center"><a href="admin.php?page=bft_integrate_contact"><?php _e('Integrate in Contact Form', 'bftpro')?></a></p>
			</div>
			
			
			<div id="bftOptinConfig" style="clear:both;display:<?php echo $bft_optin ? 'block': 'none';?>;">
				<hr />
				
				<form method="post">
					<h2><?php _e('Double Opt-in Redirect Configuration', 'broadfast')?></h2>
					<p><?php _e('URL to redirect after double opt-in (optional):', 'broadfast')?> <input type="text" name="bft_optin_redirect" value="<?php echo $bft_optin_redirect?>" size="50"><br>
					<?php _e('(If you leave this empty, the value from  "URL to redirect to after registration" will be used)', 'broadfast')?> </p>				
				
					<h2><?php _e('Double Opt-in Email Message Configuration', 'broadfast')?></h2>
					<p><?php _e('Feel free to leave this empty - in such case a default message will be used.', 'broadfast')?></p>
					
					<p><label><?php _e('Message subject:', 'broadfast')?></label> <input type="text" size="60" name="optin_subject" value="<?php echo get_option('bft_optin_subject')?>"></p>
					<p><?php wp_editor(stripslashes(get_option('bft_optin_message')), 'optin_message')?></p>
					<p><?php _e('Please use the variable {{url}} to provide the confirmation URL. If you do not provide it, it will be attached at the end of the message.', 'broadfast')?></p>
					<p><b><?php _e('You can use the variable {{name}} to address the user by name.', 'broadfast')?></b></p>
					<p><input type="submit" name="double_optin_ok" value="<?php _e('Save Double Opt-in Message', 'broadfast')?>"></p>
				</form>
			</div>
			
	</div>		
	<div id="bft-sidebar">
				<?php require(BFT_PATH."/views/sidebar.html.php");?>
	</div>	  
</div>			