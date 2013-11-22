<img
     data-key="<?php echo ipEsc($key) ?>"
     data-defaultvalue="<?php echo ipEsc($defaultValue) ?>"
     data-options='<?php echo json_encode($options); ?>'
     data-cssclass="<?php echo ipEsc($cssClass) ?>"
     class="ipModuleInlineManagement ipmImage <?php echo $empty ? 'ipmEmpty' : '' ?> <?php echo ipEsc($cssClass) ?>"
     src="<?php echo $empty ? ipConfig()->coreModuleUrl('InlineManagement/assets/empty.gif') : ipEsc($value) ?>" alt="" />