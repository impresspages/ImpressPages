<div class="ip ipModuleDesignConfig">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="icon-cogs"></i> <?php echo $this->escPar('standard/design/admin_translations/theme_options') ?></h4>
            </div>
            <div class="modal-body ipsForm">
                <?php echo $form->render(); ?>
            </div>
            <div class="modal-footer ipsActions">
                <a href="#" class="btn btn-success btn-xs ipsSave"><?php echo $this->escPar('standard/configuration/admin_translations/save'); ?></a>
                <a href="#" class="btn btn-default btn-xs ipsDefault"><?php echo $this->escPar('standard/design/admin_translations/restore_defaults') ?></a>
                <a href="#" class="btn btn-default btn-xs ipsCancel"><?php echo $this->escPar('standard/configuration/admin_translations/cancel') ?></a>
            </div>
        </div>
    </div>
</div>

