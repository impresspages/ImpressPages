<?php if (isset($images) && is_array($images)) { ?>
<div class="_container">
<?php foreach ($images as $imageKey => $image) { ?>
    <div class="_item ipsItem">
        <a
            <?php if ($image['type'] == 'lightbox' && !ipIsManagementState()) { ?>
                rel="lightbox"
                href="<?php echo esc($image['imageBig'], 'attr'); ?>"
            <?php } ?>
            <?php if ($image['type'] == 'link') { ?>
                href="<?php echo esc($image['url'], 'attr'); ?>"
                <?php echo $image['blank'] ? ' target="_blank" ' : ''; ?>
            <?php } ?>
            class="_link"
            title="<?php echo esc($image['title']); ?>"
            data-description="<?php echo isset($image['description']) ? esc($image['description'], 'attr') : ''; ?>"
            >
            <img class="_image ipsImage" src="<?php echo esc($image['imageSmall'], 'attr'); ?>" alt="<?php echo esc($image['title'], 'attr'); ?>" />
        </a>
    </div>
<?php } ?>
</div>
<?php } ?>
