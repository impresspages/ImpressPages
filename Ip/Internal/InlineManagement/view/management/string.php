<<?php echo $tag ?> class="ipModuleInlineManagement ipmString <?php echo $cssClass; ?> <?php echo str_replace(' ', '',$value) == '' ? 'ipmEmpty' : ''; ?>" data-cssclass='<?php echo esc($cssClass); ?>' data-key='<?php echo esc($key); ?>' data-htmltag='<?php echo esc($tag); ?>' data-defaultvalue='<?php echo esc($defaultValue); ?>'>
    <?php echo $value == '' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : nl2br(esc($value)); ?>
</<?php echo $tag ?>>
