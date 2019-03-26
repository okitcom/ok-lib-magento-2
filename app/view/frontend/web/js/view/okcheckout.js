define(
    [
        'ko',
        'uiComponent',
        'jquery',
        'mage/url',
        'Okitcom_OkLibMagento/oklibpresenter'
    ],
    function (
        ko,
        Component,
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
        window.okCashStarted = false;

        return Component.extend({
            defaults: {
                template: 'Okitcom_OkLibMagento/okcheckout'
            },

            //add here your logic to display step,
            isVisible: ko.observable(true),

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();
                // register your step
                return this;
            },

            /**
             * The navigate() method is responsible for navigation between checkout step
             * during checkout. You can add custom logic, for example some conditions
             * for switching to your custom step
             */
            open: function () {

                var type = 'cash';
                if (oklibpresenter.isInitialized(type)) {
                    oklibpresenter.showExisting(type);
                } else {

                    $(this).addClass("ok-button-progress");

                    $.ajax({
                        showLoader: true,
                        url: '/oklib/ajax/cash',
                        data: "",
                        type: "GET",
                        dataType: 'json'
                    }).done(function (data) {
                        $(this).removeClass("ok-button-progress");
                        oklibpresenter.showNew(type, data);
                    }).error(function(err, data) {
                        $(element).removeClass("ok-button-progress");
                    });
                }
            }
        });
    }
);