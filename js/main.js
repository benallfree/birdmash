/*global jQuery:false*/
window.jQuery = window.$ = jQuery;

var $window = $(window), $body = $('body');

var App = {

    start : function() {

        "use strict";

        this.bind();

    },

    bind: function() {

        "use strict";

        $(document).on('click', '#bm-container .bm-settings', App.settingsExpand);
        $(document).on('click', '#bm-container .bm-save', App.settingsSave);

    },

    settingsExpand: function(e) {

        "use strict";

        e.preventDefault();

        $('.bw-settings-container').toggleClass('bm-visible');

    },

    settingsSave: function(e) {

        "use strict";

        e.preventDefault();

        $('#bm-container').addClass('bm-ajaxing');

        $.ajax({
            type: 'POST',
            //dataType: 'json',
            url: bm_vars.ajax,
            data: {
                'action': '__bm_do_save',
                'twitter_users_system': $('#bm-container').attr('data-title'),
                'twitter_more': $('#bm-twitter-more').val(),
            },
            success: function( data ) {

                $('#bm-container').html( data );

                $('#bm-container').removeClass('bm-ajaxing');
            }
        });

    },

}

App.start();
