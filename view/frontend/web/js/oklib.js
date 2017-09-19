/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    function OKDependencies(scripts) {

        var self = this;

        this.scripts = scripts;

        // when all the scripts are successfully loaded, trigger the callback
        var _globalScriptLoadSuccessHandler = function (callback) {
            var loaded = 0;
            for (var i = 0; i < self.scripts.length; i++) {
                if (self.scripts[i].loaded)
                    loaded++;
            }

            if (loaded == self.scripts.length && callback)
                callback();
        };

        // create a script element and load the file
        var _loadScript = function (script, callback) {
            var scriptElement = document.createElement('script');
            scriptElement.type = "text/javascript";
            scriptElement.src = script.url;
            scriptElement.addEventListener('load', function () {
                if (callback) callback();
            }, false);
            document.getElementsByTagName('head')[0].appendChild(scriptElement);
        }

        // load the scripts that the libraries depend on
        this.loadScripts = function (masterCallback) {
            for (var i = 0; i < self.scripts.length; i++) {
                var callback = function (script, callback) {
                    return function () {
                        script.loaded = true;
                        _globalScriptLoadSuccessHandler(callback);
                    }
                }(self.scripts[i], masterCallback);

                // if the script is not yet loaded, attempt to load it, else call the callback directly
                if (typeof window[self.scripts[i].reference] == 'undefined') {
                    _loadScript(self.scripts[i], callback);
                } else {
                    callback();
                }
            }
        };

        this.unloadScripts = function () {
            for (var i = 0; i < self.scripts.length; i++) {
                self.scripts[i].loaded = false;
            }
        };
    };

    var config = {
        baseOKUrl: "https://dev.okit.com"
    };

    var scripts = [
        { reference: 'oklib', url: config.baseOKUrl + '/js/oklib/dist/oklib.js', loaded: false }
    ];

    var dependencies = new OKDependencies(scripts);


    return oklib;
});
