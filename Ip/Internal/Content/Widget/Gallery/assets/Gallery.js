/**
 * @package ImpressPages
 *
 *
 */

var IpWidget_Gallery = function () {
    this.$widgetObject = null;
    this.data = null;
    this.$controls = null;
    this.widgetId = null;

    var widgetClass = 'ipWidget-Gallery';

    this.init = function ($widgetObject, data) {
        var currentScope = this;
        this.$widgetObject = $widgetObject;
        this.data = data;
        this.widgetId = this.$widgetObject.data('widgetid');


        this.$widgetObject.on('click', $.proxy(this.focus, this));
        $(document.body).on('click', $.proxy(function (e) { //detect mouse click outside of the widget
            var $target = $(e.target);
            if (!$target.hasClass(widgetClass)) {
                $target = $target.closest('.' + widgetClass);
            }
            if ($target.length == 0) {
                $.proxy(currentScope.blur, currentScope)();
            }

        }, this));


        $widgetObject.find('.ipsAdd').off('click.galleryWidget').on('click.galleryWidget', function (e) {
            e.preventDefault();
            var repository = new ipRepository({preview: 'thumbnails', filter: 'image'});
            repository.on('ipRepository.filesSelected', $.proxy(currentScope.filesSelected, currentScope));
        });

        $widgetObject.find('.ipsManage').off('click.galleryWidget').on('click.galleryWidget', function (e) {
            e.preventDefault();
            $.proxy(currentScope.managementPopup, currentScope)();
        });



        var $list = this.$widgetObject.find('._container');
        $list.sortable();
        $list.disableSelection();
        $list.on("sortstart", function (event, ui) {
            currentScope.dragItemOriginalPosition = $(ui.item).index();
            $.proxy(currentScope.blurImage, currentScope)();
        });
        $list.on("sortstop", function (event, ui) {
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
        });

        //individual image management
        this.$widgetObject.find('a').on('click', function (e) {
            e.preventDefault();
        });//turn off lightbox
        this.$widgetObject.find('.ipsItem').on('click', $.proxy(this.focusImage, this));

        this.$controls = $('#ipWidgetGalleryMenu');
        $('body').on('click', $.proxy(function (e) { //detect mouse click outside of the image
            var $target = $(e.target);

            var $closestLi = $target;
            if (!$target.hasClass('ipsItem')) {
                $closestLi = $target.closest('.ipsItem');
            }

            var $closestWidget = $closestLi.closest('.' + widgetClass);

            if ($closestWidget.length != 1) {
                $.proxy(this.blurImage, this)();
            }


        }, this));

        $(document).on(
            'ipWidgetDeleted.galleryWidget ' +
                'ipWidgetAdded.galleryWidget ' +
                'ipWidgetMoved.galleryWidget'
            , $.proxy(currentScope.blur, currentScope));


    };

    this.onAdd = function (e) {
        this.$widgetObject.click();
        var thisContext = this;
        var repository = new ipRepository({preview: 'thumbnails', filter: 'image'});
        repository.on('ipRepository.filesSelected', $.proxy(thisContext.filesSelected, thisContext));
        repository.on('ipModuleRepository.cancel', function () {
            ipContent.deleteWidget(thisContext.$widgetObject.data('widgetid'));
        });

    };

    this.focusImage = function (e) {
        var context = this;
        e.preventDefault();

        var $item = $(e.currentTarget);
        var $img = $item.find('.ipsImage');
        var $controls = this.$controls;

        $controls.removeClass('hidden');
        $controls.css('position', 'absolute');
        $controls.css('left', $img.offset().left + 5);
        $controls.css('top', $img.offset().top + 10);

        this.imageIndex = $item.index();

        $controls.find('.ipsDelete').off().on('click', function (e) {
            $.proxy(context.deleteImage, context)($item.index());
        });
        $controls.find('.ipsEdit').off().on('click', function (e) {
            $.proxy(context.editImage, context)($item.index());
        });
        $controls.find('.ipsLink').off().on('click', function (e) {
            $.proxy(linkPopup, context)($item.index());
        });
        $controls.find('.ipsSettings').off().on('click', function (e) {
            $.proxy(settingsPopup, context)($item.index());
        });
    };




    this.blurImage = function () {
        this.$controls.addClass('hidden');
    };

    this.focus = function () {
        var thisContext = this;
        var $widgetObject = this.$widgetObject;

    };

    this.blur = function () {
        this.$controls.addClass('hidden');
    };

    this.deleteImage = function (position, callback) {
        if (!this.data.images[1]) { //if last image
            //remove the whole widget
            ipContent.deleteWidget(this.$widgetObject.data('widgetid'));
            return;
        }

        //proceed deleting single image
        var data = {};
        data.method = 'delete';
        data.position = position;
        this.$widgetObject.save(data, true, callback);
    };

    this.editImage = function (position, callback) {
        var context = this;
        var $modal = $('#ipWidgetGalleryEditPopup');
        var options = {};
        var data = this.data.images[position];

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
        options.enableChangeHeight = false;
        options.enableChangeWidth = false;
        options.enableUnderscale = true;

        options.autosizeType = 'crop';

        var $img = this.$widgetObject.find('.ipsImage').eq(position);
        if ($img.length == 1) {
            options.windowWidth = $img.width();
            options.windowHeight = $img.height();
        }

        var $editScreen = $modal.find('.ipsEditScreen');
        $editScreen.ipUploadImage('destroy');
        $editScreen.ipUploadImage(options);

        $modal.find('.ipsConfirm').off().on('click', function () {
            var crop = $editScreen.ipUploadImage('getCropCoordinates');
            var curImage = $editScreen.ipUploadImage('getCurImage');
            $.proxy(context.updateImage, context)(position, crop.x1, crop.y1, crop.x2, crop.y2, curImage, callback);
            $modal.modal('hide');
        });

        $modal.off('hidden.bs.modal.GalleryWidget').on('hidden.bs.modal.GalleryWidget', function () {
            if (callback) {
                $.proxy(callback, context)();
            }
        })
    };

    this.updateImage = function (imageIndex, x1, y1, x2, y2, image, callback) {
        var data = {
            method: 'update',
            fileName: image,
            imageIndex: imageIndex,
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

    this.filesSelected = function (event, files) {
        var data = {
            method: 'add'
        };
        $.each(files, function (key, value) {
            if (!data.images) {
                data.images = [];
            }
            data.images[data.images.length] = { //AJAX skips arrays without integer key
                fileName: value.fileName,
                status: "new"
            };
        });

        this.$widgetObject.save(data, 1, function ($widget) {
            $widget.click();
        });
    };

    var linkPopup = function (index, callback) {
        var context = this;
        this.popup = $('#ipWidgetGalleryLinkPopup');
        this.confirmButton = this.popup.find('.ipsConfirm');
        this.type = this.popup.find('select[name=type]');
        this.url = this.popup.find('input[name=url]');
        this.blank = this.popup.find('input[name=blank]');
        this.nofollow = this.popup.find('input[name=nofollow]');
        var data = this.data.images[index];

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

        this.confirmButton.off().on('click', function() {$.proxy(saveLink, context)(callback)});
        this.popup.off('hidden.bs.modal.GalleryWidget').on('hidden.bs.modal.GalleryWidget', function () {
            if (callback) {
                $.proxy(callback, context)();
            }
        })
    };


    var saveLink = function (callback) {
        var data = {
            method: 'setLink',
            type: this.type.val(),
            url: this.url.val(),
            blank: this.blank.prop('checked') ? 1 : 0,
            nofollow: this.nofollow.prop('checked') ? 1 : 0,
            index: this.imageIndex
        };
        this.$widgetObject.save(data, 1, callback); // save and reload widget
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


    var settingsPopup = function (index, callback) {
        var data = this.data.images[index];
        var context = this;
        this.settingsPopup = $('#ipWidgetGallerySettingsPopup');
        this.confirmButton = this.settingsPopup.find('.ipsConfirm');
        this.title = this.settingsPopup.find('input[name=title]');
        this.description = this.settingsPopup.find('textarea[name=description]');

        this.title.val(data.title);
        this.description.val(data.description);

        this.settingsPopup.modal(); // open modal popup
        ipInitForms();

        this.confirmButton.off().on('click', function () {
            $.proxy(saveSettings, context)(callback)
        });

        this.settingsPopup.off('hidden.bs.modal.GalleryWidget').on('hidden.bs.modal.GalleryWidget', function () {
            if (callback) {
                $.proxy(callback, context)();
            }
        });

        // Force include widgetData on submit. Overwrites default submit action. See ipInitForms()
        this.settingsPopup.find('.ipsAjaxSubmit').off('submit.ipSubmit').on('submit.ipSubmit', function (e) {
            $.proxy(saveSettings, context)(callback);
            e.preventDefault();
        })
    };

    var saveSettings = function (callback) {
        var data = {
            method: 'saveSettings',
            title: this.title.val(),
            description: this.description.val(),
            index: this.imageIndex
        };

        this.settingsPopup.modal('hide');
        this.$widgetObject.save(data, 1, callback); // save and reload widget
    };


    /*** MANAGEMENT POPUP FUNCTIONALITY ***/


    /**
     * This method reopens management popup. Used when previous operation reload the widget and this whole JS object becomes obsolete, we need to reload our data and reshow the popup.
     */
    this.reopenManagementPopup = function () {
        this.data = $('#ipWidget-' + this.widgetId).data('widgetdata');
        $('#ipWidgetGalleryManagePopup').modal();
    };

    this.refreshManagementPopupData = function () {
        this.data = $('#ipWidget-' + this.widgetId).data('widgetdata');
    };



    this.managementPopup = function () {
        var context = this;
        var $popup = $('#ipWidgetGalleryManagePopup');

        var $container = $popup.find('.ipsContainer');
        $container.html('');
        var $template = $popup.find('.ipsItemTemplate').clone().detach().removeClass('ipsItemTemplate');



        $.each(this.data.images, function (key, value) {
            var $item = $template.clone();
            $item.find('img').attr('src', value.imageSmall);
            $container.append($item);
            $item.on('click', $.proxy(context.focusManagementPopupImage, context));
        });


        $container.sortable();
        $container.disableSelection();
        $container.on("sortstart", function (event, ui) {
            context.dragItemOriginalPosition = $(ui.item).index();
            $popup.find('.ipsWidgetGalleryMenu').addClass('hidden');
        });
        $container.on("sortstop", function (event, ui) {
            var data = {};
            data.method = 'move';
            data.originalPosition = context.dragItemOriginalPosition;
            data.newPosition = $(ui.item).index();
            if (data.newPosition != data.originalPosition) {
                context.$widgetObject.save(data, true, function () {
                    $.proxy(context.refreshManagementPopupData, context)();
                });

            } else {
                //display image controls
                $(ui.item).click();
            }
        });


        $popup.modal();

    };


    this.focusManagementPopupImage = function (e) {
        var context = this;
        var $popup = $('#ipWidgetGalleryManagePopup');
        var $body = $popup.find('.modal-body');
        var $container = $popup.find('.ipsContainer');
        e.preventDefault();

        var $item = $(e.currentTarget);
        var $img = $item.find('.ipsImage');

        var $controls = $popup.find('.ipsWidgetGalleryMenu');
        if (!$controls.length) {
            $controls = this.$controls.clone().removeAttr('id').detach();
            $body.prepend($controls);
        }

        $controls.removeClass('hidden');
        $controls.css('position', 'absolute');
        $controls.css('left', ($item.offset().left - $container.offset().left) + 20 +'px');
        $controls.css('top', ($item.offset().top - $container.offset().top) + 20 +'px');

        this.imageIndex = $item.index();

        $controls.find('.ipsDelete').off('click.galleryWidget').on('click.galleryWidget', function (e) {
            $.proxy(context.deleteImage, context)($item.index(), function () {
                $controls.addClass('hidden');
                context.data = $('#ipWidget-' + context.widgetId).data('widgetdata');
                var index = $item.index();
                var $next = $item.next();
                $item.remove();
                $next.click();

            });

        });
        $controls.find('.ipsEdit').off('click.galleryWidget').on('click.galleryWidget', function (e) {
            $popup.modal('hide');
            $.proxy(context.editImage, context)($item.index(), function () {
                $.proxy(context.reopenManagementPopup, context)();
                $item.find('img').attr('src', context.data.images[$item.index()]['imageSmall']);
            });
        });
        $controls.find('.ipsLink').off('click.galleryWidget').on('click.galleryWidget', function (e) {
            $popup.modal('hide');
            $.proxy(linkPopup, context)($item.index(), context.reopenManagementPopup);
        });
        $controls.find('.ipsSettings').off('click.galleryWidget').on('click.galleryWidget', function (e) {
            $popup.modal('hide');
            $.proxy(settingsPopup, context)($item.index(), context.reopenManagementPopup);
        });
    };


    /*** END MANAGEMENT POPUP FUNCTIONALITY ***/


};

