(function($){
    "use strict";

        $('.ipsModuleForm .type-color').each(function() {
            var $this = $(this);
            var lastColor = $this.find('.ipsColorPicker').val();

             $this.find('.ipsColorPicker').spectrum({
                showInput: true,
                cancelText: $this.find('.ipsColorPicker').data('canceltext'),
                chooseText: $this.find('.ipsColorPicker').data('confirmtext'),
                move: function(color) {
                    $this.find('.ipsColorPicker').spectrum("set", color.toHexString());
                },
                show: function (color) {
                    lastColor = color.toHexString();
                    $('.sp-cancel').on('click', function () {
                        $this.find('.ipsColorPicker').spectrum("set", lastColor);
                    });
                    $('.sp-choose').on('click', function () {
                        lastColor = $this.find('.ipsColorPicker').val();
                    });
                }

            });

    });

})(ip.jQuery);
