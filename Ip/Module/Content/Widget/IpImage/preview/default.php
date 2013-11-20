<?php if (isset($imageSmall) && $imageSmall != ''){ ?>
<img src="<?php echo ipGetConfig()->baseUrl($imageSmall) ?>" alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" title="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"/>
<?php } ?>
