<?php if (isset($imageSmall) && $imageSmall != ''){ ?>
<img
    src="<?php echo BASE_URL.$imageSmall ?>"
    title="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"
    alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" />
<?php } ?>

