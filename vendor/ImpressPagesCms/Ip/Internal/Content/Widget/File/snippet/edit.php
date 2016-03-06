<div class="ip">
    <div id="ipWidgetFilePopup" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php _e('Files', 'Ip-admin'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="ipWidget_ipFile_container"></div>
                    <div class="hidden">
                        <div class="ipsFileTemplate form-group">
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <button class="btn btn-default ipsFileMove" type="button" title="<?php _e('Drag', 'Ip-admin'); ?>"><i class="fa fa-arrows"></i></button>
                                </div>
                                <input type="text" class="form-control ipsFileTitle" name="title" value="" />
                                <div class="input-group-btn">
                                    <a href="#" class="btn btn-default ipsFileLink" target="_blank" title="<?php _e('Preview', 'Ip-admin'); ?>"><i class="fa fa-external-link"></i></a>
                                    <button class="btn btn-danger ipsFileRemove" type="button" title="<?php _e('Delete', 'Ip-admin'); ?>"><i class="fa fa-trash-o"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="ipsUpload ipAdminButton btn btn-new"><?php _e('Add new', 'Ip-admin'); ?></a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default ipsCancel" data-dismiss="modal"><?php _e('Cancel', 'Ip-admin'); ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php _e('Confirm', 'Ip-admin'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
