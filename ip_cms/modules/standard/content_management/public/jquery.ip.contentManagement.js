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
	                
	                var options = new Object;
	                options.zoneName = ipZoneName;
	                options.pageId = ipPageId;
	                options.revisionId = ipRevisionId;
	                options.widgetControlsHtml = response.widgetControlsHtml;
	                options.contentManagementObject = $this;
	                

	                
	                $('.ipWidgetButtonSelector').ipWidgetButton();
	                
	                $('.ipPageSave').bind('click', function(event){$(this).trigger('saveClick.ipContentManagement');});
	                $('.ipPagePublish').bind('click', function(event){$(this).trigger('publishClick.ipContentManagement');});
	                $('#ipRevisionSelect').bind('change', function(event){document.location = $(event.currentTarget).val(); });
	                

	                $this.bind('saveClick.ipContentManagement', function(event){$(this).ipContentManagement('saveStart');});
	                $this.bind('publishClick.ipContentManagement', function(event){$(this).ipContentManagement('publish');});
	                
	                $this.bind('addSaveJob.ipContentManagement', function(event, jobName, saveJobObject){$(this).ipContentManagement('addSaveJob', jobName, saveJobObject);});

	                
	                $this.trigger('initFinished.ipContentManagement', options);
	            }
            });
        },

        //*********SAVE**********//
        
        saveStart : function() {
            return this.each(function() {    
	        	$this = $(this);
	        	$this.trigger('saveStart.ipContentManagement');
            });
     
        },
        
        addSaveJob : function (jobName, saveJobObject) {
            return this.each(function() {  
	        	$this = $(this);
	        	$this.data('ipContentManagement').saveJobs[jobName] = saveJobObject;
	        	$this.ipContentManagement('_displaySaveProgress');
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