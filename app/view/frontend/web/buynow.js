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
        var lastSelectedOptions = null;

        return function (config, element) {

            var type = 'cash';
            var addtocart_form_selector = "#product_addtocart_form";
            $(element).on('click', function(e) {
                e.preventDefault();

                var form = $(addtocart_form_selector);

                // TODO: Check if user changed the select options
                var formData = form.serialize();
                if (lastSelectedOptions !== formData) {
                    oklibpresenter.reset(type);
                }
                lastSelectedOptions = formData;

                // Check if options are valid
                var valid = form.validation('isValid');
                if (!valid) {
                    return;
                }


                if (oklibpresenter.isInitialized(type)) {
                    oklibpresenter.showExisting(type);
                } else {
                    $(element).addClass("ok-button-progress");
                    $.ajax({
                        showLoader: true,
                        url: '/oklib/ajax/buynow',
                        data: lastSelectedOptions,
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