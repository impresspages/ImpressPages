<?php if (isset($imageSmall) && $imageSmall != ''){ ?>
<img
    style="float: left;" src="<?php echo BASE_URL.$imageSmall ?>"
    title="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"
    alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" />
<?php } ?>
<?php echo isset($text) ? $text : '' ?>
<div class="clear">
    <!--  -->
</div>
