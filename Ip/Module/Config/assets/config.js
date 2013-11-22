
$( document ).ready(function() {
    "use strict";

    IpConfig.init();

    $('.ipsAutoSave').on('change', IpConfig.autoSaveValue);
    $('.ipsAutoSave').on('keyup', IpConfig.autoSaveValue);

    $('.ipsConfigForm').validator(validatorConfig);
    $('.ipsConfigForm').data("validator").checkValidity();

});

var IpConfig = new function () {
    "use strict";
    var queue = [],
        processing = false;

    this.init = function () {
        updateCronUrl();
    }

    var queueAdd = function (fieldName) {
        queue = removeFromArray(queue, fieldName);
        queue.push(fieldName);
        queueProcess();
    };

    var getFieldValue = function (fieldName) {
        var $field = $('.ips' + fieldName);
        if ($field.attr('type') === 'checkbox') {
            return $field.prop('checked') ? 1 : 0;
        }

        return $field.val();
    }


    var queueProcess = function () {
        if (processing) {
            return;
        }
        processing = true;
        var curItem = queue.shift();

        if (!curItem) {
            processing = false;
            return;
        }

        updateCronUrl();


        var postData = {
            'aa' : 'Config.saveValue',
            'fieldName' : curItem,
            'value' : getFieldValue(curItem),
            'securityToken' : ip.securityToken
        };

        $.ajax({
            url: '',
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                processing = false;
                queueProcess();
            },
            error: function (response) {
                if (response && response.readyState === 4) {
                    if (response.responseText) {
                        alert(response.responseText);
                    } else {
                        alert('Autosave error');
                    }
                }
                processing = false;
                queueProcess();
            }
        });
    }

    var updateCronUrl = function () {
        var $urlText = $('.ipsautomaticCron').closest('.ipmField').find('.ipmCheckboxText');
        var $url = $urlText.find('.ipsUrl');
        var $passField = $('.ipscronPassword').closest('.ipmField');
        if (getFieldValue('automaticCron')) {
            $urlText.addClass('ipgHide');
            $passField.addClass('ipgHide');
        } else {
            $url.text(ip.baseUrl + '?pa=Cron&pass=' + $('.ipscronPassword').val());
            $passField.removeClass('ipgHide');
            $urlText.removeClass('ipgHide');
        }
    }

    this.autoSaveValue = function () {
        $('.ipsConfigForm').data("validator").checkValidity();
        queueAdd($(this).data('fieldname'));
    };

    var removeFromArray = function (dataArray, value) {
        for(var i = dataArray.length - 1; i >= 0; i--) {
            if(dataArray[i] === value) {
                dataArray.splice(i, 1);
            }
        }
        return dataArray;
    }
};

