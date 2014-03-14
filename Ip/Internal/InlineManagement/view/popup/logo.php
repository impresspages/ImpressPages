<div class="ip">
    <div class="ipModuleInlineManagementLogoModal ipsModuleInlineManagementLogoModal modal"><?php /* Fade breaks image management */ ?>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php _e('Edit logo', 'ipAdmin'); ?></h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#ipsTypeSelectText" data-toggle="tab" data-logotype="text"><?php _e('Text', 'ipAdmin'); ?></a></li>
                        <li><a href="#ipsTypeSelectImage" data-toggle="tab" data-logotype="image"><?php _e('Image logo', 'ipAdmin'); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="ipsTypeSelectText">
                            <div class="input-group input-group-lg">
                                <input class="form-control ipsLogoText" type="text" value="" />
                                <div class="ipsFontSelect input-group-btn">
                                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                                        <span class="ipsFontName"><?php _e('Default', 'ipAdmin'); ?></span>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="#" class="ipsDefaultFont"><?php _e('Default', 'ipAdmin'); ?>,</a></li>
                                        <?php if (isset($availableFonts) && is_array($availableFonts)) foreach($availableFonts as $font) { ?>
                                            <li><a href="#"><?php echo esc($font); ?></a></li>
                                        <?php } ?>
                                    </ul>
                                    <button class="_colorPicker ipsColorPicker colorPickerSelector"></button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="ipsTypeSelectImage">
                            <div class="ipsImage"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'ipAdmin'); ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php _e('Confirm', 'ipAdmin'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>




