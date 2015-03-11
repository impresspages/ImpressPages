/**
 * @package ImpressPages
 *
 */


var IpWidget_Image = function () {
    var controllerScope = this;
    this.$widgetObject = null;
    this.data = null;

    this.init = function ($widgetObject, data) {
        var context = this;
        this.$widgetObject = $widgetObject;
        this.data = data;


        this.$widgetObject.find('.ipsImage').on('click', function () {
            $.proxy(makeResizable, context)();
        });

        this.$controls = $('#ipWidgetImageMenu');
        this.$widgetObject.on('click', $.proxy(this.focusImage, this));

        $('body').on('click', $.proxy(function (e) { //detect mouse click outside of the image
            var $target = $(e.target);
            var $closestWidget = $target.closest('.ipWidget-Image');

            if (!$target.hasClass('ipWidget-Image') && !$closestWidget.hasClass('ipWidget-Image')) {
                $.proxy(this.blurImage, this)();
            }


        }, this));

        $(document).on(
            'ipWidgetDeleted.imageWidget ' +
            'ipWidgetAdded.imageWidget ' +
            'ipWidgetMoved.imageWidget'
            , $.proxy(this.blurImage, controllerScope));

    };

    this.focusImage = function (e) {
        var context = this;
        e.preventDefault();

        var $item = $(e.currentTarget);
        var $img = $item.find('.ipsImage');
        var $controls = this.$controls;

        $controls.removeClass('hidden');
        $controls.css('position', 'absolute');
        $controls.css('left', $img.offset().left + 10);
        $controls.css('top', $img.offset().top + 10);

        $controls.find('.ipsDelete').off().on('click', function (e) {
            $.proxy(context.deleteImage, context)();
        });
        $controls.find('.ipsEdit').off().on('click', function (e) {
            $.proxy(context.editImage, context)();
        });
        $controls.find('.ipsLink').off().on('click', function (e) {
            $.proxy(linkPopup, context)();
        });
        $controls.find('.ipsSettings').off().on('click', function (e) {
            $.proxy(settingsPopup, context)();
        });
        $controls.find('.ipsActualSize').off().on('click', function (e) {
            $.proxy(actualSize, context)();
        });
    };

    this.blurImage = function () {
        this.$controls.addClass('hidden');
    };


    this.editImage = function (position) {
        var thisContext = this;
        var $modal = $('#ipWidgetImageEditPopup');
        var options = {};
        var data = this.data;

        $modal.modal();

        if (data.imageOriginal) {
            options.image = data.imageOriginal;
        }
        if (data.cropX1) {
            options.cropX1 = data.cropX1;
        }
        if (data.cropY1) {
            options.cropY1 = data.cropY1;
        }
        if (data.cropX2) {
            options.cropX2 = data.cropX2;
        }
        if (data.cropY2) {
            options.cropY2 = data.cropY2;
        }
        options.enableChangeHeight = true;
        options.enableChangeWidth = true;
        options.maxWindowWidth = 538;
        options.enableUnderscale = true;

        options.autosizeType = 'fit';

        var $img = this.$widgetObject.find('.ipsImage').eq(position);
        if ($img.length == 1) {
            options.windowWidth = 538;
            options.windowHeight = Math.round($img.height() / $img.width() * options.windowWidth);
        }

        var $editScreen = $modal.find('.ipsEditScreen');
        $editScreen.ipUploadImage('destroy');
        $editScreen.ipUploadImage(options);
        $modal.find('.ipsConfirm').off().on('click', function () {
            var crop = $editScreen.ipUploadImage('getCropCoordinates');
            var curImage = $editScreen.ipUploadImage('getCurImage');
            $.proxy(thisContext.updateImage, thisContext)(crop.x1, crop.y1, crop.x2, crop.y2, curImage);
            $modal.modal('hide');
        });
    };

    this.updateImage = function (x1, y1, x2, y2, image, callback) {
        var data = {
            method: 'update',
            fileName: image,
            cropX1: x1,
            cropY1: y1,
            cropX2: x2,
            cropY2: y2
        };


        this.$widgetObject.save(data, 1, function ($widget) {
            $widget.click();
            if (callback) {
                callback($widget);
            }
        });
    };

    this.onAdd = function (e) {
        var thisContext = this;
        var repository = new ipRepository({preview: 'thumbnails', filter: 'image'});
        repository.on('ipRepository.filesSelected', $.proxy(thisContext.filesSelected, thisContext));
        repository.on('ipModuleRepository.cancel', function () {
            ipContent.deleteWidget(thisContext.$widgetObject.data('widgetid'));
        });

    };


    this.filesSelected = function (event, files) {
        var $this = $(this);

        var data = {
            method: 'update'
        };
        $.each(files, function (key, value) {
            data.fileName = value.fileName;
        });

        this.$widgetObject.save(data, 1);
    };

    this.resize = function (width, height) {
        var $this = $(this);

        var data = {
            method: 'resize',
            width: width,
            height: height
        };

        if (this.$widgetObject.width() - width <= 2 && width < this.data.originalWidth) {
            data = {
                method: 'autosize'
            }
        }

        this.$widgetObject.save(data, 0);
    };

    var linkPopup = function () {
        var context = this;
        this.popup = $('#ipWidgetImageLinkPopup');
        this.confirmButton = this.popup.find('.ipsConfirm');
        this.type = this.popup.find('select[name=type]');
        this.url = this.popup.find('input[name=url]');
        this.blank = this.popup.find('input[name=blank]');
        this.nofollow = this.popup.find('input[name=nofollow]');
        var data = this.data;

        if (data.type) {
            this.type.val(data.type);
        } else {
            this.type.val('lightbox'); // cleanup value if it was set before
        }

        if (data.url) {
            this.url.val(data.url);
        } else {
            this.url.val(''); // cleanup value if it was set before
        }

        if (data.blank) {
            this.blank.attr('checked', true);
        } else {
            this.blank.attr('checked', false);
        }

        if (data.nofollow) {
            this.nofollow.attr('checked', true);
        } else {
            this.nofollow.attr('checked', false);
        }


        this.type.off().on('change', function () {
            $.proxy(showHide, context)();
        });

        $.proxy(showHide, context)();


        this.popup.modal(); // open modal popup

        ipInitForms();

        this.confirmButton.off().on('click', $.proxy(saveLink, context));
    };

    var saveLink = function () {
        var data = {
            method: 'setLink',
            type: this.type.val(),
            url: this.url.val(),
            blank: this.blank.prop('checked') ? 1 : 0,
            nofollow: this.nofollow.prop('checked') ? 1 : 0
        };

        this.$widgetObject.save(data, 1); // save and reload widget
        this.popup.modal('hide');
    };

    var showHide = function () {
        if (this.type.val() == 'link') {
            this.popup.find('.form-group.name-url').show();
            this.popup.find('.form-group.name-blank').show();
            this.popup.find('.form-group.name-nofollow').show();
        } else {
            this.popup.find('.form-group.name-url').hide();
            this.popup.find('.form-group.name-blank').hide();
            this.popup.find('.form-group.name-nofollow').hide();
        }
    };


    var settingsPopup = function () {
        var data = this.data;
        var context = this;
        this.settingsPopup = $('#ipWidgetImageSettingsPopup');
        this.confirmButton = this.settingsPopup.find('.ipsConfirm');
        this.title = this.settingsPopup.find('input[name=title]');
        this.description = this.settingsPopup.find('textarea[name=description]');

        this.title.val(data.title);
        this.description.val(data.description);

        this.settingsPopup.modal(); // open modal popup

        this.confirmButton.off().on('click', $.proxy(saveSettings, context));
    };

    var saveSettings = function () {
        var data = {
            method: 'saveSettings',
            title: this.title.val(),
            description: this.description.val()
        };

        this.$widgetObject.save(data, 1); // save and reload widget
        this.settingsPopup.modal('hide');
    };

    var actualSize = function(){
        var data = {
            method: 'autosize'
        }

        this.$widgetObject.save(data, 1);

    };

    var makeResizable = function()
    {
        this.$widgetObject.find('.ipsImage').resizable({
            aspectRatio: true,
            maxWidth: controllerScope.$widgetObject.width(),
            resize: function (event, ui) {
                controllerScope.resize(Math.round(ui.size.width), Math.round(ui.size.height));
            }
        });

    }


};
