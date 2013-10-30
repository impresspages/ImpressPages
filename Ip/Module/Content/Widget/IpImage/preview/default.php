<?php if (isset($imageSmall) && $imageSmall != ''){ ?>
<img src="<?php echo \Ip\Config::baseUrl($imageSmall) ?>" alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" title="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"/>
<?php } ?>
