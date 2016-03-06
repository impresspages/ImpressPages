<div class="logo<?php echo empty($cssClass) ? '' : ' ' . $cssClass; ?>">
<?php if (isset($type) && $type == 'image') { ?>
    <a href="<?php echo isset($link) ? $link : '' ?>" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : '' ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : '' ?>">
        <img src="<?php echo (escAttr(!empty($image) ? ipFileUrl($image) : ipFileUrl('Ip/Internal/InlineManagement/assets/empty.gif'))) ?>" alt="<?php echo esc(ipGetOptionLang('Config.websiteTitle')); ?>" />
    </a>
<?php } else { ?>
    <a href="<?php echo isset($link) ? $link : '' ?>" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : '' ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : '' ?>">
        <?php echo nl2br(esc(isset($text) ? $text : '')) ?>
    </a>
<?php } ?>
</div>
