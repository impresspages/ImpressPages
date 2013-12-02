/**
 * @package ImpressPages
 *
 *
 */


/**
 * Widget initialization
 */
function IpWidget_IpForm() {

    this.data = null;
    this.modal = null;
    this.container = null;
    this.addButton = null;


    this.init = function($widgetObject, data) {
        this.data = data;
        this.widgetObject = $widgetObject;
        $widgetObject.on('click', $.proxy(openPopup, this));
    };



    var openPopup = function ()
    {
        this.modal = $('#ipWidgetFormPopup');
        this.addButton = this.modal.find(".ipaFieldAdd");
        this.container = this.modal.find('.ipWidget_ipForm_container');
        this.modal.modal({});

        this.modal.on('hidden.bs.modal', $.proxy(cleanup, this));

        var instanceData = this.data;

        var options = new Object;
        if (instanceData['fields']) {
            options.fields = instanceData.fields;
        } else {
            options.fields = new Array();
        }

        options.fieldTemplate = this.modal.find('.ipaFieldTemplate');

        options.optionsPopup = this.modal.find(".ipaFieldOptionsPopup").ipWidget_ipForm_options({fieldTypes : instanceData.fieldTypes});
        this.container.ipWidget_ipForm_container(options);


        this.addButton.on('click', $.proxy(addField, this));
        var customTinyMceConfig = ipTinyMceConfigMin;
        customTinyMceConfig.height = 100;
        this.modal.find(".ipWidgetIpFormSuccess").tinymce(customTinyMceConfig);
    };


    var cleanup = function() {
        this.container.html('');
        this.addButton.off();
    }
    
    var addField = function (e) {

        this.container.ipWidget_ipForm_container('addField');
    };
    

    
    var prepareData = function() {
        var data = Object();
        var container = this.widgetObject.find('.ipWidget_ipForm_container');

        data.fields = new Array();
        var $fields = container.ipWidget_ipForm_container('getFields');
        $fields.each(function(index) {
            var $this = $(this);
            var tmpField = new Object();
            tmpField.label = $this.ipWidget_ipForm_field('getLabel');
            tmpField.type = $this.ipWidget_ipForm_field('getType');
            tmpField.options = $this.ipWidget_ipForm_field('getOptions');
            if ($this.ipWidget_ipForm_field('getRequired')) {
                tmpField.required = 1;
            } else {
                tmpField.required = 0;
            }
            var status = $this.ipWidget_ipForm_field('getStatus');
            if (status != 'deleted') {
                data.fields.push(tmpField);
            }

        });

        data.success = this.widgetObject.find('.ipWidgetIpFormSuccess').html();
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    };


};




