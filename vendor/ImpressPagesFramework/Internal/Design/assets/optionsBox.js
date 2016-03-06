var ipDesign = new function () {
    "use strict";
    var lastSerialized = null,
        lastSerializedArray = null,
        cssUpdateQueue = [], //css files that are in progress to be updated
        cssUpdateInProgress = false,
        saveButtonDown = false;

    /**
     *
     *
     * @private
     * @param src
     * @param type
     * @param callback_fn
     */
    var loadScript = function (src, type, id, callback_fn) {
        var loaded = false, scrpt, img;
        if (type === 'script') {
            scrpt = document.createElement('script');
            scrpt.setAttribute('type', 'text/javascript');
            scrpt.setAttribute('src', src);

        } else if (type === 'css') {
            scrpt = document.createElement('link');
            scrpt.setAttribute('rel', 'stylesheet');
            scrpt.setAttribute('type', 'text/css');
            scrpt.setAttribute('href', src);
        }
        scrpt.setAttribute('id', id);
        document.getElementsByTagName('head')[0].appendChild(scrpt);

        scrpt.onreadystatechange = function () {
            if (this.readyState === 'complete' || this.readyState === 'loaded') {
                if (loaded === false) {
                    callback_fn();
                }
                loaded = true;
            }
        };

        scrpt.onload = function () {
            if (loaded === false) {
                callback_fn();
            }
            loaded = true;
        };

        /* for browsers who don't throw any event
         img = document.createElement('img');
         img.onerror = function () {
         if (loaded === false) {
         callback_fn();
         }
         loaded = true;
         };
         img.src = src;
         */
    };


    /*
     * This is the way to declare private methods.
     * */
    var processCssUpdateQueue = function () {
        if (cssUpdateInProgress) {
            return;
        }

        if (cssUpdateQueue.length) {
            var nextFile = cssUpdateQueue.shift();
            //remove files if they already are in the queue. This is to make sure the right order of loading.
            var filePos = $.inArray(nextFile, cssUpdateQueue);
            while (filePos !== -1 && cssUpdateQueue.length) {
                nextFile = cssUpdateQueue.shift();
                filePos = $.inArray(nextFile, cssUpdateQueue);
            }
            processFileReload(nextFile);
        }
    };


    /*
     * This is the way to declare private methods.
     * */
    var processFileReload = function (file) {
        var dataIterator, formData, data;
        cssUpdateInProgress = 1;
        formData = $('.ipModuleDesignConfig .ipsForm').serializeArray();
        data = 'aa=Design.realTimeLess&ipDesignPreview=1&file=' + file + '.less';

        $.each(formData, function (index, elem) {
            if (elem.name !== 'a' && elem.name !== 'aa' && elem.name !== 'm' && elem.name !== 'g') {
                data = data + '&ipDesign[pCfg][' + elem.name + ']=' + encodeURIComponent(elem.value);
            }

        });

        if (getParameterByName('theme')) {
            //for theme preview in market
            data = data + '&theme=' + encodeURIComponent(getParameterByName('theme'));
        }

        loadScript(ip.baseUrl + '?' + data, 'css', 'ipsRealTimeCss_' + file, function () {
            $('link[href*="' + ipThemeUrl(file) + '"]').remove();
            if ($('#ipsRealTimeCss_' + file).length > 1) {
                $('#ipsRealTimeCss_' + file).first().remove();
            }
            cssUpdateInProgress = false;
            processCssUpdateQueue();
        });


    };

    var initAccordion = function () {
        var firstFieldsetToShow;
        // wrap fields in a div so accordion would work
        $('.ipModuleDesignConfig .ipsBody fieldset').each(function (index, fieldset) {
            var $fieldset = $(fieldset);
            var $legend = $fieldset.find('legend');

            $fieldset.addClass('panel');
            // if legend exist it means its option group
            if ($legend.length) {
                // adding required class to make a group
                $fieldset.parent().attr('id', 'optionBoxCollapseGroup');
                firstFieldsetToShow = firstFieldsetToShow || index;
                // adding required attributes to make collapse() to work
                $legend
                    .attr('data-toggle', 'collapse')
                    .attr('data-target', '#optionBoxCollapse' + index)
                    .attr('data-parent', '#optionBoxCollapseGroup');
                if (firstFieldsetToShow != index) {
                    $legend.addClass('collapsed');
                }
                $fieldset.find('.form-group').wrapAll('<div class="collapse' + (firstFieldsetToShow == index ? ' in' : '') + '" id="optionBoxCollapse' + index + '" />');
            }
        });
        $('.ipModuleDesignConfig .ipsBody .collapse').on('shown.bs.collapse', function () {
            fixAccordion();
        });
    };

    var fixAccordion = function () {
        // this code is not in ipDesign.fixLayout so it would be executed only on drag
        var $body = $('.ipModuleDesignConfig .ipsBody');
        var $openPanel = $body.find('.collapse.in');
        if ($openPanel.length) {
            var bodyHeight = parseInt($body.css('max-height'));
            var panelHeight = parseInt($openPanel.height('auto').height());
            var legendHeight = 0;
            // calculating the height of all opened legends
            $body.find('legend').each(function (index, legend) {
                legendHeight += $(legend).outerHeight(true);
            });
            // adding the height of warning
            //legendHeight += $('.ipModuleDesignConfig .ipsReload').outerHeight(true); #removeReloadNote

            // calculating how much space is left for content
            var openPanelHeight = (bodyHeight > legendHeight) ? (bodyHeight - legendHeight) : 0;

            // fixing height only if there's not enough space
            if (openPanelHeight < panelHeight) {
                $openPanel.height(openPanelHeight);
            } else {
                $openPanel.height('auto');
            }
        }
    };

    var initLayout = function () {
        ipDesign.fixLayout();
        $('.ipModuleDesignConfig .ipsDialog').draggable({
            //axis: "x",
            //containment: "body",
            //containment: [0, 0, x2, y2],
            handle: ".ipsDragHandler",
            scroll: false,
            drag: function (event, ui) {
                ipDesign.fixLayout();
            }
        });

        $(window).bind("resize.ipModuleDesign", ipDesign.fixLayout);
        $(window).bind("scroll.ipModuleDesign", ipDesign.fixLayout);
    };

    this.init = function () {
        $('a').not('.ipWidget-Gallery a, .ipWidget-Image a, .ipWidget-File a')
            .off('click').on('click', function (e) {
                e.preventDefault();
                ipDesign.openLink($(e.currentTarget).attr('href'));
            }); //it is important to bind links before adding configuration box html to the body

        $('body').append(ipModuleDesignConfiguration);
        ipInitForms(); //reinit form controls after adding option box

        $('.ipModuleDesignConfig .ipsSave').off('mousedown').on('mousedown', function (e) {
            saveButtonDown = true;
        });

        $('.ipModuleDesignConfig .ipsSave').off('mouseup').on('mouseup', function (e) {
            saveButtonDown = false;
        });

        $('.ipModuleDesignConfig .ipsSave').off('click').on('click', function (e) {
            e.preventDefault();
            $(this).addClass('disabled').text(ipTranslationSaving);

            $('.ipModuleDesignConfig .ipsForm').submit();
        });

        $('.ipModuleDesignConfig .ipsForm').on('ipSubmitResponse', function (e, response) {
            if (response.result) {
                window.location.reload(true);
            }
        });

        $('.ipModuleDesignConfig .ipsCancel').off('click').on('click', function (e) {
            e.preventDefault();
            window.parent.ipDesignOptionsClose(e);
        });

        $('.ipModuleDesignConfig .ipsDefault').off('click').on('click', function (e) {
            e.preventDefault();
            var restoreDefault = 1;
            ipDesign.openLink(window.location.href.split('#')[0].split('?')[0], restoreDefault);
        });


        $('.ipModuleDesignConfig .ipsForm input').on('change', ipDesign.livePreviewUpdate);
        $('.ipModuleDesignConfig .ipsForm select').on('change', ipDesign.livePreviewUpdate);
        $('.ipModuleDesignConfig .ipsForm .type-repositoryFile').on('ipFieldFileAdded', ipDesign.livePreviewUpdate);
        $('.ipModuleDesignConfig .ipsForm .type-repositoryFile').on('ipFieldFileRemoved', ipDesign.livePreviewUpdate);


        initAccordion();
        initLayout();

        $('.ipModuleDesignConfig .ipsReloadButton').on('click', function (e) {
            e.preventDefault();
            ipDesign.openLink(window.location.href);
        });

        lastSerialized = $('.ipModuleDesignConfig .ipsForm').serialize();
        lastSerializedArray = $('.ipModuleDesignConfig .ipsForm').serializeArray();

        //setup config groups


    };

    this.showReloadNotice = function () {
        if (!saveButtonDown) { //if user is holding down the save button, don't show reload message as it will scroll save booton down and save won't happen.
            //$('.ipModuleDesignConfig .ipsReload').removeClass('hidden'); #removeReloadNote
            $('.ipModuleDesignConfig .ipsReloadButton').removeClass('hidden');
            //fixAccordion(); #removeReloadNote
        }
    };

    this.reloadLessFiles = function (files) {
        if (!(files instanceof Array)) {
            files = [files];
        }

        var i = 0,
            filePos = 0;


        //add files to the queue
        $.each(files, function (index, elem) {
            cssUpdateQueue.push(elem);
        });

        setTimeout(processCssUpdateQueue, 200);

    };


    this.openLink = function (href, restoreDefault) {
        var config = $('.ipModuleDesignConfig .ipsForm').serializeArray();

        // create preview config data
        var pCfg = {};
        var key;
        for (var i = 0; i < config.length; i++) {
            key = config[i].name;
            if (key != 'securityToken' && key != 'g' && key != 'm' && key != 'aa') {
                pCfg[key] = config[i].value;
            }
        }


        // create form for preview config
        var postForm = $('<form>', {
            'method': 'POST',
            'action': href.indexOf('?') == -1 ? href + '?ipDesignPreview=1' : href + '&ipDesignPreview=1'
        });

        for (var name in pCfg) {
            postForm.append($('<input>', {
                'name': 'ipDesign[pCfg][' + name + ']',
                'value': pCfg[name],
                'type': 'hidden'
            }));
        }

        postForm.append($('<input>', {
            'name': 'securityToken',
            'value': ip.securityToken,
            'type': 'hidden'
        }));

        if (restoreDefault) {
            postForm.find('[name^=ipDesign]').remove();
            postForm.append($('<input>', {
                'name': 'restoreDefault',
                'value': 1,
                'type': 'hidden'
            }));
        }


        postForm.append($('<input>', {
            'name': 'refreshPreview',
            'value': 1,
            'type': 'hidden'
        }));


        postForm.appendTo('body').submit();
    };


    this.livePreviewUpdate = function () {
        var $form = $('.ipModuleDesignConfig .ipsForm');


        var curSerialized = $form.serialize();
        var curSerializedArray = $form.serializeArray();

        if (curSerialized != lastSerialized) {
            for (var optionNameIndex in ipModuleDesignOptionNames) {
                var optionName = ipModuleDesignOptionNames[optionNameIndex];
                var curValue = getValueByName(optionName, curSerializedArray);
                var lastValue = getValueByName(optionName, lastSerializedArray);
                if (lastValue != curValue) {
                    if (typeof(ipDesignOptions[optionName]) === 'function') {
                        if ($('.type-repositoryFile.name-bodyBackgroundColor').length) {
                            curValue = ipRepositoryUrl + curValue; //add base URL if we deal with RepositoryFile input
                        }
                        ipDesignOptions[optionName](curValue);
                    } else {
                        //live preview doesn't exist. Tell user to reload the page
                        ipDesign.showReloadNotice();
                    }
                }
            }
        }

        lastSerialized = curSerialized;
        lastSerializedArray = curSerializedArray;
    };

    this.fixLayout = function (e) {
        var x2 = $(window).width() - $('.ipModuleDesignConfig .ipsDialog').width() - 20;
        var y2 = $(window).height() - 150;
        var topOffset = parseInt($('.ipModuleDesignConfig .ipsDialog').css('top'));
        $('.ipModuleDesignConfig .ipsBody').css('max-height', $(window).height() - topOffset - 170);

        fixAccordion();
    };


    var getValueByName = function (name, values) {
        var results = "";

        $.each(values, function (key, value) {
            if (value && value.name && value.value) {
                if (value.name == name || value.name == name + '[]') { //array for RepositoryFile
                    results = value.value;
                }
            }
        });

        return results;
    };

    var getParameterByName = function (name) {
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    };

};

$(document).ready(function () {
    ipDesign.init();
});

