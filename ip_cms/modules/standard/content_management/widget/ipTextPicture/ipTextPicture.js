/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipWidget_IpTextPicture(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;

    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        var options = new Object;

        if (instanceData.data.pictureOriginal) {
            options.picture = instanceData.data.pictureOriginal;
        }
        if (instanceData.data.cropX1) {
            options.cropX1 = instanceData.data.cropX1;
        }
        if (instanceData.data.cropY1) {
            options.cropY1 = instanceData.data.cropY1;
        }
        if (instanceData.data.cropX2) {
            options.cropX2 = instanceData.data.cropX2;
        }
        if (instanceData.data.cropY2) {
            options.cropY2 = instanceData.data.cropY2;
        }
        
        options.changeHeight = true;
        options.changeWidth = false;
        

        this.widgetObject.find('.ipWidget_ipTextPicture_uploadPicture').ipUploadPicture(options);
        
        
        this.widgetObject.find('textarea').tinymce(ipTinyMceConfigMin);
    }

    function prepareData() {

        var data = Object();

        data.text = $(this.widgetObject).find('textarea').first().val();
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }


    

};

