<form method="post" onsubmit="return BFTValidate(this);">
<p><label><?php _e('Name:', 'broadfast')?></label>&nbsp;<input type="text" name="user_name"></p>
<p><label><?php _e('Email:', 'broadfast')?></label>&nbsp;<input type="text" name="email"></p>
<p align="center"><input type="submit" value="Register"></p>
<input type="hidden" name="bft" value="register">
</form>

<script type="text/javascript" >
function BFTValidate(frm) {
	var email = frm.email.value;
	if(email=="" || email.indexOf("@")<1 ||email.indexOf(".")<1) {
		alert("<?php _e('Please fill in valid email address!', 'broadfast')?>");
		frm.email.focus();
		return false;
	}
	
	return true;
}
</script>