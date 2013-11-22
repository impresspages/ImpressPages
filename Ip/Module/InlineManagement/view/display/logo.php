<div class="logo <?php echo empty($cssClass) ? '' : $cssClass ?>">
<?php if (isset($type) && $type == 'image') { ?>
    <a href="<?php echo isset($link) ? $link : '' ?>" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : '' ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : '' ?>">
        <img src="<?php echo (ipEsc(!empty($image) ? ipConfig()->baseUrl($image) : ipConfig()->coreModuleUrl('InlineManagement/assets/empty.gif'))) ?>" alt="<?php echo ipEsc(ipGetOption('Config.websiteTitle')); ?>" />
    </a>
<?php } else { ?>
    <a href="<?php echo isset($link) ? $link : '' ?>" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : '' ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : '' ?>">
        <?php echo nl2br(ipEsc(isset($text) ? $text : '')) ?>
    </a>
<?php } ?>
</div>