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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionIndex"><?php __('menuStores'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionCreate"><?php __('lblAddStore'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionImport"><?php __('lblImportStore'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoImportTitle', true), __('infoImportBody', true));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionImport" method="post" id="frmImportStore" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="store_import" value="1" />
		<p>
			<label class="title"><?php __('lblCSVFile'); ?></label>
			<span class="inline_block">
				<input id="csv" name="csv" type="file" class="required"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblWithLatLng'); ?></label>
			<span class="inline_block">
				<input id="with_lat_lng" name="with_lat_lng" type="checkbox" class="t5"/>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnImport'); ?>" class="pj-button" />
		</p>
	</form>
	<?php
}
?>