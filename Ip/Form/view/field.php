<div class="form-group type-<?php echo $field->getTypeClass(); ?><?php if($field->isRequired()) { echo " required"; } ?>">
    <label for="<?php echo $field->getId(); ?>">
        <?php echo $this->esc($field->getLabel()); ?>
    </label>
    <?php echo $field->render($this->getDoctype()); ?>
<?php if($field->getNote()) { ?>
    <p class="help-block"><?php echo $field->getNote(); ?></p>
<?php } ?>
<?php if($field->getHint()) { ?>
    <p class="help-block"><?php echo $field->getHint(); ?></p>
<?php } ?>
</div>
