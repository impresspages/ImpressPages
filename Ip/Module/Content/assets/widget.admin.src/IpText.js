/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpText(widgetObject) {
    "use strict";
    this.widgetObject = widgetObject;


    var saveInterval = null;

    this.manageInit = function() {

        var instanceData = this.widgetObject.data('ipWidget');
        //this.widgetObject.find('textarea').tinymce(ipTinyMceConfigMin);

        //this.widgetObject.find('h1.ipwTitle').attr('contenteditable', 'true');

var customTinyMceConfig = ipTinyMceConfigMin;
        customTinyMceConfig.setup = function(ed) {ed.on('change', function(e) {
            //console.log('change ' + time() );

//                console.log('the event object '+e);
//                console.log('the editor object '+ed);
//                console.log('the content '+ed.getContent());
            });
            };

        this.widgetObject.find('.ipsContent').tinymce(ipTinyMceConfigMin);

//setInterval($.proxy(prepareData, this), 3000);



            //$('.ipWidget-IpText .ipsContent').tinymce(ipTinyMceConfigMin);

    }

    this.focusIn = function()
    {
        console.log('focusin');
    }



    this.focusOut = function()
    {
        //clearInterval(saveInterval);
        console.log('focusout');
    }


    this.getSaveData = function() {
        console.log('IpText save');
        var data = Object();

        data.text = this.widgetObject.find('.ipsContent').html();
        return data;

    }




};


