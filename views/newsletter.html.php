<div class="wrap">

	<div class="postbox-container" style="width:73%;margin-right:2%;">	
		<h2><?php _e('Send Instant Newsletter', 'broadfast')?></h2>
		
		<?php if(!empty($_POST['ok'])):?>
			<p><b><?php printf(__('%d emails were sent.', 'broadfast'), $num_mails_sent)?></b></p>
		<?php endif;?>
		
		<form  method="post" action="#" onsubmit="return BFTValidateNewsletter(this);">
		<div><label><?php _e('Newsletter Subject:', 'broadfast')?></label> <input type="text" name="subject" size="60" value="<?php echo stripslashes(@$_POST['subject'])?>"></div>
		
		<div><label><?php _e('Newsletter contents:', 'broadfast')?></label> <?php echo wp_editor(stripslashes(@$_POST['message']), 'message')?></div>
		
		<p class="bft-warning"><b><?php _e("Important!", 'broadfast')?></b> <?php _e('This newsletter will be sent immediately to all your confirmed subscribers. If you have a large mailing lists (anything over 500 subscribers) this might be a problem for your server. So please double-check if you can send many emails at once. If your mailing list is small you have nothing to worry about.', 'broadfast')?></p>
		
		<p><?php _e('Our <a href="http://calendarscripts.info/bft-pro/" target="_blank">PRO Version</a> offers delayed sending of newsletters and much better flexibility over the number of emails you send at once and per day. You can also see and reuse old newsletters, customize them with dynamic tags, see reports and a lot more', 'broadfast')?></p>
			
		<div><p><input type="submit" value="<?php _e('Send Newsletter', 'broadfast')?>"></p></div>	
		<input type="hidden" name="ok" value="1">
		</form>
	</div>		
	<div id="bft-sidebar">
				<?php require(BFT_PATH."/views/sidebar.html.php");?>
	</div>	  
</div>		

<script type="text/javascript" >
function BFTValidateNewsletter(frm) {
	if(frm.subject.value == '') {
		alert("<?php _e('Please enter subject', 'broadfast');?>");
		frm.subject.focus();
		return false;
	}
	
	return true;
}
</script>