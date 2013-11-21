<<?php echo $tag ?> class="ipModuleInlineManagement ipmString <?php echo $cssClass ?> <?php echo str_replace(' ', '',$value) == '' ? 'ipmEmpty' : '' ?>" data-cssclass='<?php echo ipEsc($cssClass) ?>' data-key='<?php echo ipEsc($key) ?>' data-htmltag='<?php echo ipEsc($tag) ?>' data-defaultvalue='<?php echo ipEsc($defaultValue) ?>'>
    <?php echo $value == '' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : nl2br(ipEsc($value)) ?>
</<?php echo $tag ?>>
