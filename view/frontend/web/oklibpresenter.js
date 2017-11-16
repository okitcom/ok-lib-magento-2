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

        /**
         * Either cash or open
         */
        const current = window.okLibType;

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
                if (current === type) {
                    // just show the lib
                    window.oklib.show();
                    return true;
                }
                return false;
            },
            showNew: function (type, data) {
                if (current !== null) {
                    window.oklib.remove();
                }
                window.okLibType = type;
                window.$ = $;
                window.oklib.init(getLibType(type), data.guid, {
                    color: "dark",
                    culture: data.culture,
                    loaded: oklib.start,
                    callback: function (status, guid) {
                        window.location = url.build("oklib/callback/" + type) + "?q=" + data.guid;
                    }
                }, data.environment);
            }
        };

    }
);