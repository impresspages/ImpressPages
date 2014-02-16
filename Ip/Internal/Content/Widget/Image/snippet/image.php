<div class="ip">
    <div class="ipgHide" id="ipWidgetImageMenu">
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group">
                <button class="btn btn-default ipsEdit" role="button"><i class="fa fa-edit"></i></button>
                <button class="btn btn-default ipsLink" role="button"><i class="fa fa-link"></i></button>
                <button class="btn btn-default ipsSettings" role="button"><i class="fa fa-gears"></i></button>
            </div>
        </div>
    </div>
    <div id="ipWidgetImageEditPopup" class="modal"><?php /*Fade breaks image management*/?>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Edit image', 'ipAdmin') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="ipsEditScreen"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'ipAdmin') ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php _e('Confirm', 'ipAdmin') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
