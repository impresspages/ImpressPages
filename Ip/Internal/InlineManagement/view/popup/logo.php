<div class="ip">
    <div id="ipInlineLogoModal" class="modal"><?php /*Fade breaks image management*/?>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Edit image', 'ipAdmin') ?></h4>
                </div>
                <div class="modal-body">
                    <form onsubmit="return false;">
                        <div class="ipmTypeSelect">
                            <label><?php _e('Text', 'ipAdmin'); ?><input type="radio" name="type" value="text" /></label>
                            <div>&nbsp;&nbsp;&nbsp;</div>
                            <label><?php _e('Image logo', 'ipAdmin'); ?><input type="radio" name="type" value="image" /></label>
                        </div>
                        <br/>
                        <br/>
                        <br/>
                        <div class="ipmTextManagement">
                            <input class="ipmLogoText" type="text" value="" />
                            <br/><br/>
                            <div class="ipmFontSelect">
                                <span>Arial</span>
                                <div class="arrow-down"></div>
                                <ul>
                                    <li class="ipmDefaultFont"><?php _e('Default', 'ipAdmin'); ?>,</li>
                                    <?php if (isset($availableFonts) && is_array($availableFonts)) foreach($availableFonts as $font) { ?>
                                        <li><?php echo esc($font); ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <br/><br/>
                            <div class="ipmColorPicker colorPickerSelector"></div>
                            <br/><br/>
                        </div>
                        <div class="ipmImageManagement">
                            <div class="ipsImage"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'ipAdmin') ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php _e('Confirm', 'ipAdmin') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>




