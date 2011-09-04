/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipWidget_ipTextPhoto(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;
    this.uploadPhoto = uploadPhoto;

    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        
        this.widgetObject.find('.ipWidget_ipTextPhoto_uploadPhoto').bind('click', this.uploadPhoto);
        
        var uploader = new plupload.Uploader( {
            runtimes : 'gears,html5,flash,silverlight,browserplus',
            browse_button : 'ipWidget_ipTextPhoto_browseButton' + instanceData.instanceId,
            max_file_size : '100mb',            
            url : ipBaseUrl, //website root (available globaly in ImpressPages environment)
            multipart_params : {
                g : 'standard',
                m : 'content_management',
                a : 'widgetPost',
                widgetName : instanceData.name,
                instanceId : instanceData.instanceId,
                widgetAction : 'uploadPhoto'
            },
            
            
            flash_swf_url : '/plupload/js/plupload.flash.swf',
            silverlight_xap_url : '/plupload/js/plupload.silverlight.xap'
        });

        uploader.bind('Init', function(up, params) {
            console.log("Current runtime: " + params.runtime);
        });

        $('#uploadfiles').click(function(e) {
            uploader.start();
            e.preventDefault();
        });
        
        uploader.init();

        uploader.bind('FilesAdded', function(up, files) {
            $.each(files, function(i, file) {
                console.log('File added ' + file.id + ' ' + file.name + ' (' + plupload.formatSize(file.size) + ')');
            });
            up.refresh(); // Reposition Flash/Silverlight
            up.start();
        });

        uploader.bind('UploadProgress', function(up, file) {
            $('#' + file.id + " b").html(file.percent + "%");
        });

        uploader.bind('Error', function(up, err) {
            console.log("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : ""));
            up.refresh(); // Reposition Flash/Silverlight
        });

        uploader.bind('FileUploaded', function(up, file) {
            console.log(file.id + " 100%");
        });

        console.log('upload');        
        
    }

    function prepareData() {
        console.log('saving');

        var data = Object();

        data.text = $(this.widgetObject).find('textarea').first().val();
        console.log(this.widgetObject);
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }

    function uploadPhoto(event) {

    }
    
    
    

};

