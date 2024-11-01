/*
Plugin Name: (Spyr) Network Bar
Author: Spyr Media
Author URI: http://spyr.me
*/

jQuery(document).ready(function() {
	var spyr_bar = jQuery("#spyr_bar");
	var spyr_bar_logo = jQuery("#spyr_bar_logo");
	var spyr_bar_nav = jQuery(".spyr_bar_nav ul");
	spyr_bar_logo.mouseenter(
	 function(){
		spyr_bar_nav.slideDown();
		spyr_bar_logo.addClass('hover');
		});
	spyr_bar.mouseleave(
	 function(){
		spyr_bar_nav.delay(100).slideUp('fast');
		spyr_bar_logo.removeClass('hover');
		});
	jQuery("#spyr_bar .menu-item a").prepend("<span class=\"subnav_arrow\"></span>");
	});