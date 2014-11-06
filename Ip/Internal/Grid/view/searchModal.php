<div class="ipsSearchModal modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php _e('Search', 'Ip-admin'); ?></h4>
            </div>
            <div class="modal-body ipsBody">
                <?php if (count($searchForm->getFieldsets()) > 1) { ?>
                    <ul class="nav nav-tabs" role="tablist">
                        <?php foreach($searchForm->getFieldsets() as $key => $fieldset) { ?>
                            <li class="<?php echo $key == 0 ? 'active' : '' ?>"><a href="#<?php echo escAttr($fieldset->getAttribute('id')) ?>" role="tab" data-toggle="tab"><?php echo esc($fieldset->getLabel()) ?></a></li>
                            <?php $fieldset->setLabel(' '); ?>
                        <?php } ?>
                    </ul>
                <?php } ?>
                <?php echo $searchForm ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'Ip-admin') ?></button>
                <button type="button" class="ipsSearch btn btn-primary"><?php _e('Search', 'Ip-admin') ?></button>
            </div>
        </div>
    </div>
</div>
