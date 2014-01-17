<div class="ip">
    <div id="ipWidgetFormPopup" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Form options', 'ipAdmin') ?></h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#ipWidgetFormPopup-fields" data-toggle="tab"><?php echo __('Fields', 'ipAdmin') ?></a></li>
                        <li><a href="#ipWidgetFormPopup-options" data-toggle="tab"><?php echo __('Options', 'ipAdmin') ?></a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="ipWidgetFormPopup-fields">
                            <div class="ipWidget_ipForm_container"></div>
                            <div class="hide">
                                <div class="ipaFieldTemplate">
                                    <a href="#" class="ipaButton ipaFieldMove"><?php _e('Move', 'ipAdmin') ?></a>
                                    <input type="text" class="ipAdminInput ipaFieldLabel" name="label" value="" />
                                    <select class="ipaFieldType">
                                        <?php foreach($fieldTypes as $fieldType) { ?>
                                            <option value="<?php echo esc($fieldType['key']); ?>"><?php echo esc($fieldType['title']); ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="checkbox" class="ipaFieldRequired" />
                                    <a href="#" class="ipaButton ipaFieldOptions"><?php _e('Options', 'ipAdmin') ?></a>
                                    <a href="#" class="ipaButton ipaFieldRemove"><?php _e('Remove', 'ipAdmin') ?></a>
                                </div>
                                <div class="ipaFieldOptionsPopup"></div>
                            </div>
                            <button type="button" class="btn btn-default ipaFieldAdd"><?php _e('Add new', 'ipAdmin') ?></button>
                        </div>

                        <div class="tab-pane" id="ipWidgetFormPopup-options">
                            <div class="ipaOptions">
                                <label class="ipAdminLabel"><?php _e('Thank you message', 'ipAdmin'); ?></label>
                                <textarea class="ipWidgetFormSuccess"><?php echo isset($success) ? esc($success, 'textarea') : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'ipAdmin') ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php _e('Confirm', 'ipAdmin') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
