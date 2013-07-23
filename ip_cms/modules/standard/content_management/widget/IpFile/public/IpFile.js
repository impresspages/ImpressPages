/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpFile(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;
    this.fileUploaded = fileUploaded;

    this.addError = addError;

    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        
        var uploader = this.widgetObject.find('.ipaUpload');
        var options = new Object;
        uploader.ipUploadFile(options);
        
        var container = this.widgetObject.find('.ipWidget_ipFile_container');
        var options = new Object;
        if (instanceData.data.files) {
            options.files = instanceData.data.files;
        } else {
            options.files = new Array();
        }
        options.fileTemplate = this.widgetObject.find('.ipaFileTemplate');
        container.ipWidget_ipFile_container(options);
        
        
        this.widgetObject.bind('fileUploaded.ipUploadFile', this.fileUploaded);
        this.widgetObject.bind('error.ipUploadFile', this.addError);

        var widgetObject = this.widgetObject;
        this.widgetObject.find('.ipmBrowseButton').click(function(e){
            e.preventDefault();
            var repository = new ipRepository({preview: 'list'});
            repository.bind('ipRepository.filesSelected', $.proxy(fileUploaded, widgetObject));
        });
        
    }
    
    function addError(event, errorMessage) {
        $(this).trigger('error.ipContentManagement', [errorMessage]);
    }

    
    function fileUploaded(event, files) {
        /* we are in widgetObject context */
        var $this = $(this);

        var container = $this.find('.ipWidget_ipFile_container');
        for(var index in files) {
            container.ipWidget_ipFile_container('addFile', files[index].file, files[index].fileName, 'new');
        }
    }
    

    
    function prepareData() {
        var data = Object();
        var container = this.widgetObject.find('.ipWidget_ipFile_container');
        
        data.files = new Array();
        var $files = container.ipWidget_ipFile_container('getFiles');
        $files.each(function(index) {
            var $this = $(this);
            var tmpFile = new Object();
            tmpFile.title = $this.ipWidget_ipFile_file('getTitle');
            tmpFile.fileName = $this.ipWidget_ipFile_file('getFileName');
            tmpFile.status = $this.ipWidget_ipFile_file('getStatus');
            data.files.push(tmpFile);

        });


        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }


};




(function($) {

    var methods = {
            
    init : function(options) {

        return this.each(function() {

            var $this = $(this);

            var data = $this.data('ipWidget_ipFile_container');

            // If the plugin hasn't been initialized yet
            var files = null;
            if (options.files) {
                files = options.files;
            } else {
                files = new Array();
            }
            
            if (!data) {
                $this.data('ipWidget_ipFile_container', {
                    files : files,
                    fileTemplate : options.fileTemplate
                });
                
                for (var i in files) {
                    $this.ipWidget_ipFile_container('addFile', files[i]['fileName'], files[i]['title'], 'present'); 
                }
                $this.bind('removeFile.ipWidget_ipFile', function(event, fileObject) {
                    var $fileObject = $(fileObject);
                    $fileObject.ipWidget_ipFile_container('removeFile', $fileObject);
                });
                
                $( ".ipWidget_ipFile_container" ).sortable();
                $( ".ipWidget_ipFile_container" ).sortable('option', 'handle', '.ipaFileMove');
                

            }
        });
    },
    
    addFile : function (fileName, title, status) {
        var $this = this;
        var $newFileRecord = $this.data('ipWidget_ipFile_container').fileTemplate.clone();
        $newFileRecord.ipWidget_ipFile_file({'status' : status, 'fileName' : fileName, 'title' : title});
        
        $this.append($newFileRecord);
        
    },
    
    removeFile : function ($fileObject) {
        $fileObject.hide();
        $fileObject.ipWidget_ipFile_file('setStatus', 'deleted');
        
    },
    
    getFiles : function () {
        var $this = this;
        return $this.find('.ipaFileTemplate');
    }



    };

    $.fn.ipWidget_ipFile_container = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);





(function($) {

    var methods = {
    init : function(options) {

        return this.each(function() {

            var $this = $(this);

            var data = $this.data('ipWidget_ipFile_file');

            
            // If the plugin hasn't been initialized yet
            if (!data) {
                var data = {
                    title : '',
                    fileName : '',
                    status : 'new'
                };
                
                if (options.title) {
                    data.title = options.title;
                }
                if (options.fileName) {
                    data.fileName = options.fileName;
                }
                if (options.status) {
                    data.status = options.status;
                }
                
                $this.data('ipWidget_ipFile_file', {
                    title : data.title,
                    fileName : data.fileName,
                    status : data.status
                });
                $this.find('.ipaFileTitle').val(data.title);
            }
            
            $this.find('.ipaFileLink').attr('href', ip.baseUrl + data.fileName);
            $this.find('.ipaFileRemove').bind('click', function(event){
                event.preventDefault();
                $this = $(this);
                $this.trigger('removeClick.ipWidget_ipFile');
            });
            $this.bind('removeClick.ipWidget_ipFile', function(event) {
                $this.trigger('removeFile.ipWidget_ipFile', this);
            });
            return $this;
        });
    },
    
    getTitle : function() {
        var $this = this;
        return $this.find('.ipaFileTitle').val();
    },
    
    getFileName : function() {
        var $this = this;
        var tmpData = $this.data('ipWidget_ipFile_file');
        return tmpData.fileName;
    },
        
    getStatus : function() {
        var $this = this;
        var tmpData = $this.data('ipWidget_ipFile_file');
        return tmpData.status;
    },
    
    setStatus : function(newStatus) {
        var $this = $(this);
        var tmpData = $this.data('ipWidget_ipFile_file');
        tmpData.status = newStatus;
        $this.data('ipWidget_ipFile_file', tmpData);
        
    }
    



    };

    $.fn.ipWidget_ipFile_file = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);

