var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateStore = $("#frmCreateStore"),
			$frmUpdateStore = $("#frmUpdateStore"),
			$frmImportStore = $("#frmImportStore"),
			chosen = ($.fn.chosen !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($frmImportStore.length > 0 && validate) {
			
			$frmImportStore.validate({
				rules: {
					csv:{
						extension: "csv"
					}
				},
				messages: {
					csv:{
						extension: "Only csv files are allowed."
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ''
			});
		}
		
		if ($frmCreateStore.length > 0 && validate) {
			$frmCreateStore.validate({
				rules: {
					"image":{
						extension: "jpg|png|jpeg|gif"
					}
				},
				messages: {
					"image":{
						extension: myLabel.allowed_extension
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ''
			});
		}
		if ($frmUpdateStore.length > 0 && validate) {
			$frmUpdateStore.validate({
				rules: {
					"image":{
						extension: "jpg|png|jpeg|gif"
					}
				},
				messages: {
					"image":{
						extension: myLabel.allowed_extension
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ''
			});
			
			if($("#dialogDeleteImage").length > 0)
			{
				$("#dialogDeleteImage").dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					modal: true,
					width: 400,
					close: function(){
						$('#record_id').val('');
					},
					buttons: {
						'Delete': function() {
							var id = $('#record_id').val(),
								$dialog = $(this);
							$.ajax({
								type: "GET",
								dataType: "json",
								url: "index.php?controller=pjAdminStores&action=pjActionDeleteImage&id=" + id,
								success: function (res) {
									if(res.code == 200)
									{
										$('#image_container').remove();
										$dialog.dialog('close');
									}
								}
							});
							$(this).dialog('close');			
						},
						'Cancel': function() {
							$(this).dialog('close');
						}
					}
				});
				
				$('a.icon-delete').on('click', function(e){
					e.preventDefault();
					var id = $(this).attr('rev');
					$('#record_id').val(id);
					$("#dialogDeleteImage").dialog('open');
				});
			}
			if($('#lat').val() != '' && $('#lng').val() != '')
			{
				initGMap(parseFloat($('#lat').val()), parseFloat($('#lng').val()), $('#name').val());
			}
		}
		
		if (chosen) {
			$("#category_id").chosen();
			$("#country_id").chosen();
		}
		
		if ($("#grid").length > 0 && datagrid) 
		{
			function onBeforeShow (obj) {
				return true;
			}
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminStores&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminStores&action=pjActionDeleteStore&id={:id}", beforeShow: onBeforeShow}],
						  
				columns: [{text: myLabel.store, type: "text", sortable: true, editable: true, width: 250, editableWidth: 200},
				          {text: myLabel.category, type: "text", sortable: false, editable: false, width: 250},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminStores&action=pjActionGetStore"+ pjGrid.queryString,
				dataType: "json",
				fields: ['name', 'categories', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminStores&action=pjActionDeleteStoreBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjAdminStores&action=pjActionStatusStore", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminStores&action=pjActionExportStore", ajax: false},
                        {text: myLabel.export_all, url: "index.php?controller=pjAdminStores&action=pjActionExportStore&all=true", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminStores&action=pjActionSaveStore&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		function initGMap(lat, lng, title)
		{
			var latlng = new google.maps.LatLng(lat, lng);
			var mapOptions = {
					  center: latlng,
					  zoom: 12,
					  mapTypeId: google.maps.MapTypeId.ROADMAP
					};
			var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
			var marker = new google.maps.Marker({
								draggable: true,
								position: latlng,
								map: map,
								title: title
							});
			google.maps.event.addListener(marker, 'dragend', function (event) {
			    $('#lat').val(this.getPosition().lat());
			    $('#lng').val(this.getPosition().lng());
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminStores&action=pjActionGetStore", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminStores&action=pjActionGetStore", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-status-1", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}).on("click", ".pj-status-0", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminStores&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminStores&action=pjActionGetStore");
			});
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminStores&action=pjActionGetStore", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btnGoogleMapsApi", function (e) {
			var $this = $(this);
			$.post("index.php?controller=pjAdminStores&action=pjActionGetGeocode", $(this).closest("form").serialize()).done(function (data) {
				if (data.code !== undefined && data.code == 200) {
					$("#lat").val(data.lat);
					$("#lng").val(data.lng);
					$('#map-message').css('display', 'none').html("");

					initGMap(parseFloat(data.lat), parseFloat(data.lng), $('#name').val());
				} else {
					$('#map-message').html(myLabel.address_not_found).css('display', 'block');
				}
			});
		});
	});
})(jQuery_1_8_2);