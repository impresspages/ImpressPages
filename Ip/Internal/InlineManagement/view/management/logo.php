<div class="logo ipModuleInlineManagement ipsModuleInlineManagementLogo<?php echo $empty ? ' _empty' : ''; ?>" <?php echo empty($cssClass) ? '' : $cssClass; ?> data-cssclass="<?php echo esc($cssClass); ?>">
<?php if (isset($type) && $type == 'image') { ?>
    <a href="<?php echo isset($link) ? $link : ''; ?>" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : ''; ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : ''; ?>">
        <img <?php echo $empty ? 'style="width: 50px; height: 50px;"' : ''; ?> src="<?php echo (esc(!$empty ? ipFileUrl($image) : ipFileUrl('Ip/Internal/InlineManagement/assets/empty.gif'))); ?>" alt="<?php echo esc(ipGetOptionLang('Config.websiteTitle')); ?>" />
    </a>
<?php } else { ?>
    <a href="<?php echo isset($link) ? $link : ''; ?>" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : ''; ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : ''; ?>">
        <?php echo !$empty ? nl2br(esc($text)) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
    </a>
<?php } ?>
</div>
