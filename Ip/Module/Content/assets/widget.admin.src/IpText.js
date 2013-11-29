/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpText(widgetObject) {
    "use strict";
    this.widgetObject = widgetObject;

    this.init = function() {
        var instance = this;

        var instanceData = this.widgetObject.data('ipWidget');
        //this.widgetObject.find('textarea').tinymce(ipTinyMceConfigMin);

        //this.widgetObject.find('h1.ipwTitle').attr('contenteditable', 'true');

        var customTinyMceConfig = ipTinyMceConfigMin;
        customTinyMceConfig.setup = function(ed) {ed.on('change', function(e) {
            $.proxy(function(){
                    this.widgetObject.ipWidget('save');
                }, instance)();
            });
        };

        this.widgetObject.find('.ipsContent').tinymce(ipTinyMceConfigMin);
    }


    this.getSaveData = function() {
        var data = Object();

        data.text = this.widgetObject.find('.ipsContent').html();
        return data;

    }




};


