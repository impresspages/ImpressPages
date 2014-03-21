<div class="ip">
    <div class="hidden" id="ipWidgetImageMenu">
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
                    <h4 class="modal-title"><?php echo __('Edit image', 'Ip-admin') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="ipsEditScreen"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'Ip-admin') ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php _e('Confirm', 'Ip-admin') ?></button>
                </div>
            </div>
        </div>
    </div>
    <div id="ipWidgetImageLinkPopup" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Link', 'Ip-admin') ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo $linkForm ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'Ip-admin') ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php _e('Confirm', 'Ip-admin') ?></button>
                </div>
            </div>
        </div>
    </div>
    <div id="ipWidgetImageSettingsPopup" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Settings', 'Ip-admin') ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo $settingsForm ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'Ip-admin') ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php _e('Confirm', 'Ip-admin') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
