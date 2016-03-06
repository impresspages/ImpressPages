<div class="form-group type-<?php echo $field->getTypeClass(); ?><?php if ($field->getName() != null) {
    echo " name-" . $field->getName();
} ?><?php if ($field->isRequired()) {
    echo " required";
} ?>">
    <?php if ($field->getLayout() !== \Ip\Form\Field::LAYOUT_NO_LABEL){ ?>
        <label for="<?php echo $field->getId(); ?>">
            <?php echo esc($field->getLabel()); ?>
        </label>
    <?php } ?>
    <?php echo $field->render($this->getDoctype(), \Ip\Form::ENVIRONMENT_ADMIN); ?>
    <div class="help-error"></div>
    <?php if ($field->getNote()) { ?>
        <div class="help-block"><?php echo $field->getNote(); ?></div>
    <?php } ?>
    <?php if ($field->getHint()) { ?>
        <div class="help-hint"><?php echo $field->getHint(); ?></div>
    <?php } ?>
</div>
