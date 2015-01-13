<div class="wrap">

	<div class="postbox-container" style="width:73%;margin-right:2%;">	
		<h2><?php _e('Send Instant Newsletter', 'broadfast')?></h2>
		
		<?php if(!empty($_SESSION['bft_flash'])):?>
			<p><b><?php echo $_SESSION['bft_flash'];
			unset($_SESSION['bft_flash']);?></b></p>
		<?php endif;?>
		
		<form  method="post" action="#" onsubmit="return BFTValidateNewsletter(this);">
		<div><label><?php _e('Newsletter Subject:', 'broadfast')?></label> <input type="text" name="subject" size="60" value="<?php echo stripslashes(@$nl->subject)?>"></div>
		
		<div><label><?php _e('Newsletter contents:', 'broadfast')?></label> <?php echo wp_editor(stripslashes(@$nl->message), 'message')?></div>
		
		<p><label><?php _e("Email type:", 'broadfast')?></label> <select name="content_type">
				<option value="text/html"><?php _e("HTML", 'broadfast');?></option>
				<option value="text/plain"><?php _e("Text", 'broadfast');?></option>
				</select>
			</p>	
		
		<p class="bft-warning"><b><?php _e("Important!", 'broadfast')?></b> <?php _e('This newsletter will be sent immediately to all your confirmed subscribers. If you have a large mailing lists (anything over 500 subscribers) this might be a problem for your server. So please double-check if you can send many emails at once. If your mailing list is small you have nothing to worry about.', 'broadfast')?></p>
		
		<p><?php _e('Our <a href="http://calendarscripts.info/bft-pro/" target="_blank">PRO Version</a> offers delayed sending of newsletters and much better flexibility over the number of emails you send at once and per day. You can also see and reuse old newsletters, customize them with dynamic tags, see reports and a lot more', 'broadfast')?></p>
			
		<div><p><input type="submit" value="<?php _e('Send Newsletter', 'broadfast')?>"></p></div>	
		<input type="hidden" name="ok" value="1">
		</form>
		
		
		<?php if(sizeof($newsletters)):?>
			<h2><?php _e('Previous newsletters', 'broadfast')?></h2>

			<?php if(!empty($_GET['id'])):?>
				<p><a href="admin.php?page=bft_newsletter"><?php _e('Create a new newsletter', 'broadfast')?></a></p>
			<?php endif;?>			
			<table class="widefat">
				<tr><th><?php _e('Subject', 'broadfast')?></th><th><?php _e('Sent on', 'broadfast')?></th>
				<th><?php _e('Receivers', 'broadfast')?></th><th><?php _e('Edit/Delete', 'broadfast')?></th></tr>
				<?php foreach($newsletters as $newsletter):
					$class = ('alternate' == @$class) ? '' : 'alternate';?>
					<tr class="<?php echo $class?>"><td><?php echo stripslashes($newsletter->subject);?></td>
					<td><?php echo date($dateformat, strtotime($newsletter->date))?></td>
					<td><?php echo $newsletter->num_sent;?></td>
					<td><a href="admin.php?page=bft_newsletter&id=<?php echo $newsletter->id?>"><?php _e('Edit', 'broadfast')?></a>
					| <a href="#" onclick="BFTDelNewsletter(<?php echo $newsletter->id?>);return false;"><?php _e('Delete', 'broadfast')?></a></td></tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
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

function BFTDelNewsletter(id) {
	if(confirm("<?php _e('Are you sure?', 'broadfast')?>")) {
		window.location = 'admin.php?page=bft_newsletter&del=' + id;
	}
}
</script>