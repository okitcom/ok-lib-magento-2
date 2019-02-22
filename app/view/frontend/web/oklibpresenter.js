define(
    [
        'jquery',
        'mage/url',
        'https://ok.app/js/oklib/dist/oklib-lite.min.js'
    ],
    function (
        $,
        url,
        oklib
    ) {
        'use strict';

        var oklibCash = new OKLIBLite();
        var oklibOpen = new OKLIBLite();

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
                    oklibCash.hide();
                    oklibCash = new OKLIBLite();
                } else if (type === 'open') {
                    oklibOpen.hide();
                    oklibOpen = new OKLIBLite();
                }
            }
        };

    }
);