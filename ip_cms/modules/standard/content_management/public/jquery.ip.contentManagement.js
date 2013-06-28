/**
 * @package ImpressPages
 *
 *
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
                        optionsChanged : false,
                        postUrl : document.location
                    });
                    var data = $this.data('ipContentManagement');
                    
                    
                    if ($(".ipAdminPanelContainer").length == 0) {
                        var $controlsBgDiv = $('<div class="ipAdminPanelContainer" />');
                        $('body').prepend($controlsBgDiv);
                    }
                
                    var postData = Object();
                    postData.g = 'standard';
                    postData.m = 'content_management';
                    postData.a = 'initManagementData';
            
                    $.ajax({
                        type : 'POST',
                        url : data.postUrl,
                        data : postData,
                        context : $this,
                        success : methods.initResponse,
                        dataType : 'json'
                    });
                    
                    

                }

                $this.bind('initFinished.ipContentManagement', $.proxy(methods.initBlocks, $this));
                $this.bind('pageSaveStart.ipContentManagement', $.proxy(methods.saveBlocksStart, $this));


            });
        },
        


        // ********INIT*********

        initResponse : function(response) {
            return this.each(function() {
                if (response.status == 'success') {
                    var $this = $(this);
                    $('body').prepend(response.saveProgressHtml);
                    $('body').prepend(response.controlPanelHtml);

                    var options = new Object;
                    options.zoneName = ip.zoneName;
                    options.pageId = ip.pageId;
                    options.revisionId = ip.revisionId;
                    options.widgetControlsHtml = response.widgetControlsHtml;
                    options.contentManagementObject = $this;
                    options.manageableRevision = response.manageableRevision;

                    var data = $this.data('ipContentManagement');
                    data.initInfo = options;
                    $this.data('ipContentManagement', data);

                    $('.ipActionWidgetButton').ipAdminWidgetButton();
                    
                    $('.ipaOptions').bind('click', function(event){event.preventDefault();$(this).trigger('pageOptionsClick.ipContentManagement');});

                    $('.ipActionSave').bind('click', function(event){event.preventDefault();$(this).trigger('savePageClick.ipContentManagement');});
                    $('.ipActionPublish').bind('click', function(event){event.preventDefault();$(this).trigger('publishClick.ipContentManagement');});

                    $this.bind('savePageClick.ipContentManagement', function(event){$(this).ipContentManagement('saveStart');});
                    $this.bind('publishClick.ipContentManagement', function(event){$(this).ipContentManagement('publishStart');});

                    $this.bind('addSaveJob.ipContentManagement', function(event, jobName, saveJobObject){$(this).ipContentManagement('addSaveJob', jobName, saveJobObject);});

                    $this.bind('removeSaveJob.ipContentManagement', function(event, jobName){$(this).ipContentManagement('removeSaveJob', jobName);});

                    $this.bind('saveCancel.ipContentManagement', function(event){$(this).ipContentManagement('saveCancel');});
                    
                    $this.bind('pageOptionsClick.ipContentManagement', function(event){$(this).ipContentManagement('openPageOptions');});

                    $this.bind('pageOptionsConfirm.ipPageOptions', methods._optionsConfirm);
                    $this.bind('pageOptionsCancel.ipPageOptions', methods._optionsCancel);
                    //$this.bind('dialogclose', methods._optionsCancel);
                    
                    $this.bind('error.ipContentManagement', function (event, error){$(this).ipContentManagement('addError', error);});
                    
                    $this.trigger('initFinished.ipContentManagement', options);
                }
            });
        },


        saveBlocksStart : function() {
            var $this = this;
            $('.ipBlock').ipBlock('pageSaveStart');
        },

        initBlocks : function() {
            var $this = this;
            var data = $this.data('ipContentManagement');
            var options = data.initInfo;
            if (options.manageableRevision) {
                $('.ipBlock').ipBlock(options);
            }

        },
        
        addError : function (errorMessage) {
            var $newError = $('.ipAdminErrorSample .ipAdminError').clone();
            $newError.text(errorMessage);
            $('.ipAdminErrorContainer').append($newError);
            $newError.animate( {opacity: "100%"}, 6000)
            .animate( { queue: true, opacity: "0%" }, { duration: 3000, complete: function(){$(this).remove();}});
        },
        // *********PAGE OPTIONS***********//
        
        openPageOptions : function() {
            return this.each(function() {
                var $this = $(this);
                if ($('.ipaOptionsDialog').length) {
                    
                    $this.find('.ipaOptionsDialog').dialog('open');
                } else {
                    $('.ipAdminPanel').append('<div class="ipaOptionsDialog" style="display: none;"></div>');
                    $('.ipaOptionsDialog').dialog({width: 600, height : 450, modal: true});
                    $('.ipaOptionsDialog').ipPageOptions();
                    $('.ipaOptionsDialog').ipPageOptions('refreshPageData', ip.pageId, ip.zoneName);
                }
                
            });
        },
        
        _optionsConfirm : function (event){
            var $this = $(this);
            var data = $this.data('ipContentManagement');
            
            var postData = Object();
            postData.g = 'standard';
            postData.m = 'content_management';
            postData.a = 'savePageOptions';
            postData.pageOptions = $('.ipaOptionsDialog').ipPageOptions('getPageOptions');
            postData.revisionId = ip.revisionId;

            $.ajax({
                type : 'POST',
                url : data.postUrl,
                data : postData,
                context : $this,
                success : methods._savePageOptionsResponse,
                dataType : 'json'
            });

        },
        
        _savePageOptionsResponse : function (response) {
            $this = this;
            if (response.status == 'success') {
                $('.ipaOptionsDialog').remove();
                if (response.newUrl && response.newUrl != '') {
                    var data = $this.data('ipContentManagement');
                    data.postUrl = response.newUrl;
                    $this.data('ipContentManagement', data);
                    $('a[href="' + response.oldUrl + '"]').attr('href', response.newUrl);
                }
            } else {
                alert(response.errorMessage);
            }
        },
        
        
        _optionsCancel : function (event) {
            var $this = $(this);
            $('.ipaOptionsDialog').remove();
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
                
                var data = $this.data('ipContentManagement');
                
                if (!data.saving) {
                    return;
                }
                
                
                var postData = Object();
                postData.g = 'standard';
                postData.m = 'content_management';
                postData.a = 'savePage';
                postData.revisionId = ip.revisionId;


                $.ajax({
                    type : 'POST',
                    url : data.postUrl,
                    data : postData,
                    context : $this,
                    success : methods._savePageResponse,
                    dataType : 'json'
                });
            });
        },
        
        _savePageResponse: function(response) {
            var $this = $(this);
            var data = $this.data('ipContentManagement');
            if (response.status == 'success') {
                if (data.publishAfterSave) {
                    var postData = Object();
                    postData.g = 'standard';
                    postData.m = 'content_management';
                    postData.a = 'publishPage';
                    postData.revisionId = response.newRevisionId;

                    $.ajax({
                        type : 'POST',
                        url : data.postUrl,
                        data : postData,
                        context : $this,
                        success : methods._publishPageResponse,
                        dataType : 'json'
                    });
                
                
                } else {
                    window.location.href = response.newRevisionUrl;
                }
            } else {
                var tmpData = $this.data('ipContentManagement');
                tmpData.saving = false;
                if (tmpData.publishAfterSave) {
                    tmpData.publishAfterSave = false;
                }
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
    
        publishStart : function (event) {
            var $this = $(this);
            var tmpData = $this.data('ipContentManagement'); 
            tmpData.publishAfterSave = true;
            $this.data('ipContentManagement', tmpData);
            $this.ipContentManagement('saveStart');
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

                $( "#ipSaveProgress .ipMainProgressbar" ).progressbar();
                $( "#ipSaveProgress .ipMainProgressbar" ).progressbar('value', overallProgress*100);

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