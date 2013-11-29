/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpText() {
    "use strict";

    this.init = function($widgetObject) {
        var customTinyMceConfig = ipTinyMceConfigMin;
        customTinyMceConfig.setup = function(ed, l) {ed.on('change', function(e) {
            $widgetObject.save({text: $widgetObject.find('.ipsContent').html()});
        })};

        $widgetObject.find('.ipsContent').tinymce(ipTinyMceConfigMin);
    };


};


