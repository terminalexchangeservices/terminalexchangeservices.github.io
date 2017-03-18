<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionIndex"><?php __('menuCategories'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionCreate"><?php __('lblAddCategory'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('lblUpdateCategory'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionUpdate" method="post" id="frmUpdateCategory" class="form pj-form" enctype="multipart/form-data">
		<input type="hidden" name="category_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<p>
			<label class="title"><?php __('menuStores'); ?></label>
			<label class="content">
				<?php
				if($tpl['arr']['cnt_stores'] > 0)
				{ 
					?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionIndex&category_id=<?php echo $tpl['arr']['id'];?>"><?php echo $tpl['arr']['cnt_stores'];?></a><?php
				}else{
					echo $tpl['arr']['cnt_stores'];
				} 
				?>
			</label>
		</p>
		
		<p>
			<label class="title"><?php __('lblCategory'); ?></label>
			<span class="inline_block">
				<input type="text" name="category_title" id="category_title" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['category_title'])); ?>" class="pj-form-field w300 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblGoogleMarker'); ?></label>
			<span class="inline_block">
				<input id="marker" name="marker" type="file"/>
			</span>
		</p>
		<?php
		if(!empty($tpl['arr']['marker']))
		{ 
			$marker_path = PJ_INSTALL_PATH . $tpl['arr']['marker'];
			if(file_exists($marker_path))
			{
				?>
				<p id="marker_container" class="marker-container">
					<label class="title">&nbsp;</label>
					<span class="block overflow">
						<img src="<?php echo PJ_INSTALL_URL . $tpl['arr']['marker']; ?>"/>
						<a href="#" class="icon-delete" rev="<?php echo $tpl['arr']['id']; ?>"><?php echo strtolower(__('lblDelete', true));?></a>
					</span>
				</p>
				<?php
			}
		} 
		?>
		<p>
			<label class="title"><?php __('lblStatus'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach (__('u_statarr', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['status'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		</p>
	</form>
	
	<div id="dialogDeleteMarker" title="<?php __('lblDeleteMarkerTitle'); ?>" style="display:none;">
		<div class="t15">
			<?php __('lblDeleteMarkerConfirmation'); ?>
			<input type="hidden" id="record_id" name="record_id" value="" />
		</div>
	</div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.same_category = "<?php echo __('lblSameCategory', true); ?>";
	myLabel.allowed_extension = "<?php echo __('lblAllowedExtension', true); ?>";
	</script>
	<?php
}
?>