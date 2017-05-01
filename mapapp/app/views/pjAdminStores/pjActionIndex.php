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
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionIndex"><?php __('menuStores'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionCreate"><?php __('lblAddStore'); ?></a></li>
			<?php
			if($controller->isAdmin())
			{ 
				?>
					<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionImport"><?php __('lblImportStore'); ?></a></li>
				<?php
			} 
			?>
		</ul>
	</div>
	
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<?php
		$filter = __('filter', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all">All</a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="T"><?php echo $filter['active']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="F"><?php echo $filter['inactive']; ?></a>
		</div>
		<br class="clear_both" />
	</div>

	<div id="grid"></div>
	
	<script type="text/javascript">
		var pjGrid = pjGrid || {};
		pjGrid.queryString = "";
		pjGrid.roleId = <?php echo (int) $_SESSION[$controller->defaultUser]['role_id']; ?>;
		<?php
		if (isset($_GET['category_id']) && (int) $_GET['category_id'] > 0)
		{
			?>pjGrid.queryString += "&category_id=<?php echo (int) $_GET['category_id']; ?>";<?php
		}
		?>
		var myLabel = myLabel || {};
		myLabel.store = "<?php __('lblStore'); ?>";
		myLabel.category = "<?php __('lblCategory'); ?>";
		myLabel.active = "<?php __('lblActive'); ?>";
		myLabel.inactive = "<?php __('lblInactive'); ?>";
		myLabel.exported = "<?php __('lblExport'); ?>";
        myLabel.export_all = "Export All";
		myLabel.revert_status = "<?php __('lblRevertStatus'); ?>";
		myLabel.delete_selected = "<?php __('lblDeleteSelected'); ?>";
		myLabel.delete_confirmation = "<?php __('lblDeleteConfirmation'); ?>";
		myLabel.status = "<?php __('lblStatus'); ?>";
	</script>
	<?php
}
?>