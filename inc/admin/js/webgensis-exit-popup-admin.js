jQuery(document).ready(function( $ ) {
    if($("input[name='webgensis_exit_popup_select_option']:checked").val() == 0) {
    	$('#selected_posts').hide();
    } 
    if($("input[name='webgensis_exit_popup_select_option']:checked").val() == 1) {
    	$('#relative_posts').hide();
    }
});
function show1(){
  	jQuery('#selected_posts').hide();
    jQuery('#relative_posts').show();
}
function show2(){
  	jQuery('#relative_posts').hide();
    jQuery('#selected_posts').show(); 
}