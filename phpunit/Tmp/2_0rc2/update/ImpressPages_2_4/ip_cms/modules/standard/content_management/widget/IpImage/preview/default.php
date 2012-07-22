<?php if (isset($imageSmall) && $imageSmall != ''){ ?>
<img src="<?php echo BASE_URL.$imageSmall; ?>" alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" title="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"/>
<?php } ?>
