define(
    [
        'jquery',
        'mage/url'
    ],
    function (
        $,
        url
    ) {
        'use strict';

        var getLibType = function(type) {
            switch (type) {
                case "open":
                    return "a";
                case "cash":
                    return "t";
            }
            return null;
        };

        return {
            showExisting: function (type) {
                window.$ = $;
                /**
                 * Either cash or open
                 */
                const current = window.okLibType;
                if (current === type) {
                    // just show the lib
                    window.oklib.show();
                    return true;
                }
                return false;
            },
            showNew: function (type, data) {
                window.$ = $;
                /**
                 * Either cash or open
                 */
                const current = window.okLibType;
                if (current !== null) {
                    window.oklib.remove();
                }
                window.okLibType = type;
                window.oklib.init(getLibType(type), data.guid, {
                    color: "dark",
                    culture: data.culture,
                    loaded: oklib.start
                }, data.environment);
            },
            remove: function () {
                window.$ = $;
                const current = window.okLibType;
                if (current !== null) {
                    window.oklib.remove();
                }
                window.okLibType = null;
            }
        };

    }
);