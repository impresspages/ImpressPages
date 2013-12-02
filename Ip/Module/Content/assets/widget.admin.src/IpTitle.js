/**
 * @package ImpressPages
 *
 *
 */
function IpWidget_IpTitle(widgetObject) {
    "use strict";
    this.widgetObject = null;
    this.data = null;

    this.init = function ($widgetObject, data, editMode) {
        this.widgetObject = $widgetObject;
        this.data = data;


        var customTinyMceConfig = ipTinyMceConfigMin();
        customTinyMceConfig.menubar = false;
        customTinyMceConfig.toolbar = false;
        customTinyMceConfig.setup = function(ed, l) {
            ed.on('change', function(e) {
                $widgetObject.save({title: $widgetObject.find('h1,h2,h3,h4,h5,h6').html()});
            });
        };
        customTinyMceConfig.paste_as_text = true;
        customTinyMceConfig.valid_elements = '';
            customTinyMceConfig.custom_shortcuts = false;

        $widgetObject.find('h1,h2,h3,h4,h5,h6').tinymce(customTinyMceConfig);


        //TODOX refactor this functionality
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

