<div class="ip">
    <div id="ipWidgetFormPopup" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Contact form', 'ipAdmin') ?></h4>
                </div>
                <div class="modal-body">
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
                            <a href="#" class="ipaButton ipaFieldOptions"><?php __('Options', 'ipAdmin') ?></a>
                            <a href="#" class="ipaButton ipaFieldRemove"><?php __('Remove', 'ipAdmin') ?></a>
                        </div>
                        <div class="ipaFieldOptionsPopup"></div>
                    </div>
                    <button type="button" class="btn btn-default ipaFieldAdd"><?php echo __('Add new', 'ipAdmin') ?></button>
                    <div class="ipaOptions">
                        <label class="ipAdminLabel"><?php __('Thank you message', 'ipAdmin'); ?></label>
                        <textarea class="ipWidgetIpFormSuccess"><?php echo isset($success) ? esc($success, 'textarea') : ''; ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>