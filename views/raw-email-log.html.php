<div class="wrap">
	<h1><?php _e('Email Log', 'broadfast')?></h1>
	
	<div class="postbox-container" style="width:73%;margin-right:2%;">
		<p><?php _e('This is a raw email log showing you all emails sent from the autoresponder. It incldues activation, double opt-in emails, drip marketing emails, newsletters, etc. A lot more specific logs are available in the PRO version.', 'broadfast')?></p>
		
		<form method="post">
			<p><label><?php _e('Log date:', 'broadfast')?></label> <input type="text" name="date" class="datepicker" value="<?php echo $date?>">
			<input type="submit" value="<?php _e('Show log', 'broadfast')?>"></p>
		</form>		
		
		<?php if(!sizeof($emails)):?>
			<p><?php _e('No emails have been sent on the selected date.', 'broadfast')?></p>
		<?php else:?>
			<table class="widefat">
				<tr><th><?php _e('Time', 'broadfast')?></th><th><?php _e('Sender', 'broadfast')?></th><th><?php _e('Receiver', 'broadfast')?></th>
<th><?php _e('Subject', 'broadfast')?></th><th><?php _e('Status', 'broadfast')?></th></tr>
				<?php foreach($emails as $email):
					$class = ('alternate' == @$class) ? '' : 'alternate';?>
					<tr class="<?php echo $class?>"><td><?php echo date('H:i', strtotime($email->datetime))?></td>
					<td><?php echo stripslashes($email->sender)?></td>
					<td><?php echo stripslashes($email->receiver)?></td>
					<td><?php echo stripslashes($email->subject)?></td>
					<td><?php echo $email->status?></td></tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
	
	</div>		
	<div id="bft-sidebar">
				<?php require(BFT_PATH."/views/sidebar.html.php");?>
	</div>	  
</div>	

<script type="text/javascript" >
jQuery(function(){
	jQuery('.datepicker').datepicker({dateFormat: "yy-m-d"});
});
</script>