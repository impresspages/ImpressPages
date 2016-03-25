<div class="ip">
    <div id="ipWidgetHtmlPopup" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php _e('Edit HTML', 'Ip-admin'); ?></h4>
                </div>
                <div class="modal-body">
                    <textarea name="html" class="form-control" rows="10"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'Ip-admin'); ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php _e('Confirm', 'Ip-admin'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="ipWidgetHtmlDisabledJavaScriptNotice" class="hidden"><?php _e('JavaScript is disabled in management mode.', 'Ip-admin') ?></div>
<div id="ipWidgetHtmlDisabledInSafeMode" class="hidden"><?php _e('HTML widget content is hidden in safe mode.', 'Ip-admin') ?></div>
