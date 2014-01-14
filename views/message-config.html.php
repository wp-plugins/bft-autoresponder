<div class="wrap">
	<h1><?php _e('Configure Email Notification Message', 'broadfast');?></h1>
	
	<p><?php _e('Message type:', 'broadfast')?> <b><?php echo $friendly_type;?></b></p>
	
	<p><?php _e("If you don't configure this message, a default message will be used.", 'broadfast')?></p>
	
	<form method="post" onsubmit="return validateBFConfigForm(this);">
		<p><label><?php _e('Subject:', 'broadfast')?></label> <input type="text" size="50" name="subject" value="<?php echo stripslashes($subject)?>"></p>
		<p><label><?php _e('Message:', 'broadfast')?></label> <?php echo wp_editor(stripslashes($message), 'message')?></p>
		<p><?php _e('Inside the message you can use the following variables:', 'broadfast')?> {{{blog-name}}}, {{{subscriber-name}}}, {{{subscriber-email}}}, {{{date}}}</p>
		<p align="center"><input type="submit" value="<?php _e('Save Message', 'broadfast')?>">
		<input type="button" value="<?php _e('Go Back', 'broadfast')?>" onclick="window.location='admin.php?page=bft_options';"></p>
		<input type="hidden" name="ok" value="1">
	</form>
</div>

<script type="text/javascript" >
function validateBFConfigForm(frm) {
	if(frm.subject.value == '') {
		alert("<?php _e('The subject cannot be empty.', 'broadfast');?>");
		frm.subject.focus();
		return false;
	}
	return true;
}
</script>