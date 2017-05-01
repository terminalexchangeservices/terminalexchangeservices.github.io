// Menu Select Drop-down

	 // DOM ready
	 $(function() {
	   
      // Create the dropdown base
      $("<select />").appendTo("#ddmenu");
      
      // Create default option "Go to..."
      $("<option />", {
         "selected": "selected",
         "value"   : "",
         "text"    : "Menu >>"
      }).appendTo("#ddmenu select");
      
      // Populate dropdown with menu items
      $("#navigation a").each(function() {
       var el = $(this);
       $("<option />", {
           "value"   : el.attr("href"),
           "text"    : el.text()
       }).appendTo("#ddmenu select");
      });
      
	   // To make dropdown actually work
	   // To make more unobtrusive: http://css-tricks.com/4064-unobtrusive-page-changer/
      $("#ddmenu select").change(function() {
        window.location = $(this).find("option:selected").val();
      });
	 
	 });

