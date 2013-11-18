<div class="ip ipModuleDesignConfig">
    <div class="modal-dialog ipsDialog">
        <div class="modal-content">
            <div class="modal-header ipsDragHandler">
                <h4 class="modal-title"><i class="icon-cogs"></i> <?php _e('Theme options', 'ipAdmin') ?></h4>
            </div>
            <div class="ipsBody ipmBody">
                <div class="ipgHide ipsReload alert alert-block">
                    <a class="ipsReload" href="#"><?php _e('Preview window needs to be reloaded', 'ipAdmin') ?></a>
                </div>
                <?php echo $form->render(); ?>
            </div>
            <div class="modal-footer ipsActions">
                <a href="#" class="btn btn-success btn-xs ipsSave"><?php _e('save', 'ipAdmin'); ?></a>
                <a href="#" class="btn btn-default btn-xs ipsDefault"><?php _e('Preview defaults', 'ipAdmin') ?></a>
                <a href="#" class="btn btn-default btn-xs ipsCancel"><?php _e('Cancel', 'ipAdmin') ?></a>
            </div>
        </div>
    </div>
</div>

