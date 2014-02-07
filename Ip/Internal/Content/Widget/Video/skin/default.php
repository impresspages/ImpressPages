<div class="ipsContainer">
    <?php if (ipIsManagementState() && empty($videoHtml)) { ?>
        <img style="max-width: 100%; cursor: pointer;" class="defaultImage" src="<?php echo ipFileUrl('Ip/Internal/Content/Widget/Video/assets/video.gif') ?>" /> <!-- //TODOX MOVE STYLE TO CSS -->
    <?php } else { ?>
        <?php echo isset($videoHtml) ? $videoHtml : ''; ?>
    <?php } ?>
</div>


