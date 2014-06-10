<div class="wrap">
	<div class="postbox-container" style="width:73%;margin-right:2%;">	
    <h1><?php _e('Manage Your Mailing List', 'broadfast')?></h1>

    <?php if($error):?>
        <p class="error"><?php echo $err_msg;?></p>
    <?php endif;?>

    <form method="post" style="margin:none;" onsubmit="return validateUser(this);">
    <div style="clear:both;float:left;">
        <div style="float:left;"><?php _e('Email:', 'broadfast')?> <input type="text" name="email"> 
        &nbsp; <?php _e('Name:', 'broadfast')?> <input type="text" name="user_name"> 
        &nbsp; <input type="checkbox" name="status" value="1" checked="checked"> <?php _e('Active', 'broadfast')?> 
        <input type="submit" value="<?php _e('Add User', 'broadfast')?>"></div>
    </div>
    <input type="hidden" name="add_user" value="1">
    </form>
    
    <p>&nbsp;</p>
    <p>&nbsp;</p>
	<?php if(sizeof($users)):?>	
		<table class="widefat">
			<tr><th><input type="checkbox" onclick="this.checked ? jQuery('.subscriber_chk').attr('checked', 'true') : jQuery('.subscriber_chk').removeAttr('checked');showHideDelSubs();"></th><th width="30%"><a href="admin.php?page=bft_list&ob=email"><?php _e('User Email Address', 'broadfast')?></a></th>
			<th width="27%"><a href="admin.php?page=bft_list&ob=name"><?php _e('User Name', 'broadfast')?></a></th>
			<th width="9%"><a href="admin.php?page=bft_list&ob=ip"><?php _e('IP Address', 'broadfast')?></a></th>
			<th width="15%"><a href="admin.php?page=bft_list&ob=date"><?php _e('Date Signed', 'broadfast')?></a></th>
			<th width="5%"><a href="admin.php?page=bft_list&ob=status,email"><?php _e('Active?', 'broadfast')?></a></th>
			<th width="16%"><?php _e('Action', 'broadfast')?></th></tr>
		</table>
	<?php else: ?>
		<p><?php _e('This mailing list is empty.', 'broadfast')?></p>
	<?php endif;?>	
		
    <?php foreach($users as $user): ?>
    
    <form method="post" style="margin:none;" onsubmit="return validateUser(this);">
    <table class="widefat">
	    <tr><td><input type="checkbox" value="<?php echo $user->id?>" class="subscriber_chk" onclick="showHideDelSubs();"></td>
	    <td width="23%"><input type="text" name="email" value="<?php echo $user->email?>"></td>
	    <td width="23%"><input type="text" name="user_name" value="<?php echo $user->name?>"></td>
	    <td width="9%"><?php echo $user->ip?></td>
	    <td width="15%"><?php echo $user->date?></td>
	    <td width="9%" align="center"><input type="checkbox" name="status" value="1" <?if($user->status) echo "checked"?>></td>
	    <td width="21%"><input type="submit" name="save_user" value="<?php _e('Save User', 'broadfast')?>">	
      <input type="button" value="<?php _e('Delete', 'broadfast')?>" onclick="delUser(this.form);"></td></tr>
    </table> 
    <input type="hidden" name="del_user" value="0">
    <input type="hidden" name="id" value="<?php echo $user->id?>">
    </form>
    <?php endforeach; ?>
   
    
    <p align="center">
    	<?php if($offset > 0):?><a href="admin.php?page=bft_list&ob=<?php echo $ob?>&offset=<?php echo $offset-$per_page?>"><?php _e('previous page')?></a> &nbsp;<?php endif;?>
    	<?php if($offset + $per_page < $count):?><a href="admin.php?page=bft_list&ob=<?php echo $ob?>&offset=<?php echo $offset+$per_page?>"><?php _e('next page')?></a> &nbsp;<?php endif;?>
    </p>
    
    <div id="massSubscribersAction" style="display:none;">
    	<form method="post" action="admin.php?page=bft_list&ob=<?php echo $ob?>&offset=<?php echo $offset?>">
    	<p align="center"><input type="hidden" name="mass_delete" value="0">
    	<input type="button" value="<?php _e('Delete Selected Subscribers', 'broadfast')?>" onclick="bftMassDelete(this.form);"></p> 
    	<input type="hidden" name="del_ids" value="0">
    	</form>
    </div>
  </div>
	<div id="bft-sidebar">
				<?php require(BFT_PATH."/views/sidebar.html.php");?>
	</div>	  
</div>

<script language="javascript">
function delUser(frm) {
	if(confirm("<?php _e('Are you sure?', 'broadfast')?>")) {
		frm.del_user.value=1;
		frm.submit();
	}
}

function validateUser(frm) {
	if(frm.email.value=="") {
		alert("<?php _e('Please enter email', 'broadfast')?>");
		frm.email.focus();
		return false;
	}	
	return true;
}

// show or hide the mass-action button(s)
function showHideDelSubs() {
	var len = jQuery(".subscriber_chk:checked").length;
	
	if(len) jQuery('#massSubscribersAction').show();
	else jQuery('#massSubscribersAction').hide();
}

function bftMassDelete(frm) {
	if(confirm("<?php _e('Are you sure?', 'broadfast')?>")) {
		jQuery(".subscriber_chk:checked").each(function(){
			frm.del_ids.value += "," + jQuery(this).val();
		})
		frm.mass_delete.value = 1;
		frm.submit();
	}
}
</script>