/**
 * setup/admin.js file is automatically included into the website but only in admin mode.
 * Add any code you need to be executed in admin. The most common case is to add additional styles to the TinyMCE editor config.
 */


//change default TinyMCE editor configuration. Please check TinyMCE project website for more options.
var ipTinyMceConfigOriginalFunction = ipTinyMceConfig; //ipTinyMceConfig is defined in the core and holds default TinyMCE text editor settings
var ipTinyMceConfig = function () {
    var customizedConfig = ipTinyMceConfigOriginalFunction();

    //adding Button2 style to the existing styles
    //more details on TinyMCE website http://www.tinymce.com/wiki.php/Configuration:style_formats
    customizedConfig.style_formats.push({title: 'Button2', inline: 'span', classes: 'button2'});

    //replace any other default setting
    //customizedConfig.configName = 'customValue';

    return customizedConfig;
};
