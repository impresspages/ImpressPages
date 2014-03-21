<div class="ipsUpdateMenuModal modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php _e('Update menu', 'Ip-admin') ?></h4>
            </div>
            <div class="modal-body">
                <button class="btn btn-default ipsDelete" role="button">
                    <i class="fa fa-delete"></i><?php _e('Delete', 'Ip-admin') ?>
                </button>
                <div class="ipsDeleteConfirmation hidden">
                    <div class="alert alert-danger"><?php _e('All pages inside this menu will be deleted. Are you sure you want to proceed?', 'Ip-admin'); ?></div>
                    <button class="btn btn-default ipsDeleteProceed" role="button">
                        <i class="fa fa-delete"></i><?php _e('Proceed', 'Ip-admin') ?>
                    </button>
                    <button class="btn btn-default ipsDeleteCancel" role="button">
                        <i class="fa fa-delete"></i><?php _e('Cancel', 'Ip-admin') ?>
                    </button>
                </div>
                <div class="ipsBody"></div>
            </div>
            <div class="modal-footer ipsModalActions">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'Ip-admin') ?></button>
                <button type="button" class="ipsSave btn btn-primary"><?php _e('Save', 'Ip-admin') ?></button>
            </div>
        </div>
    </div>
</div>
