/**
 * @package ImpressPages
 *
 */

(function($){
    "use strict";

    $(document).ready(function () {
        //$('.ipModuleInlineManagement').ipModuleInlineManagement();
        $('.ipModuleInlineManagement.ipmLogo').ipModuleInlineManagementLogo();
        $('.ipModuleInlineManagement.ipmString').ipModuleInlineManagementString();
        $('.ipModuleInlineManagement.ipmText').ipModuleInlineManagementText();
        $('.ipModuleInlineManagement.ipmImage').ipModuleInlineManagementImage();
    });
})(ip.jQuery);
