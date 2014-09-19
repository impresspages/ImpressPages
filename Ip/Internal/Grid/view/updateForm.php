<?php
/**
 * @var $updateForm \Ip\Form
 */
?>
<?php if (count($updateForm->getFieldsets()) > 1) { ?>
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach($updateForm->getFieldsets() as $key => $fieldset) { ?>
            <li class="<?php echo $key == 0 ? 'active' : '' ?>"><a href="#<?php echo escAttr($fieldset->getAttribute('id')) ?>" role="tab" data-toggle="tab"><?php echo esc($fieldset->getLabel()) ?></a></li>
            <?php $fieldset->setLabel(' '); ?>
        <?php } ?>
    </ul>
<?php } ?>
<?php echo $updateForm ?>
