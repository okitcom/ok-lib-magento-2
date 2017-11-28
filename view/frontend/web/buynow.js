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

            const type = 'cash';
            const addtocart_form_selector = "#product_addtocart_form";
            $(element).on('click', function(e) {
                e.preventDefault();

                var form = $(addtocart_form_selector);

                // TODO: Check if user changed the select options
                var formData = form.serialize();
                if (lastSelectedOptions !== formData) {
                    oklibpresenter.remove();
                }
                lastSelectedOptions = formData;

                // Check if options are valid
                var valid = form.validation('isValid');
                if (!valid) {
                    return;
                }

                if (!oklibpresenter.showExisting(type)) {
                    $.ajax({
                        showLoader: true,
                        url: '/oklib/ajax/buynow',
                        data: lastSelectedOptions,
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