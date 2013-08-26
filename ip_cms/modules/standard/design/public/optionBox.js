
"use strict";

$(document).ready(function() {

    $('a').off('click').on('click', function(e) {
        e.preventDefault();
        ipDesign.openLink($(e.currentTarget).attr('href'));
    }); //it is important to bind links before adding configuration box html to the body

    $('body').append(ipModuleDesignConfiguration);
    ipModuleForm.init(); //reinit form controls after adding option box

    $('.ipModuleDesignConfig .ipsForm').on('submit', function(e) {
        e.preventDefault();
        ipDesign.apply();
    });
    $('.ipModuleDesignConfig .ipsSave').off('click').on('click', function(e){
        e.preventDefault();
        $('.ipModuleDesignConfig .ipsForm').submit();
    });

    $('.ipModuleDesignConfig .ipsForm').validator(validatorConfig);

    $('.ipModuleDesignConfig .ipsCancel').off('click').on('click', function(e){
        e.preventDefault();
        window.parent.ipDesignCloseOptions(e);
    });

    $('.ipModuleDesignConfig .ipsDefault').off('click').on('click', function(e){
        e.preventDefault();
        var restoreDefault = 1;
        ipDesign.openLink(window.location.href.split('#')[0], restoreDefault);
    });


    $('.ipModuleDesignConfig .ipsForm input').on('change', ipDesign.livePreviewUpdate);
    $('.ipModuleDesignConfig .ipsForm select').on('change', ipDesign.livePreviewUpdate);

    ipDesign.resize();
    $(window).bind("resize.ipModuleDesign", ipDesign.resize);
});


var ipDesign = new function() {
    var lastSerialized = null;
    var lastValues = {};



    this.openLink = function (href, restoreDefault) {
console.log('openLink ' + href);
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
                if (lastValues[optionName] != curValue) {
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