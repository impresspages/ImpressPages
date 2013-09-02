
var ipDesign = new function () {
    "use strict";
    var lastSerialized = null,
        cssUpdateQueue = new Array(), //css files that are in progress to be updated
        cssUpdateInProgress = false;


    /**
     * 
     *
     * @private
     * @param src
     * @param type
     * @param callback_fn
     */
    var loadScript = function (src, type, callback_fn) {
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
        document.getElementsByTagName('head')[0].appendChild(scrpt);

        scrpt.onreadystatechange = function () {
            if (this.readyState === 'complete' || this.readyState === 'loaded') {
                if (loaded === false) {
                    callback_fn();
                }
                loaded = true;
            }
        };

        scrpt.onload = function() {
            if (loaded === false) {
                callback_fn();
            }
            loaded = true;
        };

        img = document.createElement('img');
        img.onerror = function () {
            if (loaded === false) {
                callback_fn();
            }
            loaded = true;
        };
        img.src = src;
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
        data = 'g=standard&m=design&ba=realTimeLess&ipDesignPreview=1&file=' + file + '.less';

        $.each(formData, function (index, elem) {
            if (elem.name !== 'a' && elem.name !== 'ba' && elem.name !== 'm' && elem.name !== 'g') {
                data = data + '&ipDesign[previewConfig][' + elem.name + ']=' + encodeURIComponent(elem.value);
            }

        });

        loadScript(ip.baseUrl + '?' + data, 'css', function () {
            $('link[href*="' + ip.baseUrl + ip.themeDir + ip.theme + '/' + file + '"]').remove();
            $('#ipsRealTimeCss_' + file).first().remove();
            cssUpdateInProgress = false;
            processCssUpdateQueue();
            console.log('loaded ' + file);
        });


    };





    this.init = function () {
        $('a').off('click').on('click', function (e) {
            e.preventDefault();
            ipDesign.openLink($(e.currentTarget).attr('href'));
        }); //it is important to bind links before adding configuration box html to the body

        $('body').append(ipModuleDesignConfiguration);
        ipModuleForm.init(); //reinit form controls after adding option box

        $('.ipModuleDesignConfig .ipsForm').off('submit').on('submit', function (e) {
            e.preventDefault();
            ipDesign.apply();
        });
        $('.ipModuleDesignConfig .ipsSave').off('click').on('click', function (e) {
            e.preventDefault();
            $('.ipModuleDesignConfig .ipsForm').submit();
        });

        $('.ipModuleDesignConfig .ipsForm').validator(validatorConfig);

        $('.ipModuleDesignConfig .ipsCancel').off('click').on('click', function (e) {
            e.preventDefault();
            window.parent.ipDesignCloseOptions(e);
        });

        $('.ipModuleDesignConfig .ipsDefault').off('click').on('click', function (e) {
            e.preventDefault();
            var restoreDefault = 1;
            ipDesign.openLink(window.location.href.split('#')[0], restoreDefault);
        });


        $('.ipModuleDesignConfig .ipsForm input').on('change', ipDesign.livePreviewUpdate);
        $('.ipModuleDesignConfig .ipsForm select').on('change', ipDesign.livePreviewUpdate);

        ipDesign.resize();
        $(window).bind("resize.ipModuleDesign", ipDesign.resize);

        $('.ipsReload').on('click', function (e) {
            e.preventDefault();
            ipDesign.openLink(window.location.href);
        });
    };

    this.showReloadNotice = function () {
        $('.ipModuleDesignConfig .ipsReload').removeClass('ipgHide');
    };

    this.reloadLessFile = function (files) {
        if (!(files instanceof Array)) {
            files = [files];
        }

        var i = 0,
            allFilesInQueue = true,
            filePos = 0;



        //remove files if they already are in the queue. This is to make sure the right order of loading.
        $.each(files, function (index, elem) {
            filePos = $.inArray(elem, cssUpdateQueue);
            if (filePos !== -1) {
                cssUpdateQueue.splice(filePos, 1);
            }
        });

        //add required files in the new order
        $.each(files, function (index, elem) {
            cssUpdateQueue.push(elem);
        });

        processCssUpdateQueue();
    };




    this.openLink = function (href, restoreDefault) {
        var config = $('.ipModuleDesignConfig .ipsForm').serializeArray();

        // create previewConfig data
        var previewConfig = {};
        var key;
        for (var i = 0; i < config.length; i++) {
            key = config[i].name;
            if (key != 'securityToken' && key != 'g' && key != 'm' && key != 'ba') {
                previewConfig[key] = config[i].value;
            }
        }


        // create form for previewConfig
        var postForm = $('<form>', {
            'method': 'POST',
            'action': href.indexOf('?') == -1 ? href + '?ipDesignPreview=1' : href + '&ipDesignPreview=1'
        });

        for (var name in previewConfig) {
            postForm.append($('<input>', {
                'name': 'ipDesign[previewConfig][' + name + ']',
                'value': previewConfig[name],
                'type': 'hidden'
            }));
        }

        postForm.append($('<input>', {
            'name': 'securityToken',
            'value': ip.securityToken,
            'type': 'hidden'
        }));

        if (restoreDefault) {
            postForm.append($('<input>', {
                'name': 'restoreDefault',
                'value': 1,
                'type': 'hidden'
            }));
        }


        postForm.appendTo('body').submit();
    };

    this.apply = function () {
        var $form = $('.ipModuleDesignConfig .ipsForm');
        var data = $form.serialize();
        $.ajax({
            url: ip.baseUrl,
            dataType: 'json',
            type : 'POST',
            data: data,
            success: function (response){
                if (response.status && response.status == 'success') {
                    var refreshUrl = window.location.href.split('#')[0];
                    window.location = refreshUrl;
                } else {
                    if (response.errors) {
                        $form.data("validator").invalidate(response.errors);
                    }
                }
            }
        });
    };

    this.livePreviewUpdate = function() {
        var $form = $('.ipModuleDesignConfig .ipsForm');
        if (lastSerialized == null) {
            lastSerialized = $form.serialize();
            return;
        }

        var curSerialized = $form.serialize();

        if (curSerialized != lastSerialized) {
            for (var optionNameIndex in ipModuleDesignOptionNames) {
                var optionName = ipModuleDesignOptionNames[optionNameIndex];
                var curValue = getValueByName(optionName, curSerialized);
                var lastValue = getValueByName(optionName, lastSerialized);
                if (lastValue != curValue) {
                    if (typeof(window['ipDesignOption_' + optionName]) === "function") {
                        eval('ipDesignOption_' + optionName + '(curValue);');
                    }
                }
            }


        }

        lastSerialized = curSerialized;
    };

    this.resize = function(e) {
        $('.ipModuleDesignConfig .modal-body').css('maxHeight', $(window).height() - 200);
    };

    var getValueByName = function(name, values) {
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec('?' + values);
        return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

};



$(document).ready(function () {
    ipDesign.init();
});

