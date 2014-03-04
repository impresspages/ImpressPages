<?php if (!$empty) { ?>
<img
    class="<?php echo esc($cssClass); ?>"
    src="<?php echo esc($value); ?>"
    style="<?php if(!empty($options['height'])) { echo 'height: '.$options['height'].'px;'; } ?><?php if(!empty($options['width'])) { echo 'width: '.$options['width'].'px;'; } ?>"
    alt=""
/>
<?php } ?>
