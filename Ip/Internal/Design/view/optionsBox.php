<div class="ip">
    <div class="ipModuleDesignConfig">
        <div class="modal-dialog ipsDialog">
            <div class="modal-content">
                <div class="modal-header ipsDragHandler">
                    <h4 class="modal-title"><i class="fa fa-cogs"></i> <?php _e('Theme options', 'ipAdmin'); ?></h4>
                </div>
                <div class="_body ipsBody">
                    <div class="alert alert-warning hidden ipsReload">
                        <a href="#" class="ipsReloadButton"><?php _e('Preview window needs to be reloaded', 'ipAdmin'); ?></a>
                    </div>
                    <?php echo $form->render(); ?>
                </div>
                <div class="modal-footer ipsActions">
                    <a href="#" class="btn btn-success btn-sm ipsSave"><?php _e('Save', 'ipAdmin'); ?></a>
                    <a href="#" class="btn btn-default btn-sm ipsDefault"><?php _e('Preview defaults', 'ipAdmin'); ?></a>
                    <a href="#" class="btn btn-default btn-sm ipsCancel"><?php _e('Cancel', 'ipAdmin'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
