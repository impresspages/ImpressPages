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
<hr/>
<br/>
<a class="ipAdminButton ipsConfirm" href="#"><?php _e('Confirm', 'ipAdmin'); ?></a>
<a class="ipAdminButton ipsCancel" href="#"><?php _e('Cancel', 'ipAdmin'); ?></a>
