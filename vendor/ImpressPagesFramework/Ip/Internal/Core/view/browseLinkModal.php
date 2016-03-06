<div class="ip">
    <div id="ipBrowseLinkModal" class="modal fade">
        <a class="ipsItemTemplate hidden list-group-item" href="#"></a>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Select Link Target', 'Ip-admin') ?></h4>
                </div>
                <div class="modal-body">
                    <iframe class="ipsPageSelectIframe" style="overflow: hidden; width: 100%; height: 300px;" src="" data-source="<?php echo ipConfig()->baseUrl() ?>?aa=Pages&disableAdminNavbar=1&disableActions=1">

                    </iframe>
<!--                    <div class="ipSitemap"></div>-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary ipsConfirm" ><?php echo __('Select', 'Ip-admin') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel', 'Ip-admin') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
