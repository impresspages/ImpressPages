<<?php echo $tag ?> class="ipModuleInlineManagement ipmText <?php echo $cssClass ?> <?php echo str_replace(' ', '',$value) == '' ? 'ipmEmpty' : '' ?>" data-cssclass='<?php echo ipEsc($cssClass) ?>' data-key='<?php echo ipEsc($key) ?>' data-htmltag='<?php echo ipEsc($tag) ?>' data-defaultvalue='<?php echo ipEsc($defaultValue) ?>'>
    <?php echo $value == '' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : $value ?>
</<?php echo $tag ?>>
