jQuery(document).ready(function($) {
	"use strict";

	$(".edit_tweethandles").on('click', function()
	{
		$(this).fadeOut('slow', function()
		{
			$(".user_tweet_settings").fadeIn('slow');
		});
	});
	$("[name='update_twitter_settings']").on('click', function()
	{
		$(".loading_status").html('');
		$(".loading_message").fadeIn('slow');
			$.post("/wp-admin/admin-ajax.php",
			{
				"action": "update_twitter_feed",
				"user_handles": $("[name='user_tweet_settings']").val()
			}, function( data )
			{
				$(".timeline").fadeOut('slow',function()
				{
					$(this).html('');
				}).fadeIn('slow', function()
				{
					$(this).html(data);
				});
				$(".loading_message").fadeOut('fast');
			}).fail(function( response )
			{
				$(".loading_message").hide();
				$(".loading_status").fadeIn('slow').html('There was a problem with the Twitter API call. Please try again in a few seconds.');
			});
	});
});