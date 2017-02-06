/*global jQuery:false */
jQuery(document).ready(function($) {

	'use strict';

	// Widget Button reveals - close onLoad
	// $('.secrets > div, .avatar > div, .twitterFollow > div, .modTime > div, .twitterIntents > div').hide();
	// Reveal/Hide onClick and change arrow direction
	$(document).on('click', '.secrets h4, .avatar h4, .twitterFollow h4, .modTime h4, .twitterIntents h4', function() {
		// var tFollow = $(this).next('div');
		$(this).next('div').addClass('open');
		$(this).next('div').slideToggle('fast', function() {
			if(!$(this).is(':hidden')) {
				$(this).siblings('h4').children('span').html('&#9650;');
			}else{
				$(this).removeClass('open');
				$(this).siblings('h4').children('span').html('&#9660;');
			}
		});
	});


	function savedIt(e, xhr, settings){
		// settings.data.search('action=save-widget') != -1

		if( $('.secrets > div, .avatar > div, .twitterFollow > div, .modTime > div, .twitterIntents > div').hasClass('open') ){
			// Do nothing
		}else{
			// reset toggles - clean view
			// $('.secrets > div, .avatar > div, .twitterFollow > div, .modTime > div, .twitterIntents > div').slideUp();
			$('.secrets > div, .avatar > div, .twitterFollow > div, .modTime > div, .twitterIntents > div').hide();
			$('.secrets h4 > span, .avatar h4 > span, .twitterFollow h4 > span, .modTime h4 > span, .twitterIntents h4 > span').html('&#9660;');
		}
	}

	// // Widget - Auto Ajax Saved
	$(document).ajaxSuccess(function(e, xhr, settings) {
		savedIt(e, xhr, settings);
	}); // END AJAX success


}); // END READY