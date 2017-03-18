(function (window, undefined) {
	var d = window.document;
	
	function stivaSTL(options) {
		if (!(this instanceof stivaSTL)) {
			return new stivaSTL(options);
		}
		this.options = {};
		this.map;
		this.storeData;
		this.directionsDisplay = new google.maps.DirectionsRenderer();
		this.directionsService = new google.maps.DirectionsService();
		this.geocoder = new google.maps.Geocoder();
		this.markersArray = [];
		this.init(options);
		return this;
	}
	
	stivaSTL.prototype = {
		
		showOverlays: function () {
			var self = this;
			if (self.markersArray) {
				for (i in self.markersArray) {
					if (self.markersArray.hasOwnProperty(i)) {
						self.markersArray[i].setMap(self.map);
					}
				}
			}
		},
		
		deleteOverlays: function () {
			var self = this;
			if (self.markersArray) {
				for (i in self.markersArray) {
					if (self.markersArray.hasOwnProperty(i)) {
						self.markersArray[i].setMap(null);
					}
				}
				self.markersArray.length = 0;
			}
		},
			
		addMarker: function (location, title, category_marker, content, distance) {
			var self = this;
			if(category_marker != null && category_marker != '')
			{
				var marker = new google.maps.Marker({
					position: location,
					icon: self.options.install_url + category_marker,
					map: self.map,
					title: title
				});
			}else{
				var marker = new google.maps.Marker({
					position: location,
					map: self.map,
					title: title
				});
			}	
			
			if (content.length > 0 && content != "") {
				marker.infoWindow = new google.maps.InfoWindow({
					content: content
				});
				google.maps.event.addListener(marker, "click", function() {
					for (var i = self.markersArray.length - 1; i >= 0; i--) {
						self.markersArray[i].infoWindow.close();
					}
					this.infoWindow.open(self.map, marker);
				});
			}  
			self.markersArray.push(marker);
			return self.markersArray.length - 1;
		},	
			
		resultMarkers: function (data) {
			var	self = this,
				latlng, title, category_marker, distance, arr = [], addr = [], phone, email, website, address, i, LatLngList = [], 
				phone_search, email_search, website_search, opening_times_search, address_search, img_tag, flag=true, firstMarker;;
			self.storeData = data;
			self.deleteOverlays();
			if (data.length > 0) {
				for (var k in data) {
					if (data.hasOwnProperty(k)) {
						latlng = new google.maps.LatLng(data[k].lat, data[k].lng);
						LatLngList.push(latlng);
						title = data[k].name;
						category_marker = data[k].marker;
						distance = data[k].distance;
						addr = [];
						
						if (data[k].address_content && data[k].address_content !== "") {
							addr.push(data[k].address_content);
						}
						if (data[k].address_city && data[k].address_city !== "") {
							addr.push(data[k].address_city);
						}
						if (data[k].address_state && data[k].address_state !== "") {
							addr.push(data[k].address_state);
						}
						if (data[k].address_zip && data[k].address_zip !== "") {
							addr.push(data[k].address_zip);
						}
						phone = "";
						phone_search = "";
						if (data[k].phone && data[k].phone !== "") {
							phone = '<label class="stl-row-tooltip">'+self.options.label_phone+': <span class="stl-content-tooltip">' + data[k].phone + '</span></label>';
							phone_search = '<div class="stl-item-row"><label>'+self.options.label_phone+': </label><span>' + data[k].phone + '</span></div>';
						}
						email = "";
						email_search = "";
						if (data[k].email && data[k].email !== "") {
							email = '<label class="stl-row-tooltip">'+self.options.label_email+': <span class="stl-content-tooltip">' + data[k].email + '</span></label>';
							email_search = '<div class="stl-item-row"><label>'+self.options.label_email+': </label><span>' + data[k].email + '</span></div>';
						}
						website = "";
						website_search = "";
						if (data[k].website && data[k].website !== "") {
							website = '<label class="stl-row-tooltip">'+self.options.label_website+': <span class="stl-content-tooltip">' + data[k].website + '</span></label>';
							website_search = '<div class="stl-item-row"><label>'+self.options.label_website+': </label><span>' + data[k].website + '</span></div>';
						}
						opening_times_search = "";
						if (data[k].opening_times && data[k].opening_times !== "") {
							opening_times_search = '<div class="stl-item-row"><label>'+self.options.label_opening_time+':</label><br/><span>' + data[k].opening_times.replace(/\r\n/g, '<br/>') + '</span></div>';
						}
						
						address = "";
						address_search = "";
						if(addr.length > 0)
						{
							address = '<label class="stl-row-tooltip">'+self.options.label_address+': <span class="stl-content-tooltip">' + addr.join(", ") + '</span></label>';
							address_search = 	'<div id="stl_hidden_container_'+k+'" class="stl-hidden-container">'+
													'<div id="stl_full_address_'+k+'" class="stl-full-address" lang="'+k+'">'+self.options.label_full_address+'</div>'+
													'<div id="stl_directions_'+k+'" class="stl-directions" lang="'+k+'">'+self.options.label_directions+'</div>'+
												'</div>' +
												'<div id="stl_store_address_' + k + '" class="stl-item-row stl-store-address">'+
													'<label>'+self.options.label_address+': </label>' + 
													'<span>' + addr.join(", ") + '</span>'+
												'</div>'+
												'<div id="stl_close_address_'+k+'" class="stl-close-address" lang="'+k+'">'+self.options.label_close+'</div>' +
												'<div id="stl_direction_box_'+k+'" class="stl-direction-box stl-form" lang="'+k+'">'+
													'<p>'+
														'<label class="title30">'+self.options.label_from+'</label>'+
														'<input id="stl_direction_text_'+k+'" class="stl-direction-text stl-text stl-w150" name="stl_direction_text_'+k+'" lang="'+k+'" />' +
														'<input type="button" value="'+self.options.label_go+'" name="stl_direction_go_'+k+'" class="stl-button stl-go-button" lang="'+k+'" />' + 
													'</p>'+
													'<div id="stl_close_direction_'+k+'" class="stl-close-direction" lang="'+k+'">'+self.options.label_close+'</div>' +
												'</div>';
						}
						
						img_tag = "";
						if (data[k].image_path && data[k].image_path !== "") {
							img_tag = '<img class="stl-store-image" src="'+ self.options.install_url + data[k].image_path + '" lang="' + k + '" />'; 
						}
						
						i = self.addMarker(latlng, title, category_marker, ['<div class="stl-google-tooltip">'+img_tag+'<div class="stl-detail-tooltip"><h3>', title, '</h3>', phone, email, website, address, '</div></div>'].join(""), distance);
                        if (flag){
                            firstMarker = latlng;
                            flag = false;
                        }						
						var store_detail = 	'<div class="stl-store-item" lang="' + k + '">' +
												img_tag +
												'<div class="stl-store-item-detail">' +
													'<abbr class="stl-store-title" lang="' + k + '">' + title + '</abbr>' +
													phone_search +
													email_search +
													website_search +
													opening_times_search + 
													address_search +
												'</div>' +
											'</div>';
						arr.push(store_detail);
					}			  
				}
				self.showOverlays();
				
				var bounds = new google.maps.LatLngBounds();
				for (var j = 0, len = LatLngList.length; j < len; j++) {
					bounds.extend(LatLngList[j]);
				}
				
                if (firstMarker!=null) self.map.setCenter(firstMarker);
                self.map.fitBounds(bounds);
                self.$search_directions.style.display = 'none';
				self.$search_addresses.innerHTML = arr.join("");
				self.$search_addresses.style.display = 'block';
				self.bindSearchResult();
			} else {
				self.emptyResults();
			}
			
		},
			
		getMarkers: function (url){
			var self = this;				
			JABB.Ajax.getJSON(url, function (json) {
				self.resultMarkers(json);																								
			});
		},
		
		loadDirections: function(url, i)
		{
			var self = this;
			
			JABB.Ajax.getJSON(url, function (json) {
				if(json.code == '200')
				{
					self.directionsDisplay.setMap(null);
					self.directionsDisplay.setMap(self.map);
					self.directionsDisplay.setPanel(document.getElementById("stl_search_directions_panel"));
				
					var start =new google.maps.LatLng(parseFloat(json.lat), parseFloat(json.lng));
					var end = new google.maps.LatLng(parseFloat(self.storeData[i].lat), parseFloat(self.storeData[i].lng));
					
					var request = {
									origin: start,
									destination: end,
									travelMode: google.maps.TravelMode.DRIVING
							};
					self.directionsService.route(request, function(result, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							self.directionsDisplay.setDirections(result);
						}
					});
					
					self.$search_result.style.display = "none";
					self.$search_directions.style.display = "block";
					self.bindDirections();
				}
			});
		},
		
		bindSearchResult: function () {
			var self = this,
				arr = JABB.Utils.getElementsByClass("stl-store-title", document.getElementById("stl_search_addresses"), "ABBR");
			for (var i = 0, len = arr.length; i < len; i++) {
				JABB.Utils.addEvent(arr[i], "click", function (e) {
					if (e.preventDefault) {
						e.preventDefault();
					}
					var item_arr = JABB.Utils.getElementsByClass("stl-store-item", document.getElementById("stl_search_addresses"), "DIV");
					for (var j = 0, jlen = item_arr.length; j < jlen; j++) {
						if(item_arr[j].getAttributeNode("lang").value == this.getAttributeNode("lang").value)
						{
							JABB.Utils.addClass(item_arr[j], "stl-item-focus");
						}else{
							JABB.Utils.removeClass(item_arr[j], "stl-item-focus");
						}
					}
					
					google.maps.event.trigger(self.markersArray[this.getAttributeNode("lang").value], 'click');
					return false;
				});
			}
			
			var arr = JABB.Utils.getElementsByClass("stl-store-image", document.getElementById("stl_search_addresses"), "IMG");
			for (var i = 0, len = arr.length; i < len; i++) {
				JABB.Utils.addEvent(arr[i], "click", function (e) {
					if (e.preventDefault) {
						e.preventDefault();
					}
					var item_arr = JABB.Utils.getElementsByClass("stl-store-item", document.getElementById("stl_search_addresses"), "DIV");
					for (var j = 0, jlen = item_arr.length; j < jlen; j++) {
						if(item_arr[j].getAttributeNode("lang").value == this.getAttributeNode("lang").value)
						{
							JABB.Utils.addClass(item_arr[j], "stl-item-focus");
						}else{
							JABB.Utils.removeClass(item_arr[j], "stl-item-focus");
						}
					}
					
					google.maps.event.trigger(self.markersArray[this.getAttributeNode("lang").value], 'click');
					return false;
				});
			}
			
			var arr = JABB.Utils.getElementsByClass("stl-full-address", document.getElementById("stl_search_addresses"), "DIV");
			for (var i = 0, len = arr.length; i < len; i++) {
				JABB.Utils.addEvent(arr[i], "click", function (e) {
					var i = this.getAttributeNode("lang").value;
					
					document.getElementById("stl_hidden_container_" + i).style.display = 'none';
					document.getElementById("stl_store_address_" + i).style.display = 'block';
					document.getElementById("stl_close_address_" + i).style.display = 'block';
					google.maps.event.trigger(self.markersArray[i], 'click');
				});
			}
			
			var arr = JABB.Utils.getElementsByClass("stl-close-address", document.getElementById("stl_search_addresses"), "DIV");
			for (var i = 0, len = arr.length; i < len; i++) {
				JABB.Utils.addEvent(arr[i], "click", function (e) {
					var i = this.getAttributeNode("lang").value;
					this.style.display = 'none';
					document.getElementById("stl_store_address_" + i).style.display = 'none';
					document.getElementById("stl_hidden_container_" + i).style.display = 'block';
				});
			}
			
			var arr = JABB.Utils.getElementsByClass("stl-directions", document.getElementById("stl_search_addresses"), "DIV");
			for (var i = 0, len = arr.length; i < len; i++) {
				JABB.Utils.addEvent(arr[i], "click", function (e) {
					var i = this.getAttributeNode("lang").value;
					
					document.getElementById("stl_hidden_container_" + i).style.display = 'none';
					document.getElementById("stl_direction_box_" + i).style.display = 'block';
				});
			}
			
			var arr = JABB.Utils.getElementsByClass("stl-close-direction", document.getElementById("stl_search_addresses"), "DIV");
			for (var i = 0, len = arr.length; i < len; i++) {
				JABB.Utils.addEvent(arr[i], "click", function (e) {
					var i = this.getAttributeNode("lang").value;
					document.getElementById("stl_direction_text_" + i).value = '';
					document.getElementById("stl_direction_box_" + i).style.display = 'none';
					document.getElementById("stl_hidden_container_" + i).style.display = 'block';
				});
			}
			
			var arr = JABB.Utils.getElementsByClass("stl-direction-text", document.getElementById("stl_search_addresses"), "INPUT");
			for (var i = 0, len = arr.length; i < len; i++) {
				JABB.Utils.addEvent(arr[i], "keyup", function (e) {
					var i = this.getAttributeNode("lang").value;
					if(e.keyCode == 13)
					{
						var url = self.options.get_latlng_url + '&address=' + this.value;
						self.loadDirections(url, i);
					}
				});
			}
			
			var arr = JABB.Utils.getElementsByClass("stl-go-button", document.getElementById("stl_search_addresses"), "INPUT");
			for (var i = 0, len = arr.length; i < len; i++) {
				JABB.Utils.addEvent(arr[i], "click", function (e) {
					var i = this.getAttributeNode("lang").value;
					var url = self.options.get_latlng_url + '&address=' + document.getElementById("stl_direction_text_"+ i).value;
					self.loadDirections(url, i);
				});
			}
		},
		
		bindDirections: function () {
			var i, iCnt,
				self = this,
				close = JABB.Utils.getElementsByClass("stl-directions-close", this.$search_directions, "A")[0],
				hidden = JABB.Utils.getElementsByClass("stl-hidden-container", this.$search_addresses, "DIV"),
				box = JABB.Utils.getElementsByClass("stl-direction-box", this.$search_addresses, "DIV"),
				text = JABB.Utils.getElementsByClass("stl-direction-text", this.$search_addresses, "INPUT"),
				send = document.getElementById("stl_send_email");
			if (close) {
				close.onclick = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					self.$search_directions.style.display = "none";
					self.$directions_email.style.display = "none";
					self.$search_result.style.display = "block";
					self.directionsDisplay.setMap(null);
					return false;
				};
			}
			
			for (i = 0, iCnt = hidden.length; i < iCnt; i++) {
				hidden[i].style.display = 'block';
			}
			
			for (i = 0, iCnt = box.length; i < iCnt; i++) {
				box[i].style.display = 'none';
			}
			
			for (i = 0, iCnt = text.length; i < iCnt; i++) {
				text[i].value = '';
			}
			
			JABB.Utils.addEvent(self.$email_menu, "click", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.$directions_html.value = document.getElementById("stl_search_directions_panel").innerHTML;
				self.$directions_email.style.display = "block";
			});
			
			if (send) {
				send.onclick = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					var form = document.getElementById("stl_send_email_form");
					send.disabled = true;
					JABB.Ajax.postJSON(self.options.send_email_url, function (json) {
						if(json.code == 200)
						{
							self.$directions_email.style.display = "none";
							alert(self.options.label_sent);
						}else if(json.code == 100){
							alert(self.options.label_empty_email);
						}else if(json.code == 300){
							alert(self.options.label_invalid_email);
						}
						send.disabled = false;
					}, JABB.Utils.serialize(form));
				};
			}
		},
		
		searchLocations: function()
		{
			var self = this,
				search_form = d.forms[self.options.search_form_name],
				address = search_form['address'].value;
            if (address == ''){
                address = "USA";
            }
				
			self.geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var radius = search_form['radius'].value;
                    self.map.setCenter(results[0].geometry.location);
					if(self.options.use_categories == 'Yes')
					{
						var category_id = search_form['category_id'].value;
						self.getMarkers([self.options.generate_xml_url + '&lat=', results[0].geometry.location.lat(), '&lng=', results[0].geometry.location.lng(), '&radius=', radius, '&address=', address, "&distance=", self.options.distance, "&category_id=", category_id].join(""));
					}else{
						self.getMarkers([self.options.generate_xml_url + '&lat=', results[0].geometry.location.lat(), '&lng=', results[0].geometry.location.lng(), '&radius=', radius, '&address=', address, "&distance=", self.options.distance].join(""));
					}
				} else {
					self.emptyResults();
				}
			});
		},
		
		bindSearchForm: function()
		{
			var self = this;
			JABB.Utils.addEvent(d.forms[self.options.search_form_name][self.options.search_form_search_name], "click", function () {
				self.searchLocations();
			});
			JABB.Utils.addEvent(d.forms[self.options.search_form_name][self.options.search_form_address], "keyup", function (e) {
				if(e.keyCode == 13)
				{
					e.preventDefault();
					self.searchLocations();
				}
			});
			
            JABB.Utils.addEvent(d.forms[self.options.search_form_name][self.options.search_form_address], "focus", function () {
                this.value = '';
            });
			
		},
		
		emptyResults: function () {
			var self = this;
			this.$search_addresses.innerHTML = '<div class="stl-store-empty">'+self.options.label_not_found+'</div>';
			this.$search_addresses.style.display = 'block';
			this.$search_directions.style.display = 'none';
			this.$search_directions.innerHTML = '';
		},
		
		init: function (stivaObj) {
			var self = this;
			
			this.$search_result = document.getElementById("stl_search_result");
			this.$search_addresses = document.getElementById("stl_search_addresses");
			this.$search_directions = document.getElementById("stl_search_directions");
			this.$email_menu = document.getElementById("stl_email_menu");
			this.$directions_email = document.getElementById("stl_directions_email");
			this.$directions_html = document.getElementById("stl_directions_html");
			
			self.options = stivaObj;
			self.bindSearchForm();
            if (self.options.default_address == ''){
                self.options.default_address = "USA";
            }

			self.geocoder.geocode( { 'address': self.options.default_address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var stlOptions = {
						zoom: self.options.zoom_level,
						center: results[0].geometry.location,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					};
					self.map = new google.maps.Map(d.getElementById("stl_store_canvas"), stlOptions);
					self.searchLocations();
				} else {
					self.emptyResults();
				}
			});
		}
	}
	return (window.stivaSTL = stivaSTL);
})(window);