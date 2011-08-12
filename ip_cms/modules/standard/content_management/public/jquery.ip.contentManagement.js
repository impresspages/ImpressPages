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
                    
                    
                    if ($("#ipControllPanelBg").length == 0) {
                        var $controlsBgDiv = $('<div id="ipControllPanelBg" />');
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
        


        //********INIT*********

        initResponse : function(response) {
            return this.each(function() {        	
	            if (response.status == 'success') {
	            	$this = $(this);
	                $('body').prepend(response.controlPanelHtml);
	                $('body').append(response.saveProgressHtml);
	                
	                var options = new Object;
	                options.zoneName = ipZoneName;
	                options.pageId = ipPageId;
	                options.revisionId = ipRevisionId;
	                options.widgetControlsHtml = response.widgetControlsHtml;
	                options.contentManagementObject = $this;
	                

	                
	                $('.ipWidgetButtonSelector').ipWidgetButton();
	                
	                
	                $('.ipPageSave').bind('click', function(event){$(this).trigger('savePageClick.ipContentManagement');});
	                $('.ipPagePublish').bind('click', function(event){$(this).trigger('publishClick.ipContentManagement');});
	                $('#ipRevisionSelect').bind('change', function(event){document.location = $(event.currentTarget).val(); });
	                

	                $this.bind('savePageClick.ipContentManagement', function(event){$(this).ipContentManagement('saveStart');});
	                $this.bind('publishClick.ipContentManagement', function(event){$(this).ipContentManagement('publish');});
	                
	                $this.bind('addSaveJob.ipContentManagement', function(event, jobName, saveJobObject){console.log('add job'); console.log(this); $(this).ipContentManagement('addSaveJob', jobName, saveJobObject);});

	                
	                $this.trigger('initFinished.ipContentManagement', options);
	            }
            });
        },

        //*********SAVE**********//
        
        saveStart : function() {
            return this.each(function() {
                $this = $(this);

                $( "#ipSaveProgress" ).dialog({
                    height: 140,
                    modal: true
                });
                
                $( "#ipSaveProgress .ipMainProgressbar" ).progressbar({
                    value: 0
                });
                $( "#ipSaveProgress .ipMainProgressbar" ).progressbar('value', 50);
                
                
	        	$this.trigger('pageSaveStart.ipContentManagement');
	        	jobsCount = 0;
	        	for (var prop in $this.data('ipContentManagement').saveJobs) {
	        	    jobsCount++;
	        	}
	        	
	        	if (jobsCount == 0) {
	        	    $this.ipContentManagement('saveFinish'); //initiate save finishing action
	        	} else {
	        	    //wait for jobs to finish
	        	}
	        	
            });
     
        },
        
        saveFinish : function() {
            return this.each(function() {
                return ;
                $this = $(this);
                data = Object();
                data.g = 'standard';
                data.m = 'content_management';
                data.a = 'savePage';
                data.revisionId = ipRevisionId;


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
              // window.location.href = response.newRevisionUrl;
            }
        },
        
        addSaveJob : function (jobName, saveJobObject) {
            return this.each(function() {  
	        	$(this); //don't do $this = $(this); Somehow this operation equals to ipBlock = $(this); and raises problems if block want to add two jobs;	
	        	$(this).data('ipContentManagement').saveJobs[jobName] = saveJobObject;
	        	$(this).ipContentManagement('_displaySaveProgress');
            });
        },

    
        publish : function(event){
            return this.each(function() {  
            	$this = $(this);
            });
        },

        _displaySaveProgress : function () {
            return this.each(function() {
	        	$this = $(this);
	        	var percentage = 0;
	        	
	        	var timeLeft = 0;
	        	var timeSpent = 0;
	        	var progress = 0;
	        	
	        	var saveJobs = $this.data('ipContentManagement').saveJobs;
	
	        	console.log('save progress');
	        	
	        	for (var i in saveJobs) {
	        		var curJob = saveJobs[i];
	        		timeLeft = timeLeft + curJob.getTimeLeft();
	        		timeSpent = timeSpent + curJob.getTimeLeft() / (1 - curJob.getProgress()) * curJob.getProgress();	        		
	        	}
	        	
	        	console.log('Time spent: ' + timeSpent + ' Time left: ' + timeLeft + ' Complete: ' + timeSpent / (timeLeft + timeSpent) * 100 + '%');
            });
        }

        //*********END SAVE*************//
        
    };
    
    

    $.fn.ipContentManagement = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidgetButton');
        }


    };
    
   

})(jQuery);