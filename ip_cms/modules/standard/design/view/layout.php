<div>
    <?php echo $this->esc($themeName); ?>
    <?php echo $this->esc($themeVersion); ?>
    <img width="200" src="<?php echo $this->esc($themePreviewImage) ?>" alt="<?php echo addslashes($themeName); ?>" />
</div>
<iframe style="width: 900px; height: 500px; clear: both;" src="<?php echo addslashes($marketUrl) ?>">

</iframe>
<br/>
<iframe  style="width: 900px; height: 500px; clear: both;"  src="<?php echo $previewUrl ?>">

</iframe>