
$(document).ready(function() {
    $('body').append(ipModuleDesignConfiguration);
    $('a').off('click').on('click', ipDesign.openLink);
    $('.ipModuleDesignConfig .ipsForm').on('submit', ipDesign.apply);
    $('.ipModuleDesignConfig .ipsSave').on('click', function(e){
        e.preventDefault();
        $('.ipModuleDesignConfig .ipsForm').submit();
    });
    $('.ipModuleDesignConfig .ipsCancel').on('click', function(e){
        e.preventDefault();
        window.parent.ipDesignCloseOptions(e);
    });

    setInterval(ipDesign.livePreviewUpdate, 50);

    $('.ipModuleDesignConfig .ipmFormContainer').css('maxHeight', $(window).height() - 200);
});


var ipDesign = new function() {
    var lastSerialized = null;
    var lastValues = {};

    this.openLink = function (e) {
        e.preventDefault();
        var href = $(e.currentTarget).attr('href');
        href = href + '?ipDesignPreview=1';
        window.location = href;
    }

    this.apply = function (e) {
        e.preventDefault();
        $form = $(this);

        $.ajax({
            url: ip.baseUrl,
            dataType: 'json',
            type : 'POST',
            data: $form.serialize(),
            success: function (response){
                if (response.status && response.status == 'success') {
                    var refreshUrl = window.location.href.split('#')[0];
                    window.location = refreshUrl;
                } else {
                    if (response.errors) {
                        form.data("validator").invalidate(response.errors);
                    }
                }
            }
        });
    }

    this.livePreviewUpdate = function() {
        var $form = $('.ipModuleDesignConfig .ipsForm');
        if (lastSerialized == null) {
            lastSerialized = $form.serialize();
            return;
        }

        var curSerialized = $form.serialize();

        if (curSerialized != lastSerialized) {
            for (optionNameIndex in ipModuleDesignOptionNames) {
                var optionName = ipModuleDesignOptionNames[optionNameIndex];
                var curValue = getValueByName(optionName, curSerialized);
                if (lastValues[optionName] != curValue) {
                    if (typeof(window['ipDesignOption_' + optionName]) === "function") {
                        eval('ipDesignOption_' + optionName + '(curValue);');
                    }
                }
            }

//            var val = $('.ipModuleDesignConfig .ipsForm').find('input[name=\'backgroundColor\']').val();
//            ipDesignOption_backgroundColor(val);
//            console.log(val);
        }

        lastSerialized = curSerialized;
    }


    var getValueByName = function(name, values) {
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec('?' + values);
        return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

};