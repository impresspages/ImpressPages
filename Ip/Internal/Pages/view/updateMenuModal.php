<div class="ipsUpdateMenuModal modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php _e('Update menu', 'Ip-admin') ?></h4>
            </div>
            <div class="modal-body">
                <div class="ipsDeleteConfirmation hidden">
                    <div class="alert alert-danger"><?php _e('All pages inside this menu will be deleted. Are you sure you want to proceed?', 'Ip-admin'); ?></div>
                    <button class="btn btn-default ipsDeleteProceed" role="button"><?php _e('Proceed', 'Ip-admin') ?> &nbsp; <i class="fa fa-trash-o"></i></button>
                    <button class="btn btn-default ipsDeleteCancel" role="button"><?php _e('Cancel', 'Ip-admin') ?> &nbsp; <i class="fa fa-trash-o"></i></button>
                </div>
                <div class="ipsBody"></div>
            </div>
            <div class="modal-footer ipsModalActions">
                <button class="btn btn-danger ipsDelete" role="button"><?php _e('Delete', 'Ip-admin') ?> &nbsp; <i class="fa fa-trash-o"></i></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'Ip-admin') ?></button>
                <button type="button" class="ipsSave btn btn-primary"><?php _e('Save', 'Ip-admin') ?></button>
            </div>
        </div>
    </div>
</div>
