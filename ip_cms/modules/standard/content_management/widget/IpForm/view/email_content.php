<?php foreach($values as $value) { ?>
    <b><?php echo $this->esc($value['title']); ?>:</b> <?php echo $this->esc($value['value']); ?><br/> 
<?php } ?>
