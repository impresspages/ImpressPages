
var ipDesignOpenOptions = function(e) {
    e.preventDefault();
    $('.ipModuleDesign .ipaPreview .ipaFrame').attr('src', ip.baseUrl + '?ipDesignPreview=1');
    $('.ipModuleDesign .ipaPreview').show();
}