this.imagePreview = function(){
	/* CONFIG */

		xOffset = 10;
		yOffset = 30;

		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result

	/* END CONFIG */
	dragdr("a.preview").hover(function(e){
		this.t = this.title;
		this.title = "";
		var c = (this.t != "") ? "<br/>" + this.t : "";
		dragdr("body").append("<p id='preview' style='display:block;position:absolute'><img src='"+ this.id +"' alt='Image preview' />"+ c +"</p>");//alert('TTTDD');
		dragdr("#preview")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");
                        //alert('title');
    },
	function(){
		this.title = this.t;
		dragdr("#preview").remove();
    });
	dragdr("a.preview").mousemove(function(e){
          
		dragdr("#preview")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});
};

var dragdr = jQuery.noConflict();
