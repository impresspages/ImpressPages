<div class="ipsContainer">
    <?php if (ipIsManagementState() && empty($videoHtml)) { ?>
        <img style="max-width: 100%; cursor: pointer;" class="defaultImage" src="<?php echo ipFileUrl('Ip/Internal/Content/Widget/Video/assets/video.svg') ?>" /> <!-- //TODOX MOVE STYLE TO CSS #videocss -->
    <?php } else { ?>
        <?php echo isset($videoHtml) ? $videoHtml : ''; ?>
    <?php } ?>
    <div style="clear: both;"></div>

</div>


