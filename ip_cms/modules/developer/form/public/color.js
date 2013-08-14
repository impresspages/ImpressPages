alert('color');
$.each($('.ipsModuleForm .ipmType-color'), function(){
    var $this = $(this);
    $this.find('.ipsColorPicker').spectrum();
});
