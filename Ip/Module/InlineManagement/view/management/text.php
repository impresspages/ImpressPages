<<?php echo $tag ?> class="ipModuleInlineManagement ipmText <?php echo $cssClass ?> <?php echo str_replace(' ', '',$value) == '' ? 'ipmEmpty' : '' ?>" data-cssclass='<?php echo $this->esc($cssClass) ?>' data-key='<?php echo $this->esc($key) ?>' data-htmltag='<?php echo $this->esc($tag) ?>' data-defaultvalue='<?php echo $this->esc($defaultValue) ?>'>
    <?php echo $value == '' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : $value ?>
</<?php echo $tag ?>>
