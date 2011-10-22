<?php if (isset($pictureSmall) && $pictureSmall != ''){ ?>
    <img width="100px" height="100px" src="<?php echo BASE_URL.$pictureSmall ?>" title="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" />
<?php } ?>

