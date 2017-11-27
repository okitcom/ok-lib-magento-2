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

            const type = 'open';
            $(element).on('click', function(e) {

                if (!oklibpresenter.showExisting(type)) {

                    $.ajax({
                        showLoader: true,
                        url: '/oklib/ajax/open',
                        data: "",
                        type: "GET",
                        dataType: 'json'
                    }).done(function (data) {
                        oklibpresenter.showNew(type, data);
                    });

                }

            });


        };
    }
);