/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipWidget_IpPicture(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;


    function manageInit() {
        console.log('picture init');
        var instanceData = this.widgetObject.data('ipWidget');
        var options = new Object;
        var options = {
            curPicture : instanceData.data.picture,
            crop : true,
            aspectRatio: 500/60
        };
        this.widgetObject.find('.ipWidget_ipPicture_uploadPicture').ipUploadPicture(options);
        
        
    }

    function prepareData() {
        var data = Object();
        data.newPicture = this.widgetObject.find('.ipWidget_ipPicture_uploadPicture').ipUploadPicture('getCurPicture');
        data.title = this.widgetObject.find('.ipWidget_ipPicture_title').val();
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);        
    }



    

};

