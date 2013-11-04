<?php foreach($values as $value) { ?>
    <?php if ($value['fieldClass'] == 'Ip\Form\Field\Textarea'){ ?>
        <b><?php echo $this->esc($value['title']); ?>:</b><br/>
        <?php echo str_replace("\n", "<br/>", $this->esc($value['value'])); ?><br/>
    <?php } elseif ($value['fieldClass'] != 'Ip\Form\Field\File') { ?>
        <b><?php echo $this->esc($value['title']); ?>:</b> <?php echo $this->esc($value['value']); ?><br/>
    <?php } ?> 
<?php } ?>
