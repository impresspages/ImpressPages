<?php
/** @var $form \Ip\Form */
?>
<form <?php echo $form->getClassesStr(); ?> <?php echo $form->getAttributesStr(); ?>
    method="<?php echo $form->getMethod(); ?>" action="<?php echo $form->getAction(); ?>" enctype="multipart/form-data">
    <?php foreach ($form->getFieldsets() as $fieldsetKey => $fieldset) { ?>
        <fieldset <?php echo $fieldset->getAttributesStr($this->getDoctype()) ?>>
            <?php if ($fieldset->getLabel()) { ?>
                <legend><?php echo esc($fieldset->getLabel()); ?></legend>
            <?php } ?>
            <?php foreach ($fieldset->getFields() as $fieldKey => $field) { ?>
                <?php
                switch ($field->getLayout()) {
                    case \Ip\Form\Field::LAYOUT_DEFAULT:
                    case \Ip\Form\Field::LAYOUT_NO_LABEL:
                        echo ipView('field.php', array('field' => $field))->render() . "\n";
                        break;
                    case \Ip\Form\Field::LAYOUT_BLANK:
                    default:
                        echo $field->render($this->getDoctype(), \Ip\Form::ENVIRONMENT_PUBLIC) . "\n";
                        break;
                }
                ?>
            <?php } ?>
        </fieldset>
    <?php } ?>
</form>
