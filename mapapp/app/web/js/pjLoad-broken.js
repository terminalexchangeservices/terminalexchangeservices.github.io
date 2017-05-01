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
				latlng, title, category_marker, distance, i, LatLngList = [], store_list, flag=true, firstMarker;
				
			self.storeData = data;
			self.deleteOverlays();
			if (data.length > 0) {
				var cnt = data.length - 1;
				for (var k in data) 
				{
					if (data.hasOwnProperty(k) && k < cnt) {
						latlng = new google.maps.LatLng(data[k].lat, data[k].lng);
						LatLngList.push(latlng);
						title = data[k].name;
						category_marker = data[k].marker;
						distance = data[k].distance;
						
						i = self.addMarker(latlng, title, category_marker, data[k].marker_content, distance);
                        if (flag){
                            firstMarker = latlng;
                            flag = false;
                        }
					}			  
				}
				store_list = data[cnt].store_list;
				self.showOverlays();
				
				var bounds = new google.maps.LatLngBounds();
				for (var j = 0, len = LatLngList.length; j < len; j++) {
					bounds.extend(LatLngList[j]);
				}

                if (firstMarker!=null) self.map.setCenter(firstMarker);
				self.$search_directions.style.display = 'none';
				self.$search_addresses.innerHTML = store_list;
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
				
			self.geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var radius = search_form['radius'].value;
                    self.map.setCenter(results[0].geometry.location)
					if(self.options.use_categories == 'Yes')
					{
						var category_id = search_form['category_id'].value;
						self.getMarkers([self.options.generate_xml_url + '&lat=', results[0].geometry.location.lat(), '&lng=', results[0].geometry.location.lng(), '&radius=', radius, "&distance=", self.options.distance, "&category_id=", category_id].join(""));
					}else{
						self.getMarkers([self.options.generate_xml_url + '&lat=', results[0].geometry.location.lat(), '&lng=', results[0].geometry.location.lng(), '&radius=', radius, "&distance=", self.options.distance].join(""));
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