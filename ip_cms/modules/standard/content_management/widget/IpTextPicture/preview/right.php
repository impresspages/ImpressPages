<?php if (isset($pictureSmall) && $pictureSmall != ''){ ?>
<img
    style="float: right;" src="<?php echo BASE_URL.$pictureSmall ?>"
    title="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"
    alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" />
<?php } ?>
<?php echo isset($text) ? $text : '' ?>
<div class="clear">
    <!--  -->
</div>
