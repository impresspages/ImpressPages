/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipWidget_ipTextPicture(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;
    this.uploadPicture = uploadPicture;

    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        
        var options = {
            crop: true,
            aspectRatio: 500/60
        };
        
        //this.widgetObject.find('.ipWidget_ipTextPhoto_uploadPicture').ipWidgetPhotoUpload(options);
        
        
    }

    function prepareData() {

        var data = Object();

        data.text = $(this.widgetObject).find('textarea').first().val();
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }

    function fileUploaded(event) {

    }
    
    function uploadPicture() {
        
    }
    
    

};

