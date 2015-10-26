var Pwx = {

    // Initialize app
    init: function() {
        this.initLanguageSelector();
        this.initThemeSelector();
        this.initClipboard();
        this.initCountdown();
        this.initPasswordGenerator();
        this.initShowPassword();
    },

    // Init language selector
    initLanguageSelector: function() {
        $('#language-selector li a').click(function(e) {
            e.preventDefault();
            if ($(this).closest('li').hasClass('active')) { return; }
            Cookies.set('locale', $(this).data('locale'), { expires: 365 });
            window.location.href = window.location.href
                .replace(/locale=[a-zA-Z]{2}&?/, '')
                .replace(/[?&#]$/, '');
        });
    },

    // Init theme selector
    initThemeSelector: function() {
        $('#theme-selector li a').click(function(e) {
            e.preventDefault();
            if ($(this).closest('li').hasClass('active')) { return; }
            Cookies.set('theme', $(this).data('theme'), { expires: 365 });
            window.location.href = window.location.href
                .replace(/[#]$/, '');
        });
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
        $('#expires-countdown').countdown($('#expires-countdown').data('expires'), this.countdownCallback);
    },

    // Click event handler for password generation
    initPasswordGenerator: function() {
        $('.password-length').click($.proxy(function(e) {
            var length = parseInt($(e.target).text());
            $('#password').val(this.generatePassword(length));

            e.stopPropagation();
        }, this));
    },

    // Init show/hide password link
    initShowPassword: function() {
        $('[data-show-password]').click(function(e) {
            var toggle = $(this).data('show-password');

            if (toggle) {
                $(this).data('show-password', false);
                $(this).html($(this).data('show-password-text-show'));
            } else {
                $(this).data('show-password', true);
                $(this).html($(this).data('show-password-text-hide'));
            }

            $('#password').togglePassword();

            e.stopPropagation();
        });
    },

    // Callback function for the countdown timer
    countdownCallback: function(e) {
      $(this).html(e.strftime('<span>%D</span> ' + LOCALE.app.data.days + ' ' + '<span>%H</span> ' + LOCALE.app.data.hours + ' ' + '<span>%M</span> ' + LOCALE.app.data.minutes + ' ' + '<span>%S</span> ' + LOCALE.app.data.seconds));
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
