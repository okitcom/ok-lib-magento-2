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

            const type = 'cash';
            $(element).on('click', function(e) {
                e.preventDefault();

                if (!oklibpresenter.showExisting(type)) {
                    var qtyObj = $("#product_addtocart_form #qty");
                    var qty = config.qty;
                    if (qtyObj !== 'undefined') {
                        qty = qtyObj.val();
                    }

                    $.ajax({
                        showLoader: true,
                        url: '/oklib/ajax/buynow',
                        data: {
                            product_id: config.product_id,
                            qty: qty
                        },
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