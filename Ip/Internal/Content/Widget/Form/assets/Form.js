/**
 * @package ImpressPages
 *
 */


    var IpWidget_Form = function() {
        this.data = null;
        this.modal = null;
        this.container = null;
        this.addButton = null;
        this.confirmButton = null;
        this.$widgetOverlay = null;

        this.init = function($widgetObject, data) {
            var context = this;
            this.data = data;
            this.widgetObject = $widgetObject;


            this.$widgetOverlay = $('<div></div>');
            this.widgetObject.prepend(this.$widgetOverlay);
            this.$widgetOverlay.on('click', $.proxy(openPopup, this));

            $(document).on('ipWidgetResized', function () {
                $.proxy(fixOverlay, context)();
            });
            $(window).on('resize', function () {
                $.proxy(fixOverlay, context)();
            });
            $.proxy(fixOverlay, context)();

        };

        var fixOverlay = function () {
            this.$widgetOverlay
                .css('position', 'absolute')
                .css('z-index', 1000) // should be higher enough but lower than widget controls
                .width(this.widgetObject.width())
                .height(this.widgetObject.height());
        };

        var openPopup = function() {
            var context = this;
            this.modal = $('#ipWidgetFormPopup');
            this.addButton = this.modal.find(".ipsFieldAdd");
            this.container = this.modal.find('.ipWidget_ipForm_container');
            this.confirmButton = this.modal.find('.ipsConfirm');
            this.modal.modal();

            this.modal.on('hidden.bs.modal', $.proxy(cleanup, this));
            this.confirmButton.on('click', $.proxy(save, this));

            var instanceData = this.data;

            var options = {};
            if (instanceData['fields']) {
                options.fields = instanceData.fields;
            } else {
                options.fields = new Array();
            }

            options.fieldTemplate = this.modal.find('.ipsFieldTemplate');

            options.optionsPopup = $("#ipWidgetFormPopupOptions").ipWidget_ipForm_options({fieldTypes : instanceData.fieldTypes});
            this.container.ipWidget_ipForm_container('destroy');
            this.container.ipWidget_ipForm_container(options);


            this.addButton.on('click', $.proxy(addField, this));

            if (instanceData.success == null)  {
                instanceData.success = '';
            }
            this.modal.find('textarea[name=success]').val(instanceData.success);


            if (instanceData.sendTo == 'custom') {
                this.modal.find('select[name=sendTo]').val('custom');
                this.modal.find('.form-group.name-emails').show();
            } else {
                this.modal.find('select[name=sendTo]').val('default');
                this.modal.find('.form-group.name-emails').hide();
            }
            this.modal.find('select[name=sendTo]').on('change', function() {
                if ($(this).val() == 'custom') {
                    context.modal.find('.form-group.name-emails').show();
                } else {
                    context.modal.find('.form-group.name-emails').hide();
                }
            })

            this.modal.find('input[name=buttonText]').val(instanceData.buttonText);


            this.modal.find('input[name=emails]').val(instanceData.emails);

            this.modal.find('.ipsTabs li a').first().click();
            ipInitForms();
        };

        var cleanup = function() {
            this.container.html('');
            this.container.ipWidget_ipForm_container('destroy');
            this.addButton.off();
            this.confirmButton.off();
        };

        var addField = function (e) {
            this.container.ipWidget_ipForm_container('addField');

            var $backdrop = this.modal.children('.modal-backdrop');
            var $dialog = this.modal.children('.modal-dialog');

            if(this.modal.height() < $dialog.outerHeight(true)) {
                $backdrop.css('height', 0).css('height', $dialog.outerHeight(true));
            }
        };

        var save = function(e) {
            var data = this.getData();
            this.widgetObject.save(data, 1);
            this.modal.modal('hide');
        };

        this.getData = function() {
            var data = Object();

            data.fields = [];
            var $fields = this.container.ipWidget_ipForm_container('getFields');
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

            data.success = this.modal.find('textarea[name=success]').val();

            data.sendTo =this.modal.find('select[name=sendTo]').val();
            data.emails = this.modal.find('input[name=emails]').val();
            data.buttonText = this.modal.find('input[name=buttonText]').val();

            return data;
        };
    };



