<div class="ip">
    <div id="ipWidgetFilePopup" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Files', 'ipAdmin') ?></h4>
                </div>

                <div class="modal-body">
                    <a class="ipsUpload">Upload</a>
                    <div class="ipWidget_ipFile_container">
                    </div>
                    <div class="ipgHide">
                        <div class="ipaFileTemplate">
                            <a href="#" class="ipaButton ipaFileMove"><?php _e('move', 'ipAdmin') ?></a>
                            <input type="text" class="ipAdminInput ipaFileTitle" name="title" value="" />
                            <a href="#" class="ipaButton ipaFileLink" target="_blank"><?php _e('preview', 'ipAdmin') ?></a>
                            <a href="#" class="ipaButton ipaFileRemove"><?php _e('remove', 'ipAdmin') ?></a>
                        </div>
                    </div>
                    <a href="#" class="ipAdminButton ipmBrowseButton"><?php _e('Add new', 'ipAdmin') ?></a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel', 'ipAdmin') ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php echo __('Confirm', 'ipAdmin') ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
