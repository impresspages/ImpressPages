/**
 * @package ImpressPages
 *
 *
 */
var IpWidget_IpImageGallery;

(function($){
    "use strict";

    IpWidget_IpImageGallery = function() {
        this.$widgetObject = null;
        this.data = null;
        this.$controls = null;
        this.$widgetControls = null;

        this.init = function($widgetObject, data) {
            var currentScope = this;
            this.$widgetObject = $widgetObject;
            this.data = data;
            this.$widgetControls = $('#ipWidgetGalleryControls');

            this.$widgetObject.on('click', $.proxy(this.focus, this));
            $('body').on('click', $.proxy(function(e) { //detect mouse click outside of the widget
                var $target = $(e.target);
                if (!$target.hasClass('ipWidget-IpImageGallery')) {
                    $target = $target.closest('.ipWidget-IpImageGallery');
                }
                if ($target.length == 0 || $target.data('widgetinstanceid') != this.$widgetObject.data('widgetinstanceid')) {
                    $.proxy(this.blur, this)();
                }

            }, this));

            var $list = this.$widgetObject.find('ul');
            $list.sortable();
            $list.disableSelection();
            $list.on( "sortstart", function( event, ui ) {
                currentScope.dragItemOriginalPosition = $(ui.item).index();
                $.proxy(currentScope.blurImage, currentScope)();
            });
            $list.on( "sortstop", function( event, ui ) {
                var data = {};
                data.method = 'move';
                data.originalPosition = currentScope.dragItemOriginalPosition;
                data.newPosition = $(ui.item).index();
                if (data.newPosition != data.originalPosition) {
                    currentScope.$widgetObject.save(data, true);
                } else {
                    //display image controls
                    $(ui.item).click();
                }
            } );

            //individual image management
            this.$widgetObject.find('a').on('click', function(e){e.preventDefault();});//turn off lightbox
            this.$widgetObject.find('li').on('click', $.proxy(this.focusImage, this));

            this.$controls = $('#ipWidgetGalleryMenu');
            $('body').on('click', $.proxy(function(e) { //detect mouse click outside of the image
                var $target = $(e.target);

                var $closestLi = $target;
                if (!$target.prop("tagName") == 'li') {
                    $closestLi = $target.closest('li');
                }

                var $closestWidget = $closestLi.closest('.ipWidget-IpImageGallery');

                if ($closestWidget.length != 1) {
                    $.proxy(this.blurImage, this)();
                }


            }, this));


        }

        this.onAdd = function (e) {
            this.$widgetObject.click();
            var thisContext = this;
            var repository = new ipRepository({preview: 'thumbnails', filter: 'image'});
            repository.on('ipRepository.filesSelected', $.proxy(thisContext.filesSelected, thisContext));
            repository.on('ipModuleRepository.cancel', function () {
                ipContent.deleteWidget(thisContext.$widgetObject.data('widgetinstanceid'));
            });

        }

        this.focusImage = function (e) {
            var context = this;
            e.preventDefault();

            var $li = $(e.currentTarget);
            var $img = $li.find('img');
            var $controls = this.$controls;

            $controls.removeClass('ipgHide');
            $controls.css('position', 'absolute');
            $controls.css('left', $img.offset().left + 5);
            $controls.css('top', $img.offset().top + 5);

            $controls.find('.ipsDelete').off().on('click', function(e) {
                $.proxy(context.deleteImage, context)($li.index());
            });
        };

        this.blurImage = function () {
            this.$controls.addClass('ipgHide');
        };

        this.focus = function () {
            var thisContext = this;
            var $widgetControls = this.$widgetControls;
            var $widgetObject = this.$widgetObject;
            $widgetControls.removeClass('hide');
            $widgetControls.css('left', $widgetObject.offsetLeft);
            $widgetControls.css('top', $widgetObject.offsetTop);
            $widgetControls.css('position', 'absolute');
            $widgetControls.css('left', $widgetObject.offset().left);
            $widgetControls.css('top', $widgetObject.offset().top - $widgetControls.height() - 5);
            $widgetControls.find('.ipsAdd').off().on('click', function(e){
                e.preventDefault();
                var repository = new ipRepository({preview: 'thumbnails', filter: 'image'});
                repository.on('ipRepository.filesSelected', $.proxy(thisContext.filesSelected, thisContext));
            });

        }

        this.blur = function () {
            this.$widgetObject.find('.ipsAdd').remove();
        };

        this.deleteImage = function (position) {
            if (!this.data.images[1]) { //if last image
                //remove the whole widget
                ipContent.deleteWidget(this.$widgetObject.data('widgetinstanceid'));
                return;
            }

            //proceed deleting single image
            var data = {};
            data.method = 'delete';
            data.position = position;
            this.$widgetObject.save(data, true);
        };

        this.filesSelected = function(event, files) {
            var $this = $(this);

            var data = this.data;
            var data = {
                method: 'add'
            };
            $.each(files, function(key, value) {
                if (!data.images) {
                    data.images = [];
                }
                data.images[data.images.length] = { //AJAX skips arrays without integer key
                    fileName: value.fileName,
                    status: "new"
                };
            });

            this.$widgetObject.save(data, 1, function($widget){
                $widget.click();
            });
        }

        function addError(event, errorMessage) {
            $(this).trigger('error.ipContentManagement', [errorMessage]);
        }

        function prepareData() {
            var data = Object();
            var container = this.widgetObject.find('.ipWidget_ipImageGallery_container');

            data.images = new Array();
            var $images = container.ipWidget_ipImageGallery_container('getImages');
            $images.each(function(index) {
                var $this = $(this);
                var tmpImage = new Object();
                tmpImage.title = $this.ipWidget_ipImageGallery_image('getTitle');
                tmpImage.fileName = $this.ipWidget_ipImageGallery_image('getFileName');
                tmpImage.status = $this.ipWidget_ipImageGallery_image('getStatus');
                var tmpCropCoordinates = $this.ipWidget_ipImageGallery_image('getCropCoordinates');
                tmpImage.cropX1 = tmpCropCoordinates.x1;
                tmpImage.cropY1 = tmpCropCoordinates.y1;
                tmpImage.cropX2 = tmpCropCoordinates.x2;
                tmpImage.cropY2 = tmpCropCoordinates.y2;

                data.images.push(tmpImage);

            });

            $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
        }
    };

})(ip.jQuery);
