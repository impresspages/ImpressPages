
var ipDesignOpenOptions = function(e) {
    e.preventDefault();
    $('.ipModuleDesign .ipsPreview .ipaFrame').attr('src', ip.baseUrl + '?ipDesignPreview=1');
    $('.ipModuleDesign .ipsPreview').show();
}