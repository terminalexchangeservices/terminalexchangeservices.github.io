<?php
$distances = __('distances', true);
ksort($distances);
?>
<div class="stl-store-container">
	<div class="stl-search-container">
		<form action="" method="post" name="stl_seach_form" class="stl-form" onsubmit="return false;">
			<?php
			if($tpl['option_arr']['o_use_categories'] == 'Yes')
			{
				?>
				<p class="stl-float-left stl-r15">
					<label class="title"><?php __('front_label_category'); ?></label>
					<select name="category_id" class="stl-select stl-w208">
						<option value="">--<?php __('front_label_choose'); ?>--</option>
						<?php
						foreach ($tpl['category_arr'] as $k => $v)
						{
							?><option value="<?php echo $v['id']; ?>"><?php echo $v['category_title']; ?></option><?php
						}
						?>
					</select>
				</p>
				<?php
			}
			?>
			<p class="stl-float-left stl-r15">
				<label class="title"><?php __('front_label_address'); ?></label>
				<input name="address" value="<?php echo $tpl['option_arr']['o_default_address'];?>" class="stl-text stl-w208" />
			</p>
			<p class="stl-float-left">
				<label class="title"><?php __('front_label_within'); ?></label>
				<select name="radius" class="stl-select stl-w100 stl-r5">
					<?php
					foreach ($distances as $k => $v)
					{
						?><option value="<?php echo $k; ?>" <?php echo $k == 25 ? 'selected="selected"' : null;?>><?php echo $k; ?></option><?php
					}
					?>
				</select>
				<label class="stl-distance-legend"><?php echo $tpl['option_arr']['o_distance'];?></label>
			</p>
			<p class="stl-float-right">
				<input type="button" value="<?php __('front_button_search'); ?>" name="stl_search_form_search" class="stl-button" />
			</p>
		</form>
	</div>
	<div id="stl_store_canvas" class="stl-map-container"></div>
	<div id="stl_search_result" class="stl-search-result">
		<div id="stl_search_addresses"></div>
	</div>
	<div id="stl_search_directions" class="stl-search-directions" style="display: none">
		<div class="stl-directions-menu">
			<a href="#" class="stl-directions-close"><?php __('front_label_close');?></a>
			<a href="#" id="stl_email_menu" class="stl-directions-email">Email</a>
		</div>
		<div class="stl-search-directions-panel">
			<div id="stl_directions_email" class="stl-directions-email">
				<form action="" method="post" id="stl_send_email_form" name="stl_send_email_form" class="stl-form" onsubmit="return false;">
					<p>
						<label class="title30">Email</label>
						<input name="stl_email_text" class="stl-email-text stl-text stl-w250" />
						<input type="button" id="stl_send_email" name="stl_send_email" value="Send" class="stl-button stl-go-button" />
						<textarea id="stl_directions_html" name="stl_directions_html" class="stl-direction-html"></textarea>
					</p>
				</form>
			</div>
			<div id="stl_search_directions_panel"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var stivaSTLObj = new stivaSTL({
		zoom_level: <?php echo $tpl['option_arr']['o_zoom_level']; ?>,
		default_address: "<?php echo $tpl['option_arr']['o_default_address']; ?>",
		distance: "<?php echo $tpl['option_arr']['o_distance']; ?>",
		use_categories: "<?php echo $tpl['option_arr']['o_use_categories']; ?>",

		search_form_name: "stl_seach_form",
		search_form_address_name: "stl_seach_form_add",
		search_form_search_name: "stl_search_form_search",
		search_form_address: "address",

		label_opening_time: "<?php __('front_label_opening_times');?>",
		label_full_address: "<?php __('front_label_full_address');?>",
		label_directions: "<?php __('front_label_directions');?>",
		label_close: "<?php __('front_label_close');?>",
		label_from: "<?php __('front_label_from');?>",
		label_address: "<?php __('front_label_address');?>",
		label_go: "<?php __('front_label_go');?>",
		label_phone: "<?php __('front_label_phone');?>",
		label_email: "<?php __('front_label_email');?>",
		label_website: "<?php __('front_label_website');?>",
		label_not_found: "<?php __('front_label_not_found');?>",
		label_sent: "<?php __('front_label_sent');?>",
		label_empty_email: "<?php __('front_label_empty_email');?>",
		label_invalid_email: "<?php __('front_label_invalid_email');?>",

		install_url: "<?php echo PJ_INSTALL_URL; ?>",
		generate_xml_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionGenerateXml",
		get_latlng_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionGetLatLng",
		send_email_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionSendEmail"
	});
</script>