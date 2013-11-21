<?php foreach($values as $value) { ?>
    <?php if ($value['fieldClass'] == 'Ip\Form\Field\Textarea'){ ?>
        <b><?php echo ipEsc($value['title']); ?>:</b><br/>
        <?php echo str_replace("\n", "<br/>", ipEsc($value['value'])); ?><br/>
    <?php } elseif ($value['fieldClass'] != 'Ip\Form\Field\File') { ?>
        <b><?php echo ipEsc($value['title']); ?>:</b> <?php echo ipEsc($value['value']); ?><br/>
    <?php } ?> 
<?php } ?>
