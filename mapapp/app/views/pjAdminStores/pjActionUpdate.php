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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionIndex"><?php __('menuStores'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionCreate"><?php __('lblAddStore'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('lblUpdateStore'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStores&amp;action=pjActionUpdate" method="post" id="frmUpdateStore" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="store_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<p>
			<label class="title"><?php __('lblStoreName'); ?></label>
			<span class="inline_block">
				<input type="text" name="name" id="name" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['name'])); ?>" class="pj-form-field w350 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblCategory'); ?></label>
			<span class="inline_block">
				<select id="category_id" name="category_id[]" class="pj-form-field w300" data-placeholder="--<?php __('lblChoose'); ?>--" multiple="multiple">
					<?php
					foreach ($tpl['category_arr'] as $k => $v)
					{
						?><option value="<?php echo $v['id']; ?>" <?php echo in_array($v['id'], $tpl['category_id_arr']) ? 'selected="selected"' : null; ?>><?php echo $v['category_title']; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblEmail'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="email" id="email" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['email'])); ?>" class="pj-form-field w250 email" placeholder="info@domain.com" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblWebsite'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-url"></abbr></span>
				<input type="text" name="website" id="website" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['website'])); ?>" class="pj-form-field w300 url" placeholder="http://www.domain.com"  />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblPhone'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
				<input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['phone'])); ?>" class="pj-form-field w150" placeholder="(123) 456-7890" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblOpeningTimes'); ?></label>
			<span class="inline_block">
				<textarea id="opening_times" name="opening_times" class="textarea h100 w400"><?php echo htmlspecialchars(stripslashes($tpl['arr']['opening_times'])); ?></textarea>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblImage'); ?></label>
			<span class="inline_block">
				<input id="image" name="image" type="file"/>
			</span>
		</p>
		<?php
		if(!empty($tpl['arr']['image_path']))
		{ 
			$image_path = PJ_INSTALL_PATH . $tpl['arr']['image_path'];
			if(file_exists($image_path))
			{
				?>
				<p id="image_container" class="image-container">
					<label class="title">&nbsp;</label>
					<span class="block overflow">
						<img src="<?php echo PJ_INSTALL_URL . $tpl['arr']['image_path']; ?>"/>
						<a href="#" class="icon-delete" rev="<?php echo $tpl['arr']['id']; ?>"><?php echo strtolower(__('lblDelete', true));?></a>
					</span>
				</p>
				<?php
			}
		} 
		?>
		<p>
			<label class="title"><?php __('lblCountry'); ?></label>
			<span class="inline_block">
				<select id="country_id" name="country_id" class="pj-form-field w350">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach ($tpl['country_arr'] as $k => $v)
					{
						?><option value="<?php echo $v['id']; ?>" <?php echo $v['id'] == $tpl['arr']['country_id'] ? 'selected="selected"' : null; ?>><?php echo $v['country_title']; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblState'); ?></label>
			<span class="inline_block">
				<input type="text" name="address_state" id="address_state" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['address_state'])); ?>" class="pj-form-field w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblCity'); ?></label>
			<span class="inline_block">
				<input type="text" name="address_city" id="address_city" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['address_city'])); ?>" class="pj-form-field w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblAddress'); ?></label>
			<span class="inline_block">
				<textarea id="address_content" name="address_content" class="textarea h80 w450"><?php echo htmlspecialchars(stripslashes($tpl['arr']['address_content'])); ?></textarea>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblZip'); ?></label>
			<span class="inline_block">
				<input type="text" name="address_zip" id="address_zip" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['address_zip'])); ?>" class="pj-form-field w150" />
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<span><?php __('lblGMapNote'); ?></span>
		</p>
		<div class="left-content">
			<p>
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<input type="button" value="<?php __('btnGoogleMapsApi'); ?>" class="pj-button btnGoogleMapsApi" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblLatitude'); ?></label>
				<input type="text" name="lat" id="lat" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['lat'])); ?>" class="pj-form-field w200 number" />
			</p>
			<p>
				<label class="title"><?php __('lblLongitude'); ?></label>
				<input type="text" name="lng" id="lng" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['lng'])); ?>" class="pj-form-field w200 number" />
			</p>
			<p>
				<label class="title"><?php __('lblStatus'); ?></label>
				<span class="inline_block">
					<select name="status" id="status" class="pj-form-field required">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach (__('u_statarr', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>" <?php echo $k == $tpl['arr']['status'] ? 'selected="selected"' : null; ?>><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			</p>
		</div>
		<div class="right-content">
			<span id="map-message"></span>
			<div id="map_canvas" class="map-canvas"></div>
		</div>
	</form>
	
	<div id="dialogDeleteImage" title="<?php __('lblDeleteImageTitle'); ?>" style="display:none;">
		<div class="t15">
			<?php __('lblDeleteImageConfirmation'); ?>
			<input type="hidden" id="record_id" name="record_id" value="" />
		</div>
	</div>
	
	<script type="text/javascript">
		var myLabel = myLabel || {};
		myLabel.address_not_found = "<?php __('lblAddressNotFound'); ?>";
		myLabel.allowed_extension = "<?php echo __('lblAllowedExtension', true); ?>";
	</script>
	<?php
}
?>