var validatorConfig = {
    'lang' : '<?php echo addslashes($languageCode); ?>',
    'errorClass' : 'ipmControlError',
    'messageClass' : 'ipmErrorMessage',
    'position' : 'top left',
    'offset' : [-3, 0],
    'onFail' : function(e, errors) {
        $.each(errors, function() {
            var $control = this.input;
            $control.parents('.ipmField').addClass('ipmError');
        });
    },
    'onSuccess' : function(e, valids) {
        $.each(valids, function() {
            var $control = $(this);
            $control.parents('.ipmField').removeClass('ipmError');
        });
    }
};


$.tools.validator.localize('<?php echo addslashes($languageCode); ?>', {
    '*'           : '<?php echo addslashes($this->par('developer/form/error_messages/unknown')) ?>',
    ':email'      : '<?php echo addslashes($this->par('developer/form/error_messages/email')) ?>',
    ':number'     : '<?php echo addslashes($this->par('developer/form/error_messages/number')) ?>',
    ':url'        : '<?php echo addslashes($this->par('developer/form/error_messages/url')) ?>',
    '[max]'       : '<?php echo addslashes($this->par('developer/form/error_messages/max')) ?>',
    '[min]'       : '<?php echo addslashes($this->par('developer/form/error_messages/min')) ?>',
    '[required]'  : '<?php echo addslashes($this->par('developer/form/error_messages/required')) ?>'
});
