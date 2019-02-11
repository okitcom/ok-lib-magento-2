define(
    [
        'jquery',
        'mage/url',
        'https://okit.com/js/oklib/dist/oklib.min.js'
    ],
    function (
        $,
        url,
        oklib
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
                /**
                 * Either cash or open
                 */
                const current = window.okLibType;
                if (current === type) {
                    // just show the lib
                    oklib.show();
                    return true;
                }
                return false;
            },
            showNew: function (type, data) {
                /**
                 * Either cash or open
                 */
                const current = window.okLibType;
                if (typeof current !== 'undefined' && current != null) {
                    oklib.remove();
                }
                window.okLibType = type;
                oklib.init(getLibType(type), data.guid, {
                    color: "dark",
                    culture: data.culture,
                    loaded: oklib.start,
                    initiation: data.initiation
                }, data.environment);
            },
            remove: function () {
                const current = window.okLibType;
                if (typeof current !== 'undefined') {
                    oklib.remove();
                }
                window.okLibType = null;
            }
        };

    }
);