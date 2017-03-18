var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateCategory = $("#frmCreateCategory"),
			$frmUpdateCategory = $("#frmUpdateCategory"),
			$dialogDeleteMarker = $("#dialogDeleteMarker"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($frmCreateCategory.length > 0 && validate) {
			
			$frmCreateCategory.validate({
				rules: {
					"category_title": {
						required: true,
						remote: "index.php?controller=pjAdminCategories&action=pjActionCheckCategoryName"
					},
					"marker":{
						extension: "jpg|png|jpeg|gif"
					}
				},
				messages:{
					"category_title": {
						remote: myLabel.same_category
					},
					"marker":{
						extension: myLabel.allowed_extension
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($frmUpdateCategory.length > 0 && validate) {
			$frmUpdateCategory.validate({
				rules: {
					"category_title": {
						required: true,
						remote: "index.php?controller=pjAdminCategories&action=pjActionCheckCategoryName&id=" + $frmUpdateCategory.find("input[name='id']").val()
					},
					"marker":{
						extension: "jpg|png|jpeg|gif"
					}
				},
				messages:{
					"category_title": {
						remote: myLabel.same_category
					},
					"marker":{
						extension: myLabel.allowed_extension
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
			
			if($dialogDeleteMarker.length > 0)
			{
				$dialogDeleteMarker.dialog({
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
								url: "index.php?controller=pjAdminCategories&action=pjActionDeleteMarker&id=" + id,
								success: function (res) {
									if(res.code == 200)
									{
										$('#marker_container').remove();
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
					$dialogDeleteMarker.dialog('open');
				});
			}
		}
		
		if ($("#grid").length > 0 && datagrid) 
		{
			function onBeforeShow (obj) {
				return true;
			}
			function showStores(str, obj){
				if(str == '0')
				{
					return 0;
				}else{
					return '<a href="index.php?controller=pjAdminStores&action=pjActionIndex&category_id='+obj.id+'">'+str+'</a>';
				}
			}
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminCategories&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminCategories&action=pjActionDeleteCategory&id={:id}", beforeShow: onBeforeShow}],
						  
				columns: [{text: myLabel.category, type: "text", sortable: true, editable: true, width: 380, editableWidth: 250},
				          {text: myLabel.stores, type: "text", sortable: false, editable: false, renderer: showStores},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminCategories&action=pjActionGetCategory",
				dataType: "json",
				fields: ['category_title', 'cnt_stores', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminCategories&action=pjActionDeleteCategoryBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjAdminCategories&action=pjActionStatusCategory", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminCategories&action=pjActionExportCategory", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminCategories&action=pjActionSaveCategory&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
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
			$grid.datagrid("load", "index.php?controller=pjAdminCategories&action=pjActionGetCategory", "category_title", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminCategories&action=pjActionGetCategory", "category_title", "ASC", content.page, content.rowCount);
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
			$.post("index.php?controller=pjAdminCategories&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminCategories&action=pjActionGetCategory");
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
			$grid.datagrid("load", "index.php?controller=pjAdminCategories&action=pjActionGetCategory", "category_title", "ASC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);