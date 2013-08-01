<div class="wrap">

	<div class="postbox-container" style="width:73%;margin-right:2%;">	
	
		<?php if(isset($_POST['import'])) echo $success_text; ?>
		
		<h2><?php _e('Import Members from CSV File', 'broadfast')?></h2>
		
		<form method="post" enctype="multipart/form-data" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<p><label><?php _e('Fields Delimiter:', 'broadfast')?></label> <input type="text" name="delim" value="," size="3"> <?php _e('(enter <b>\t</b> for tabulator)', 'broadfast')?></p>
		<p><label><?php _e('Email column is number:', 'broadfast')?></label> <input type="text" name="email_column" value="1" size="3"> <?php _e('in the CSV', 'broadfast')?></p>
		<p><label><?php _e('Name column is number:', 'broadfast')?></label> <input type="text" name="name_column" value="2" size="3"> <?php _e('in the CSV', 'broadfast')?></p>
		<p><label><?php _e('Upload CSV:', 'broadfast')?></label> <input type="file" name="file"></p>
		<p><input type="submit" name="import" value="<?php _e('Import CSV File', 'broadfast')?>"></p>
		</form>
		
		<br>
		
		<h2><?php _e('Export Members to CSV File', 'broadfast')?></h2>
		
		<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<p><input type="checkbox" checked="true"> <?php _e('Export only confirmed members', 'broadfast')?></p>
		<p><input type="submit" name="export" value="<?php _e('Export members', 'broadfast')?>"></p>
		</form>
		
		<?php if(isset($_POST['export'])):?>
			<div style="float:left;width:620px;margin-left:5%">    
		      <textarea style="width:600px;height:440px;"><?php echo $content; ?></textarea>    
			</div>
		<?php endif; ?>
	</div>
	<div id="bft-sidebar">
				<?php require(BFT_PATH."/views/sidebar.html.php");?>
	</div>	
</div>	