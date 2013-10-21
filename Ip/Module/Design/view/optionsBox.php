<div class="ip ipModuleDesignConfig">
    <div class="modal-dialog ipsDialog">
        <div class="modal-content">
            <div class="modal-header ipsDragHandler">
                <h4 class="modal-title"><i class="icon-cogs"></i> <?php echo $this->escPar('standard/design/admin_translations/theme_options') ?></h4>
            </div>
            <div class="ipsBody ipmBody">
                <div class="ipgHide ipsReload alert alert-block">
                    <a class="ipsReload" href="#"><?php echo $this->escPar('standard/design/admin_translations/reload_notice') ?></a>
                </div>
                <?php echo $form->render(); ?>
            </div>
            <div class="modal-footer ipsActions">
                <a href="#" class="btn btn-success btn-xs ipsSave"><?php echo $this->escPar('standard/configuration/admin_translations/save'); ?></a>
                <a href="#" class="btn btn-default btn-xs ipsDefault"><?php echo $this->escPar('standard/design/admin_translations/preview_defaults') ?></a>
                <a href="#" class="btn btn-default btn-xs ipsCancel"><?php echo $this->escPar('standard/configuration/admin_translations/cancel') ?></a>
            </div>
        </div>
    </div>
</div>

