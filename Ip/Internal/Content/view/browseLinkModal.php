<div class="ip">
    <div id="ipBrowseLinkModal" class="modal fade">
        <a class="ipsItemTemplate hidden list-group-item" href="#"></a>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Select Link Target', 'ipAdmin') ?></h4>
                </div>
                <div class="modal-body">
                    <iframe class="ipsPageSelectIframe" style="overflow: hidden; width: 100%; height: 300px;" src="<?php echo ipConfig()->baseUrl() ?>?aa=Pages&disableAdminBar=1&disableActions=1">

                    </iframe>
<!--                    <div class="ipSitemap"></div>-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary disabled" ><?php echo __('Select', 'ipAdmin') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel', 'ipAdmin') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
