<img
     data-key="<?php echo $this->esc($key) ?>"
     data-defaultvalue="<?php echo $this->esc($defaultValue) ?>"
     data-options='<?php echo json_encode($options); ?>'
     data-cssclass="<?php echo $this->esc($cssClass) ?>"
     class="ipModuleInlineManagement ipmImage <?php echo $empty ? 'ipmEmpty' : '' ?> <?php echo $this->esc($cssClass) ?>"
     src="<?php echo $empty ? ipGetConfig()->coreModuleUrl('InlineManagement/public/empty.gif') : ipGetConfig()->baseUrl($this->esc($value)) ?>" alt="" />