/**
 * @package ImpressPages
 *
 *
 */
function IpWidget_IpTitle(widgetObject) {
    "use strict";
    this.widgetObject = widgetObject;

    this.manageInit = function () {
        var $self = this.widgetObject;
        $self.find('.ipsTitleOptionsButton').on('click', function (e) {
            $self.find('.ipsTitleOptions').toggle();
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
        $self.find('.ipsAnchor').on('keydown', $.proxy(updateAnchor, this));
        $self.find('.ipsAnchor').on('change', $.proxy(updateAnchor, this));
        $self.find('.ipsAnchor').on('keyup', $.proxy(updateAnchor, this));
    };

    var updateAnchor = function () {
        var  $preview = this.widgetObject.find('.ipsAnchorPreview');
        var curText = $preview.text();
        var newText = curText.split('#')[0] + '#' + this.widgetObject.find('.ipsAnchor').val();
        $preview.text(newText);
    }

    this.prepareData = function () {
        var widgetInputs = this.widgetObject.find('.ipaBody').find(':input');
        var data = {};
        widgetInputs.each(function (index) {
            data[$(this).attr('name')] = $(this).val();
        });
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }

};