$(document).ready(function () {
    "use strict";
    IpConfig.init();

    var $autoSave = $('.ipsAutoSave');
    $autoSave.on('change', IpConfig.autoSaveValue);
    $autoSave.on('keyup', IpConfig.autoSaveValue);

    var $configForm = $('.ipsConfigForm');
    $configForm.validator(validatorConfigAdmin);
    $configForm.data("validator").checkValidity();
    $configForm.on('submit', function (e) {
        e.preventDefault();
    });

});

var IpConfig = new function () {
    "use strict";
    var queue = [],
        processing = false;

    this.init = function () {
        updateCronUrl();

        if (!$('#removeOldEmails').is(':checked')) {
            $('.form-group.name-removeOldEmailsDays').addClass('hidden');
        }
        $('#removeOldEmails').on('click', function(){$('.form-group.name-removeOldEmailsDays').toggleClass('hidden')});

        if (!$('#removeOldRevisions').is(':checked')) {
            $('.form-group.name-removeOldRevisionsDays').addClass('hidden');
        }
        $('#removeOldRevisions').on('click', function(){$('.form-group.name-removeOldRevisionsDays').toggleClass('hidden')});

        $('.ipsAdvancedOptions').on('click', function() {
            $('.ipsConfigFormAdvanced').toggleClass('hidden');

        });
    };

    var queueAdd = function (fieldid) {
        queue = removeFromArray(queue, fieldid);
        queue.push(fieldid);
        queueProcess();
    };

    var getFieldValue = function (fieldid) {
        var $field = $('#' + fieldid);
        if ($field.attr('type') === 'checkbox') {
            return $field.prop('checked') ? 1 : 0;
        }

        return $field.val();
    };

    var getFieldLanguage = function (fieldid) {
        var $field = $('#' + fieldid);
        return $field.data('languageid');
    };

    var getFieldName = function (fieldid) {
        var $field = $('#' + fieldid);
        return $field.data('fieldname');
    };

    var queueProcess = function () {
        if (processing) {
            return;
        }
        processing = true;
        var fieldId = queue.shift();

        if (!fieldId) {
            processing = false;
            return;
        }

        updateCronUrl();


        var postData = {
            'aa': 'Config.saveValue',
            'fieldName': getFieldName(fieldId),
            'value': getFieldValue(fieldId),
            'securityToken': ip.securityToken,
            'languageId': getFieldLanguage(fieldId)
        };
        $.ajax({
            url: '',
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                if (response && response.error) {
                    var errors = {};
                    errors[fieldId] = response.error;
                    $('.ipsConfigForm').data("validator").invalidate(errors);
                }
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
    };

    var updateCronUrl = function () {
        var $urlText = $('.name-cronPassword .ipsUrl');
        // cron should work without password for admin
        var url = ip.baseUrl + '?pa=Cron&pass=' + $('#cronPassword').val();
        $urlText.text(url);
        $urlText.attr('href', url);
    };

    this.autoSaveValue = function () {
        var $this = $(this);
        $('.ipsConfigForm').data("validator").checkValidity();
        queueAdd($this.data('fieldid'));
    };

    var removeFromArray = function (dataArray, value) {
        for (var i = dataArray.length - 1; i >= 0; i--) {
            if (dataArray[i] === value) {
                dataArray.splice(i, 1);
            }
        }
        return dataArray;
    }
};
