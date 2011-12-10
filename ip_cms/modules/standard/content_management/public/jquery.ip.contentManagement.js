/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */


(function($) {

    var methods = {
        init : function(options) {


            return this.each(function() {

                var $this = $(this);
                
                var data = $this.data('ipContentManagement');
            
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $(this).trigger('initStarted.ipContentManagement');
 
                    $this.data('ipContentManagement', {
                        saveJobs : Object(),
                    }); 
                    
                    
                    if ($(".ipAdminPanelContainer").length == 0) {
                        var $controlsBgDiv = $('<div class="ipAdminPanelContainer" />');
                        $('body').prepend($controlsBgDiv);
                    }
                
                    data = Object();
                    data.g = 'standard';
                    data.m = 'content_management';
                    data.a = 'initManagementData';
            
                    $.ajax({
                        type : 'POST',
                        url : document.location,
                        data : data,
                        context : $this,
                        success : methods.initResponse,
                        dataType : 'json'
                    });
                    
                    

                }
            });
        },
        


        // ********INIT*********

        initResponse : function(response) {
            return this.each(function() {
                if (response.status == 'success') {
                    var $this = $(this);
                    $('body').prepend(response.controlPanelHtml);
                    $('body').append(response.saveProgressHtml);

                    var options = new Object;
                    options.zoneName = ip.zoneName;
                    options.pageId = ip.pageId;
                    options.revisionId = ip.revisionId;
                    options.widgetControlsHtml = response.widgetControlsHtml;
                    options.contentManagementObject = $this;
                    options.manageableRevision = response.manageableRevision;

                    $('.ipAdminWidgetButtonSelector').ipAdminWidgetButton();
                    
                    $('.ipaOptions').bind('click', function(event){event.preventDefault();$(this).trigger('pageOptionsClick.ipContentManagement');});

                    $('.ipActionSave').bind('click', function(event){event.preventDefault();$(this).trigger('savePageClick.ipContentManagement');});
                    $('.ipActionPublish').bind('click', function(event){event.preventDefault();$(this).trigger('publishClick.ipContentManagement');});

                    $this.bind('savePageClick.ipContentManagement', function(event){$(this).ipContentManagement('saveStart');});
                    $this.bind('publishClick.ipContentManagement', function(event){$(this).ipContentManagement('publish');});

                    $this.bind('addSaveJob.ipContentManagement', function(event, jobName, saveJobObject){$(this).ipContentManagement('addSaveJob', jobName, saveJobObject);});

                    $this.bind('removeSaveJob.ipContentManagement', function(event, jobName){$(this).ipContentManagement('removeSaveJob', jobName);});

                    $this.bind('saveCancel.ipContentManagement', function(event){$(this).ipContentManagement('saveCancel');});
                    
                    $this.bind('pageOptionsClick.ipContentManagement', function(event){$(this).ipContentManagement('pageOptions');});

                    $this.trigger('initFinished.ipContentManagement', options);
                }
            });
        },

        // *********PAGE OPTIONS***********//
        
        pageOptions : function() {
            return this.each(function() {
                var $this = $(this);
                if ($('.ipaOptionsDialog').length) {
                    $this.find('.ipaOptionsDialog').dialog();
                } else {
                    $('.ipaOptions').append('<div class="ipaOptionsDialog" style="display: none;"><div class="ipaOptionsDialogForm"></div></div>');
                    $('.ipaOptionsDialogForm').ipPageOptions();
                    $('.ipaOptionsDialogForm').ipPageOptions('getData', ip.pageId, ip.zoneName);
                    $('.ipaOptionsDialog').dialog();
                }
                
            });
        },
        
        // *********SAVE**********//
        
        saveStart : function() {
            return this.each(function() {
                var $this = $(this);

                $( "#ipSaveProgress" ).dialog({
                    height: 140,
                    modal: true,
                    close: function(event, ui) { $(this).trigger('saveCancel.ipContentManagement'); }
                });
                
                $( "#ipSaveProgress .ipMainProgressbar" ).progressbar({
                    value: 0
                });
                
                
                var tmpData = $this.data('ipContentManagement');
                tmpData.saving = true;
                $this.data('ipContentManagement', tmpData);
                
                
                $this.trigger('pageSaveStart.ipContentManagement');
                var jobsCount = 0;
                for (var prop in $this.data('ipContentManagement').saveJobs) {
                    jobsCount++;
                }
                if (jobsCount == 0) {
                    $this.ipContentManagement('saveFinish'); // initiate save finishing action
                } else {
                    // wait for jobs to finish
                }
        
            });
     
        },
        
        saveCancel : function() {
            var $this = $(this);
            var tmpData = $this.data('ipContentManagement');
            tmpData.saving = false;
            $this.data('ipContentManagement', tmpData);
            $( "#ipSaveProgress" ).dialog('close');
        },
        
        saveFinish : function() {
            return this.each(function() {

                
                
                var $this = $(this);
                
                if (!$this.data('ipContentManagement').saving) {
                    return;
                }
                
                
                data = Object();
                data.g = 'standard';
                data.m = 'content_management';
                data.a = 'savePage';
                data.revisionId = ip.revisionId;


                refreshLocation = document.location
                
                $.ajax({
                    type : 'POST',
                    url : document.location,
                    data : data,
                    context : $this,
                    success : methods._savePageResponse,
                    dataType : 'json'
                });                     
            });
        },
        
        _savePageResponse: function(response) {
            if (response.status == 'success') {
                window.location.href = response.newRevisionUrl;
            } else {
                var tmpData = $this.data('ipContentManagement');
                tmpData.saving = false;
                $this.data('ipContentManagement', tmpData);
                                
                // show error
                $( "#ipSaveProgress" ).dialog('close');
            }
        },
        
        addSaveJob : function (jobName, saveJobObject) {
            return this.each(function() {  
                var $this = $(this);    
                $this.data('ipContentManagement').saveJobs[jobName] = saveJobObject;
                $this.ipContentManagement('_displaySaveProgress');
            });
        },

        removeSaveJob : function (jobName) {
            return this.each(function() {  
                var $this = $(this);
                
                var tmpData = $this.data('ipContentManagement'); 
                delete tmpData.saveJobs[jobName];
                $this.data('ipContentManagement', tmpData);

                $this.ipContentManagement('_displaySaveProgress');
                
                var jobsCount = 0;
                for (var prop in $this.data('ipContentManagement').saveJobs) {
                    jobsCount++;
                }

                if (jobsCount == 0) {
                    $this.ipContentManagement('saveFinish'); // initiate save finishing action
                } else {
                    // wait for other jobs to finish
                }
            });
        },        
    
        publish : function(event){
            return this.each(function() {  
                var $this = $(this);
                
                data = Object();
                data.g = 'standard';
                data.m = 'content_management';
                data.a = 'publishPage';
                data.revisionId = ip.revisionId;

                $.ajax({
                    type : 'POST',
                    url : document.location,
                    data : data,
                    context : $this,
                    success : methods._publishPageResponse,
                    dataType : 'json'
                });                      
            });
        },
        
        
        _publishPageResponse : function (response) {
            if (response.status == 'success') {
                window.location.href = response.newRevisionUrl;
            } else {
                // show error
            }            
        },

        _displaySaveProgress : function () {
            return this.each(function() {
                var $this = $(this);
                var percentage = 0;
                
                var timeLeft = 0;
                var timeSpent = 0;
                var progress = 0;
                
                var saveJobs = $(this).data('ipContentManagement').saveJobs;
    
                
                for (var i in saveJobs) {
                    var curJob = saveJobs[i];
                    timeLeft = timeLeft + curJob.getTimeLeft();
                    timeSpent = timeSpent + curJob.getTimeLeft() / (1 - curJob.getProgress()) * curJob.getProgress();                    
                }
                
                var overallProgress = timeSpent / (timeLeft + timeSpent);
                
                $( "#ipSaveProgress .ipMainProgressbar" ).progressbar('value', overallProgress*100);
                
                // console.log('Time spent ' + timeSpent + ' TimeLeft ' + timeLeft );
            });
        }

        // *********END SAVE*************//
        
    };
    
    

    $.fn.ipContentManagement = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }


    };
    
   

})(jQuery);