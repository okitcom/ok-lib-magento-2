define(
    [
        'jquery',
        'mage/url',
        'https://ok.app/js/oklib/dist/oklib.min.js'
    ],
    function (
        $,
        url,
        oklib
    ) {
        'use strict';

        var oklibCash = new oklib.OKLIB();
        var oklibOpen = new oklib.OKLIB();

        return {
            showExisting: function (type) {
                if (type === 'cash') {
                    oklibCash.show();
                } else if (type === 'open') {
                    oklibOpen.show();
                }
            },
            showNew: function (type, data) {
                var config = {
                    color: 'dark',
                    culture: data.culture,
                    initiation: data.initiation
                };

                if (type === 'cash') {
                    config.loaded = oklibCash.start;
                    oklibCash.init('t', data.guid, config, data.environment);
                } else if (type === 'open') {
                    config.loaded = oklibOpen.start;
                    oklibOpen.init('a', data.guid, config, data.environment);
                }
            },
            isInitialized: function (type) {
                if (type === 'cash') {
                    return oklibCash.isInitialized();
                } else if (type === 'open') {
                    return oklibOpen.isInitialized();
                }
            },
            reset: function (type) {
                if (type === 'cash') {
                    if (oklibCash.isInitialized()) {
                        oklibCash.hide();
                    }
                    oklibCash = new oklib.OKLIB();
                } else if (type === 'open') {
                    if (oklibOpen.isInitialized()) {
                        oklibOpen.hide();
                    }
                    oklibOpen = new oklib.OKLIB();
                }
            }
        };

    }
);