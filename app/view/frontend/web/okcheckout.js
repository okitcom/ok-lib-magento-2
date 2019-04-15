define(
    [
        'jquery',
        'mage/url',
        'Okitcom_OkLibMagento/oklibpresenter',
        'mage/validation'
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

            const type = 'cash';
            $(element).on('click', function(e) {
                e.preventDefault();

                if (oklibpresenter.isInitialized(type)) {
                    oklibpresenter.showExisting(type);
                } else {
                    $(element).addClass("ok-button-progress");
                    $.ajax({
                        showLoader: true,
                        url: '/oklib/ajax/cash',
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