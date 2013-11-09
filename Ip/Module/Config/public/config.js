
$( document ).ready(function() {
    "use strict";

    $('.ipsAutoSave').on('change', IpConfig.autoSaveValue);

});

var IpConfig = new function () {
    var queue = [],
        processing = false;

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

        var postData = {
            'aa' : 'Config.saveValue',
            'fieldName' : curItem,
            'value' : getFieldValue(curItem)
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
            error: function () {
                alert('Autosave error');
                processing = false;
                queueProcess();
            }
        });
    }


    this.autoSaveValue = function () {
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

