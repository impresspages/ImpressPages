$.each($('.ipsModuleForm .ipmType-color'), function(){
    var $this = $(this);
    $this.find('.ipsColorPicker').spectrum({
        move: function(color) {
            $this.find('.ipsColorPicker').spectrum("set", color.toHexString());
        }
    });

});
