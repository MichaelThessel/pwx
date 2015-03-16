var Pwx = {

    // Initialize app
    init: function() {
        this.initClipboard();
        this.initCountdown();
        this.initPasswordGenerator();
    },

    // Enable zclip on all elements with the data-clipboard attribute
    initClipboard: function() {
        if (!this.hasFlash()) return;

        $('[data-clipboard]').each(function() {
            var button = $('<button></button>').addClass('btn btn-default').html($('<span></span>').addClass('glyphicon glyphicon-copy'));
            $(this).append(button);
            button.zclip({
                path: '/js/ZeroClipboard.swf',
                copy: $(this).data('clipboard'),
                afterCopy: function() {}
            });
        });
    },

    // Init countdown
    initCountdown: function() {
        $('#expires').countdown($('#expires').data('expires'), this.countdownCallback);
    },

    // Click event handler for password generation
    initPasswordGenerator: function() {
        $('.password-length').click($.proxy(function(e) {
            e.stopPropagation();
            var length = parseInt($(e.target).text());
            $('#password').val(this.generatePassword(length));
        }, this));
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
