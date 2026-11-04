<div class="ipsUpdateModal modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php _e('Close', 'Ip-admin'); ?>">&times;</button>
                <h4 class="modal-title"><?php echo empty($updateTitle) ? esc(__('Edit record', 'Ip-admin', false)) : esc($updateTitle); ?></h4>
            </div>
            <div class="modal-body ipsBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'Ip-admin') ?></button>
                <button type="button" class="ipsConfirm btn btn-primary"><?php _e('Save', 'Ip-admin') ?></button>
            </div>
        </div>
    </div>
</div>
