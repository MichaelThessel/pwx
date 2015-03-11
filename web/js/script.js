$(document).ready(function() {
    // Enable zclip on the clippboard button
    if (hasFlash()) {
        $('.share-container button').zclip({
            path: '/js/ZeroClipboard.swf',
            copy: $('.share-container #share').text(),
            afterCopy: function() {}
        });
    } else {
        $('.share-container button').hide();
    }

});

// Callback function for the countdown timer
countdownCallback = function(event) {
  $(this).html(event.strftime('' + '<span>%D</span> days ' + '<span>%H</span> hr ' + '<span>%M</span> min ' + '<span>%S</span> sec'));
};

// Detect if flash is enabled (http://stackoverflow.com/a/20095467)
hasFlash = function() {
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
};
