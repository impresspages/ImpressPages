/**
 * @package ImpressPages
 *
 */

IpWidget_File = function () {
    "use strict";
    this.widgetObject = null;
    this.filesSelected = null;

    this.init = function ($widgetObject, data) {
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

    this.onAdd = function (e) {
        $.proxy(openPopup, this)();
        this.modal.find('.ipsUpload').click();
    };


    var openPopup = function () {
        this.modal = $('#ipWidgetFilePopup');
        this.addButton = this.modal.find(".ipsFieldAdd");
        this.container = this.modal.find('.ipWidget_ipForm_container');
        this.confirmButton = this.modal.find('.ipsConfirm');
        this.modal.modal();
        var context = this;


        var container = this.modal.find('.ipWidget_ipFile_container');
        var options = new Object;
        if (this.data.files) {
            options.files = this.data.files;
        } else {
            options.files = [];
        }
        options.fileTemplate = this.modal.find('.ipsFileTemplate');
        container.ipWidget_ipFile_container('destroy');
        container.ipWidget_ipFile_container(options);
        this.confirmButton.off().on('click', $.proxy(save, this));

        var widgetObject = this.widgetObject;
        this.modal.find('.ipsUpload').click(function (e) {
            e.preventDefault();
            var repository = new ipRepository({preview: 'list'});
            repository.bind('ipRepository.filesSelected', $.proxy(fileUploaded, context));
        });
    };


    var fileUploaded = function (event, files) {
        var container = this.modal.find('.ipWidget_ipFile_container');
        for (var index in files) {
            container.ipWidget_ipFile_container('addFile', files[index].fileName, files[index].fileName, 'new');
        }
    };

    var save = function () {
        var data = Object();
        var container = this.modal.find('.ipWidget_ipFile_container');

        data.files = new Array();
        var $files = container.ipWidget_ipFile_container('getFiles');
        var notDeletedCount = 0;
        $files.each(function (index) {
            var $this = $(this);
            var tmpFile = {};
            tmpFile.title = $this.ipWidget_ipFile_file('getTitle');
            tmpFile.fileName = $this.ipWidget_ipFile_file('getFileName');
            tmpFile.status = $this.ipWidget_ipFile_file('getStatus');
            data.files.push(tmpFile);
            if (tmpFile.status != 'deleted') {
                notDeletedCount++;
            }
        });


        if (notDeletedCount == 0) {
            //remove the whole widget
            this.modal.modal('hide');
            ipContent.deleteWidget(this.widgetObject.data('widgetid'));
            return;
        }

        this.widgetObject.save(data, true);
        this.modal.modal('hide');
    }

};







