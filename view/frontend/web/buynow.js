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

            var started = false;
            $(element).on('click', function(e) {
                e.preventDefault();

                if (started) {
                    window.oklib.show();
                } else {
                    started = true;

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
                        window.$ = $;
                        window.oklib.init('t', data.guid, {
                            color: "dark",
                            culture: data.culture,
                            initiation: data.initiation,
                            loaded: oklib.start,
                            callback: function (status, guid) {
                                window.location = url.build("oklib/callback/cash") + "?q=" + data.guid;
                            }
                        }, data.environment);
                    });
                }
            });


        };
    }
);