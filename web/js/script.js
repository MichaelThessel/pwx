var Pwx = {

    // Initialize app
    init: function() {
        // Enable zclip on the clippboard button
        if (this.hasFlash()) {
            $('.link-container button').zclip({
                path: '/js/ZeroClipboard.swf',
                copy: $('.link-container #link').text(),
                afterCopy: function() {}
            });
        } else {
            $('.link-container button').hide();
        }

        // Click event handler for password generation
        $('.password-length').click($.proxy(function(e) {
            e.stopPropagation();
            var length = parseInt($(e.target).text());
            $('#password').val(this.generatePassword(length));
        }, this));

        $('#expires').countdown($('#expires').data('expires'), this.countdownCallback);
    },

    // Callback function for the countdown timer
    countdownCallback: function(e) {
      $(this).html(e.strftime('' + '<span>%D</span> days ' + '<span>%H</span> hr ' + '<span>%M</span> min ' + '<span>%S</span> sec'));
    },

    // Detect if flash is enabled (http://stackoverflow.com/a/20095467)
    hasFlash: function() {
        var hasFlash = false;
        try {
            var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
            if (fo) {
                hasFlash = true;
            }
        } catch (e) {
            if (
                navigator.mimeTypes &&
                navigator.mimeTypes['application/x-shockwave-flash'] !== undefined &&
                navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin
            ) {
                hasFlash = true;
            }
        }

        return hasFlash;
    },

    // Generate random password string
    generatePassword: function(length) {
        var i, password,
            possibleChars = 'abcdefghijklmnopqrstuvwxyz';
        possibleChars += 'ABCDEFGHIJKLMNOPQSTUVWXYZ';
        possibleChars += '0123456789';
        possibleChars += '!@#$%^&*()_+-=[]{};"\'<>,./?\\';

        length = length || 24;

        password = '';
        for (i = 0; i < length; i++) {
            password += possibleChars.charAt(Math.floor(Math.random() * possibleChars.length));
        }

        return password;
    }
};

$(document).ready(function() {
    Pwx.init();
});
