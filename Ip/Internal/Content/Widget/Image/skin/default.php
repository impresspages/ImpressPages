<?php if (isset($imageSmall) && $imageSmall != ''){ ?>
    <a
        <?php if ($type == 'lightbox' && !ipIsManagementState()){ ?>
            rel="lightbox"
            href="<?php echo esc($imageBig, 'attr') ?>"
        <?php } ?>
        <?php if ($type == 'link'){ ?>
            href="<?php echo esc($url, 'attr') ?>"
            <?php echo $blank ? ' target="_blank" ' : '' ?>
        <?php } ?>
        title="<?php echo isset($title) ? esc($title, 'attr') : '' ?>"
        data-description="<?php echo isset($description) ? esc($description, 'attr') : ''; ?>"
        >
        <img class="ipsImage" src="<?php echo esc($imageSmall) ?>" alt="<?php echo isset($title) ? esc($title, 'attr') : ''; ?>" title="<?php echo isset($title) ? esc($title, 'attr') : ''; ?>" />
    </a>
<?php } else { ?>
    <div class="ipsImage">&nbsp;</div>
<?php } ?>
