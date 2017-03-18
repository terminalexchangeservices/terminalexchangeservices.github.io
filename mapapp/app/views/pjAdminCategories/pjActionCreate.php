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
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionCreate"><?php __('lblAddCategory'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoAddCategoryTitle', true), __('infoAddCategoryBody', true)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionCreate" method="post" id="frmCreateCategory" class="form pj-form" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="category_create" value="1" />
		<p>
			<label class="title"><?php __('lblCategory'); ?></label>
			<span class="inline_block">
				<input type="text" name="category_title" id="category_title" class="pj-form-field w300 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblGoogleMarker'); ?></label>
			<span class="inline_block">
				<input id="marker" name="marker" type="file"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblStatus'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach (__('u_statarr', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
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
	<script type="text/javascript">
		var myLabel = myLabel || {};
		myLabel.same_category = "<?php echo __('lblSameCategory', true); ?>";
		myLabel.allowed_extension = "<?php echo __('lblAllowedExtension', true); ?>";
	</script>
	<?php
}
?>