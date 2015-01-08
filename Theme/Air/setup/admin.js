var ipTinyMceConfigOriginalFunction = ipTinyMceConfig; //ipTinyMceConfig is defined in the core and holds default TinyMCE text editor settings
var ipTinyMceConfig = function () {
    var customizedConfig = ipTinyMceConfigOriginalFunction();

    customizedConfig.style_formats.push({title: 'Button2', inline: 'span', classes: 'button2'});

    return customizedConfig;
};
