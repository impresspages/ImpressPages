<?php if (isset($imageSmall) && $imageSmall != ''){ ?>
<img class="ipsImage" src="<?php echo esc($imageSmall) ?>" alt="<?php echo isset($title) ? esc($title) : ''; ?>" title="<?php echo isset($title) ? esc($title) : ''; ?>"/>
<?php } ?>
