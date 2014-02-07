/**
 * @package ImpressPages
 *
 */
var IpWidget_File;

(function($) {
    IpWidget_File = function() {
        this.widgetObject = null;
        this.filesSelected = null;

        this.init = function($widgetObject, data) {
            this.data = data;
            this.widgetObject = $widgetObject;

            var $widgetOverlay = $('<div></div>')
                .css('position', 'absolute')
                .css('z-index', 5)
                .width(this.widgetObject.width())
                .height(this.widgetObject.height());
            this.widgetObject.prepend($widgetOverlay);
            $widgetOverlay.on('click', $.proxy(openPopup, this));
        };

        var openPopup = function() {
            this.modal = $('#ipWidgetFilePopup');
            this.addButton = this.modal.find(".ipsFieldAdd");
            this.container = this.modal.find('.ipWidget_ipForm_container');
            this.confirmButton = this.modal.find('.ipsConfirm');
            this.modal.modal();

            var instanceData = this.data;

            var uploader = this.modal.find('.ipsUpload');
            var options = new Object;
            uploader.ipUploadFile(options);

            var container = this.modal.find('.ipWidget_ipFile_container');
            var options = new Object;
            if (instanceData.data.files) {
                options.files = instanceData.data.files;
            } else {
                options.files = new Array();
            }
            options.fileTemplate = this.modal.find('.ipsFileTemplate');
            container.ipWidget_ipFile_container(options);

            this.modal.bind('filesSelected.ipUploadFile', this.filesSelected);
            this.modal.bind('error.ipUploadFile', this.addError);

            var widgetObject = this.widgetObject;
            this.modal.find('.ipmBrowseButton').click(function(e){
                e.preventDefault();
                var repository = new ipRepository({preview: 'list'});
                repository.bind('ipRepository.filesSelected', $.proxy(fileUploaded, widgetObject));
            });
        };

//
//        function addError(event, errorMessage) {
//            $(this).trigger('error.ipContentManagement', [errorMessage]);
//        }
//
//        function fileUploaded(event, files) {
//            /* we are in widgetObject context */
//            var $this = $(this);
//
//            var container = $this.find('.ipWidget_ipFile_container');
//            for(var index in files) {
//                container.ipWidget_ipFile_container('addFile', files[index].fileName, files[index].fileName, 'new');
//            }
//        }
//
//        function prepareData() {
//            var data = Object();
//            var container = this.widgetObject.find('.ipWidget_ipFile_container');
//
//            data.files = new Array();
//            var $files = container.ipWidget_ipFile_container('getFiles');
//            $files.each(function(index) {
//                var $this = $(this);
//                var tmpFile = new Object();
//                tmpFile.title = $this.ipWidget_ipFile_file('getTitle');
//                tmpFile.fileName = $this.ipWidget_ipFile_file('getFileName');
//                tmpFile.status = $this.ipWidget_ipFile_file('getStatus');
//                data.files.push(tmpFile);
//
//            });
//
//            $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
//        }

    };

})(ip.jQuery);









