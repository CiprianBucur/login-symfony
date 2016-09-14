/**
 * This tiny script just helps us demonstrate
 * what the various example callbacks are doing
 */
var Example = (function() {
    "use strict";

    var elem,
        hideHandler,
        that = {};

    that.init = function(options) {
        elem = $(options.selector);
    };

    that.show = function(text) {
        clearTimeout(hideHandler);

        elem.find("span").html(text);
        elem.delay(500).fadeIn().delay(5000).fadeOut();
    };

    return that;
}());