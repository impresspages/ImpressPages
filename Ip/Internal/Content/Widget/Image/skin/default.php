<?php if (isset($imageSmall) && $imageSmall != ''){ ?>
    <a
        <?php if ($type == 'lightbox' && !ipIsManagementState()){ ?>
            rel="lightbox"
            href="<?php echo escAttr($imageBig) ?>"
        <?php } ?>
        <?php if ($type == 'link'){ ?>
            href="<?php echo escAttr($url) ?>"
            <?php echo $blank ? ' target="_blank" ' : '' ?>
            <?php echo $nofollow ? ' rel="nofollow" ' : '' ?>
        <?php } ?>
        title="<?php echo isset($title) ? escAttr($title) : '' ?>"
        data-description="<?php echo isset($description) ? escAttr($description) : ''; ?>"
        >
        <img class="ipsImage" src="<?php echo escAttr($imageSmall) ?>" alt="<?php echo isset($title) ? escAttr($title) : ''; ?>" title="<?php echo isset($title) ? escAttr($title) : ''; ?>" />
    </a>
<?php } else { ?>
    <div class="ipsImage">&nbsp;</div>
<?php } ?>
