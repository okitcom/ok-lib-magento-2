define(
    [
        'ko',
        'uiComponent',
        'jquery',
        'mage/url'
    ],
    function (
        ko,
        Component,
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

                $.ajax({
                    showLoader: true,
                    url: '/oklib/ajax/cash',
                    data: "",
                    type: "GET",
                    dataType: 'json'
                }).done(function (data) {
                    window.$ = $;
                    window.oklib.init('t', data.guid, {
                        color: "dark",
                        culture: data.culture,
                        loaded: oklib.start,
                        initiation: data.initiation,
                        callback: function (status, guid) {
                            window.location = url.build("oklib/callback/cash") + "?q=" + data.guid;
                        }
                        //redirectUrl: "http://www.nu.nl/"
                        //redirectUrl: url.build("oklib/callback/cash") + "?q=" + data.guid
                    }, data.environment);
                });
            }
        });
    }
);