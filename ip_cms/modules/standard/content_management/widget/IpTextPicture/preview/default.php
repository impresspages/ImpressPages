<?php if (isset($pictureSmall) && $pictureSmall != ''){ ?>
    <img style="float: left;" src="<?php echo BASE_URL.$pictureSmall ?>" title="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" />
<?php } ?>
<?php echo isset($text) ? $text : '' ?>
<div class="clear"><!--  --></div>