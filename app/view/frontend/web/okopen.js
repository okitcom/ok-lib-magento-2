define(
    [
        'jquery',
        'mage/url',
        'Okitcom_OkLibMagento/oklibpresenter'
    ],
    function (
        $,
        url,
        oklibpresenter
    ) {
        'use strict';
        /**
         *
         * mystep - is the name of the component's .html template,
         * <Vendor>_<Module>  - is the name of the your module directory.
         *
         */

        return function (config, element) {

            var type = 'open';
            $(element).on('click', function(e) {

                if (oklibpresenter.isInitialized(type)) {
                    oklibpresenter.showExisting(type);
                } else {
                    $(element).addClass("ok-button-progress");

                    $.ajax({
                        showLoader: true,
                        url: '/oklib/ajax/open',
                        data: "",
                        type: "GET",
                        dataType: 'json'
                    }).done(function (data) {
                        $(element).removeClass("ok-button-progress");
                        oklibpresenter.showNew(type, data);
                    }).error(function(err, data) {
                        $(element).removeClass("ok-button-progress");
                    });

                }

            });


        };
    }
);