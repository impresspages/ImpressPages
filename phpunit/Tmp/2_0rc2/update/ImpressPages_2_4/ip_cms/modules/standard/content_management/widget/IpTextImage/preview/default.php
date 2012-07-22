<?php if (isset($imageSmall) && $imageSmall != ''){ ?>
<img class="ipwImage" src="<?php echo BASE_URL.$imageSmall; ?>" alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" />
<?php } ?>
<div class="ipwText">
<?php echo isset($text) ? $text : ''; ?>
</div>
