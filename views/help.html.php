<div class="wrap">

	<div class="postbox-container" style="width:73%;margin-right:2%;">
	<h2><?php _e('User Manual', 'broadfast')?></h2>
			
			<p><?php _e('This is a short introduction to BFT Lite. It is very simple and easy to use. For more advanced features you can check our <a href="http://calendarscripts.info/bft-pro">PRO version</a>. You can copy all your data with a single click.', 'broadfast')?></p>
			
			
			<h3><?php _e('Automated autoresponder emails', 'broadfast')?></h3>
			<p><?php _e('In order to send automated sequential emails every day your blog should be visited at least once daily. If this is not happening, please set up a cron job to visit it.', 'broadfast')?></p>
			 
			<h3><?php _e('General Settings', 'broadfast')?></h3>
			<p><?php _e('This is the current page. Here you can set several important settings:', 'broadfast')?></p>
			<p><ul>
			   <li><?php _e('Sender of all emails. This is the "From" email address that will be used to send your autoresponder messages.', 'broadfast')?></li>
			   <li><?php _e('URL after registration. This setting allows you to redirect the user to a specific page (some sales page, help etc) after they sign up for the autoresponder. You can leave it blank.', 'broadfast')?></li>
			   <li><?php _e('Double opt in. When "yes" is selected, the subscribers to your list will be sent an email with confirmation link and will be activated only when this link is clicked.', 'broadfast')?></li>
			   <li><?php _e('Registration form HTML code. Copy this code and paste it anywhere - in a blog post or page (in HTML mode), in the sidebar or even in other website. The code is non-editable here on the page - if you want to edit the appearance, do that on the code that you have copied.', 'broadfast')?></li>
			</ul></p>
			
			<h3><?php _e('Mailing List', 'broadfast')?></h3>
			<p><?php _e('Manage your mailing list on this page. You can add, edit and delete subscribers and activate/deactivate them. When you add a subscriber as active, she will receive the welcome mail if you have set such (more about welcome mails in "Email Messages" section).', 'broadfast')?></p>
			
			<h3><?php _e('Import/Export Members', 'broadfast')?></h3>
			<p><?php _e('In addition to adding members manually, you can import them in batches from a CSV file. BFT autoresponder allows you to import CSV file with any number and order of columns - you only need to specify which column number (from left to right) in the CSV contains user email and which one - user name.', 'broadfast')?></p>
			 
			<p><?php _e('When importing members they will automatically be activated. If there are any welcome mails set, those will be sent out. Importing a lot of members at once and having a welcome mail scheduled at the same time may slow down your site.', 'broadfast')?></p>
			
			<h3><?php _e('Email Messages', 'broadfast')?></h3>
			<p><?php _e('Use this page to add, edit and delete autoresponder mails. In "Days after registration" enter a number which will represent how many days after user registration the email will be sent to the user. There should be only one email scheduled for each number of days.', 'broadfast')?></p>
			
			<p><?php _e('To create a <b>Welcome mail</b> just enter "0" in "Days after registration" field. Such emails will be sent out immediately when the user is imported, added or registers (when double opt-in is "yes" the last case to be read "when the user confirms").', 'broadfast')?></p>
			
			<h3><?php _e('Customization of the messages', 'broadfast')?></h3>
			<p><?php _e("You can use the tags <b>{{email}}</b> and <b>{{name}}</b> both in the subject and the contents of your messages. They will be replaced with user's email and name.", 'broadfast')?></p>
			
			<h3><?php _e('Using SMTP', 'broadfast')?></h3>
			<p><?php _e('This auto responder uses the standard Wordpress "wp_mail" function. This means you can easily send your emails by SMTP if you install a plugin like <a href="http://wordpress.org/extend/plugins/wp-mail-smtp/" target="_blank">WP Mail SMTP</a>.', 'broadfast')?></p>
			
			<h3><?php _e('Localization / translation', 'broadfast')?></h3>
			
			<p><?php printf(__('To localize the plugin in your language check <a href="%s" target="_blank">this guide</a>. Note that our plugin textdomain is "broadfast" and the .po and .mo files should go into folder languages/', 'broadfast'), "http://blog.calendarscripts.info/how-to-translate-a-wordpress-plugin/")?></p>
	</div>		
			
	<div id="bft-sidebar">
				<?php require(BFT_PATH."/views/sidebar.html.php");?>
	</div>	  
</div>			