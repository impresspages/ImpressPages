$.each($('.ipsModuleForm .ipmType-color'), function(){
    var $this = $(this);
    var lastColor = $this.find('.ipsColorPicker').val();


    $this.find('.ipsColorPicker').spectrum({
        move: function(color) {
            $this.find('.ipsColorPicker').spectrum("set", color.toHexString());
        },
        show: function(color){console.log('show');
            lastColor = color.toHexString();
            $('.sp-cancel').on('click', function(){$this.find('.ipsColorPicker').spectrum("set", lastColor);});
            $('.sp-choose').on('click', function(){lastColor = $this.find('.ipsColorPicker').val();});
        }


    });



});
