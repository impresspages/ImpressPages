<div class="logo ipModuleInlineManagement ipmLogo <?php echo $empty ? 'ipmEmpty' : '' ?>" <?php echo empty($cssClass) ? '' : $cssClass ?> data-cssclass='<?php echo $this->esc($cssClass); ?>'>
<?php if (isset($type) && $type == 'image') { ?>
    <a href="<?php echo isset($link) ? $link : '' ?>" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : '' ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : '' ?>">
        <img <?php echo $empty ? 'style="width: 50px; height: 50px;"' : '' ?> src="<?php echo ($this->esc(!$empty ? \Ip\Config::baseUrl($image) : \Ip\Config::oldModuleUrl('developer/inline_management/public/empty.gif'))) ?>" alt="<?php echo $this->escPar('standard/configuration/main_parameters/name'); ?>" />
    </a>
<?php } else { ?>
    <a href="<?php echo isset($link) ? $link : '' ?>" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : '' ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : '' ?>">
        <?php echo !$empty ? nl2br($this->esc($text)) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ?>
    </a>
<?php } ?>
</div>
