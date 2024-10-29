jQuery(document).ready(function( $ ) {
	$("#exit-popup-close").click(function(){
		$("#exit-popup-modal").hide(500);
	});
	var mouseX = 0;
	var mouseY = 0;
	var popupCounter = 0;
	document.addEventListener("mousemove", function(e) {
	    mouseX = e.clientX;
	    mouseY = e.clientY;
	});
	$(document).mouseleave(function () {
	    if (mouseY < 100) {
	        if (popupCounter < 1) {
	            $("#exit-popup-modal").show(500);
	        }
	        popupCounter ++;
	    }
	});	
});