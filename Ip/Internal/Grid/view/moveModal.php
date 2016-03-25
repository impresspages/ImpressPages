<div class="ipsMoveModal modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php _e('Move record to another position', 'Ip-admin'); ?></h4>
            </div>
            <div class="modal-body ipsBody">
                <?php echo __('Please enter position number where selected record has to be moved to.', 'Ip-admin') ?>
                <?php
                echo $moveForm->render();
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'Ip-admin') ?></button>
                <button type="button" class="ipsConfirm btn btn-primary"><?php _e('Move', 'Ip-admin') ?></button>
            </div>
        </div>
    </div>
</div>
