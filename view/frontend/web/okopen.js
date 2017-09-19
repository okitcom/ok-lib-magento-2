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
         *
         * mystep - is the name of the component's .html template,
         * <Vendor>_<Module>  - is the name of the your module directory.
         *
         */

        return function (config, element) {

            $(element).on('click', function(e) {
                $.ajax({
                    showLoader: true,
                    url: '/oklib/ajax/open',
                    data: "",
                    type: "GET",
                    dataType: 'json'
                }).done(function (data) {
                    window.$ = $;
                    window.oklib.init('a', data.guid, {
                        color: "dark",
                        culture: data.culture,
                        loaded: oklib.start,
                        callback: function (status, guid) {
                            window.location = url.build("oklib/callback/open") + "?q=" + data.guid;
                        }
                    }, data.environment);
                });
            });


        };
    }
);