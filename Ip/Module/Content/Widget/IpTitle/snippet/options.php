<div class="ip">
    <div id="ipWidgetTitleOptions" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Options', 'ipAdmin') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="ipsTitleOptions">
                        <label class="ipAdminLabel"><?php _e('Anchor', 'ipAdmin') ?> <span class="ipsAnchorPreview ipmAnchorPreview"><?php echo $curUrl ?>#</span><br/>
                            <input name="id" class="ipAdminInput ipsAnchor" value="" /></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel', 'ipAdmin') ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php echo __('Confirm', 'ipAdmin') ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>