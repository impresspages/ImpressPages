<?php foreach($values as $value) { ?>
    <?php if ($value['fieldClass'] == 'Ip\Form\Field\Textarea'){ ?>
        <b><?php echo esc($value['title']); ?>:</b><br/>
        <?php echo str_replace("\n", "<br/>", esc($value['value'])); ?><br/>
    <?php } elseif ($value['fieldClass'] != 'Ip\Form\Field\File') { ?>
        <?php if (is_array($value['value'])) { $value['value'] = implode(', ', $value['value']);} ?>
        <b><?php echo esc($value['title']); ?>:</b> <?php echo esc($value['value']); ?><br/>
    <?php } ?>
<?php } ?>
