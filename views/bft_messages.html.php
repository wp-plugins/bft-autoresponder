<div class="wrap">

	<div class="postbox-container" style="width:73%;margin-right:2%;">	
	
	<p><b><?php _e('Note: you can use the variable {{name}} in any message to address the user by name.', 'broadfast')?></b></p>

		<h2><?php _e('Create New Message:', 'broadfast')?></h2>
		
		<form method="post" style="margin:none;" onsubmit="return validateMessage(this);">
		<div style="clear:both;float:left;">
			<div style="float:left;">
			<p><label><?php _e('Subject:', 'broadfast')?>&nbsp; </label> <input type="text" name="subject" size="80"></p>
			<p><label><?php _e('Message:', 'broadfast')?> </label> <?php wp_editor("", "message")?></p>
			<p><label><?php _e('Days after registration:', 'broadfast')?></label> <input type="text" name="days" size="4">
		    <?php _e('or', 'broadfast')?> <input type="checkbox" name="send_on_date" value="1" onclick="sendOnDate(this);"> <?php _e('send on', 'broadfast')?> <?php echo BFTquickDD_date("date", NULL, "YYYY-MM-DD", NULL, date("Y"),date("Y")+10);?></p>
			<p><input type="submit" value="<?php _e('Add Message', 'broadfast')?>"></p></div>
		</div>
		<input type="hidden" name="add_message" value="1">
		</form>
		
		<div style="clear:both;">&nbsp;</div>
		
		<h2><?php _e('Existing Messages:', 'broadfast')?></h2>
		<?php foreach($mails as $mail): ?>
		<form method="post" style="margin:none;" onsubmit="return validateMessage(this);">
		<div style="clear:both;float:left;">
		<hr />
			<div style="float:left;">
			<p><label><?php _e('Subject:', 'broadfast')?>&nbsp; </label> <input type="text" name="subject" value="<?php echo stripslashes($mail->subject)?>" size="80"></p>
			<p><label><?php _e('Message:', 'broadfast')?> </label> <?php wp_editor(stripslashes($mail->message), "message".$mail->id, array("textarea_name"=>"message"))?></p>
			<p><label><?php _e('Days after registration:', 'broadfast')?></label> 
			<input type="text" name="days" size="4" value="<?php echo $mail->days?>" <?php if($mail->send_on_date) echo "disabled"?>>
		    <?php _e('or', 'broadfast')?> <input type="checkbox" name="send_on_date" value="1" onclick="sendOnDate(this);" <?php if($mail->send_on_date) echo "checked"?>> <?php _e('send on', 'broadfast')?> <?php echo BFTquickDD_date("date", $mail->date, "YYYY-MM-DD", NULL, date("Y"),date("Y")+10);?></p>
			<p><input type="submit" name="save_message" value="<?php _e('Save Message', 'broadfast')?>">	
			<input type="button" value="<?php _e('Delete', 'broadfast')?>" onclick="delMessage(this.form);"></p></div>
		</div>
		<input type="hidden" name="id" value="<?php echo $mail->id?>">
		<input type="hidden" name="del_message" value="0">
		</form>
		<?php endforeach; ?>
		
</div>		
	<div id="bft-sidebar">
				<?php require(BFT_PATH."/views/sidebar.html.php");?>
	</div>	  
</div>					

<script language="javascript">
function delMessage(frm)
{
	if(confirm("<?php _e('Are you sure?', 'broadfast')?>"))
	{
		frm.del_message.value=1;
		frm.submit();
	}
}

function validateMessage(frm)
{
	if(frm.subject.value=="")
	{
		alert("<?php _e('Please enter subject', 'broadfast')?>");
		frm.subject.focus();
		return false;
	}

	if(isNaN(frm.days.value))
	{
		alert("<?php _e('Please enter number of days after registration', 'broadfast')?>");
		frm.days.focus();
		return false;
	}
	
	return true;
}

// when checked turns the days to disabled and vice versa
function sendOnDate(chk)
{
    if(chk.checked)
    {
        chk.form['days'].disabled=true;
    }
    else
    {
        chk.form['days'].disabled=false;
    }
}
</script>