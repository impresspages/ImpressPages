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
                    $this.bind('initFinished.ipContentManagement', $.proxy(methods._initBlocks, $this));

                    $(this).trigger('initStarted.ipContentManagement');

                    $this.data('ipContentManagement', {
                        saveJobs : Object(),
                        optionsChanged : false
                    });
                    var data = $this.data('ipContentManagement');


                    {
                        $('body').prepend(ipContentInit.saveProgressHtml);
                        $('body').prepend(ipContentInit.controlPanelHtml);

                        var options = new Object;
                        options.zoneName = ip.zoneName;
                        options.pageId = ip.pageId;
                        options.revisionId = ip.revisionId;
                        options.widgetControlsHtml = ipContentInit.widgetControlsHtml;
                        options.contentManagementObject = $this;
                        options.manageableRevision = ipContentInit.manageableRevision;

                        var data = $this.data('ipContentManagement');
                        data.initInfo = options;
                        $this.data('ipContentManagement', data);

                        $('.ipAdminPanel .ipActionWidgetButton').ipAdminWidgetButton();


                        $('.ipAdminPanel .ipActionSave').bind('click', function(e){$.proxy(methods.save, $this)(false)});
                        $('.ipAdminPanel .ipActionPublish').bind('click', function(e){$.proxy(methods.save, $this)(true)});
                        $('.ipAdminPanelContainer .ipsPreview').on('click', function(e){e.preventDefault(); ipContent.setManagementMode(0);});


                        $this.bind('error.ipContentManagement', function (event, error){$(this).ipContentManagement('addError', error);});

                        $this.trigger('initFinished.ipContentManagement', options);
                    }


                }




            });
        },


        _initBlocks: function() {
            var $this = this;
            $this.ipContentManagement('initBlocks', $('.ipBlock'));
        },

        initBlocks : function(blocks) {
            var $this = this;
            var data = $this.data('ipContentManagement');
            var options = data.initInfo;
            if (options.manageableRevision) {
                blocks.ipBlock(options);
            }
        },

        addError : function (errorMessage) {
            var $newError = $('.ipAdminErrorSample .ipAdminError').clone();
            $newError.text(errorMessage);
            $('.ipAdminErrorContainer').append($newError);
            $newError.animate( {opacity: "100%"}, 6000)
            .animate( { queue: true, opacity: "0%" }, { duration: 3000, complete: function(){$(this).remove();}});
        },


        // *********SAVE**********//



        save : function(publish) {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('ipContentManagement');

                var postData = Object();
                postData.aa = 'Content.savePage';
                postData.securityToken = ip.securityToken;
                postData.revisionId = ip.revisionId;
                if (publish) {
                    postData.publish = 1;
                } else {
                    postData.publish = 0;
                }

                $.ajax({
                    type : 'POST',
                    url : ip.baseUrl,
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
                window.location.href = response.newRevisionUrl;
            } else {

            }
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