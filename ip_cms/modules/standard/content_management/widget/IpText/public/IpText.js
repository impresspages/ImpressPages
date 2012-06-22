/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

function IpWidget_IpText(widgetObject) {
    this.widgetObject = widgetObject;

    this.manageInit = manageInit;
    this.prepareData = prepareData;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        this.widgetObject.find('textarea').tinymce(ipTinyMceConfigMin);
    }

    function prepareData() {

        var data = Object();

        data.text = this.widgetObject.find('textarea').html();
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }

    

};

      

/**
 * Make links inside widgets preserve management state
 */
(function($) {
	ipAddGetParameterToLink('#ipBlock-main .ipWidget-IpText', "^='" + ip.baseUrl + "'", 'cms_action', 'manage');
})(jQuery);
