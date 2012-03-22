<div class="ipfField ipfType-<?php //echo $field->getCssClass(); ?> clearfix">
    <label>
        <span class="ipfLabel"><?php echo $this->esc($field->getLabel()); ?></span>
<?php if($field->isRequired()) { ?>
        <span class="ipfRequired">*</span>
<?php } ?>
        <div class="ipfControl"><?php echo $field->render($this->getDoctype()); ?></div>
    </label>
<?php if($field->getNote()) { ?>
    <div class="ipfNote"><?php echo $field->getNote(); ?></div>
<?php } ?>
<?php if($field->getHint()) { ?>
    <div class="ipfHint"><?php echo $field->getHint(); ?></div>
<?php } ?>
</div>
