<?php
/** @var $form \Ip\Form */
?>
<form <?php echo $form->getClassesStr(); ?> <?php echo $form->getAttributesStr(); ?> method="<?php echo $form->getMethod(); ?>" action="<?php echo $form->getAction(); ?>" enctype="multipart/form-data">
    <?php foreach ($form->getPages() as $pageKey => $page) { ?>
    <div class="ipmPage">
        <?php foreach ($page->getFieldsets() as $fieldsetKey => $fieldset) { ?>
        <fieldset class="ipmFieldset">
            <?php if ($fieldset->getLabel()) { ?>
                <legend><?php echo $this->esc($fieldset->getLabel()); ?></legend>
            <?php } ?>
            <?php foreach ($fieldset->getFields() as $fieldKey => $field) { ?>
                <?php 
                    switch ($field->getLayout()) {
                        case \Ip\Form\Field\Field::LAYOUT_DEFAULT :
                            echo $this->subview('field.php', array('field' => $field))->render()."\n";
                            break;
                        case \Ip\Form\Field\Field::LAYOUT_BLANK:
                        default:
                            echo $field->render($this->getDoctype())."\n";
                            break;
                    }
                ?>
            <?php } ?>
        </fieldset>
        <?php } ?>
    </div>
    <?php } ?>
</form>
