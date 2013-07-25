<div>
    <?php echo $this->esc($themeName); ?>
    <?php echo $this->esc($themeVersion); ?>
    <img src="<?php echo $this->esc($themePreviewImage) ?>" alt="<?php echo addslashes($themeName); ?>" />
</div>

<iframe src="<?php echo $previewUrl ?>">

</iframe>