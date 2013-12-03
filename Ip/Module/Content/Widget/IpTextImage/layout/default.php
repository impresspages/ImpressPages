<?php if (isset($imageSmall) && $imageSmall != ''){ ?>
<img class="ipwImage" src="<?php echo esc($imageSmall) ?>" alt="<?php echo isset($title) ? esc($title) : ''; ?>" />
<?php } ?>
<div class="ipwText">
<?php echo isset($text) ? $text : ''; ?>
</div>
