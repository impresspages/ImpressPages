<?php if (isset($images) && is_array($images)) { ?>
<div class="_container">
<?php foreach ($images as $imageKey => $image) { ?>
    <div class="_item ipsItem">
        <a
            <?php if ($image['type'] == 'lightbox' && !ipIsManagementState()) { ?>
                rel="lightbox"
                href="<?php echo escAttr($image['imageBig']); ?>"
            <?php } ?>
            <?php if ($image['type'] == 'link') { ?>
                href="<?php echo escAttr($image['url']); ?>"
                <?php echo $image['blank'] ? ' target="_blank" ' : ''; ?>
                <?php echo $image['nofollow'] ? ' rel="nofollow" ' : ''; ?>
            <?php } ?>
            class="_link"
            title="<?php echo esc($image['title']); ?>"
            data-description="<?php echo isset($image['description']) ? escAttr($image['description']) : ''; ?>"
            >
            <img class="_image ipsImage" src="<?php echo escAttr($image['imageSmall']); ?>" alt="<?php echo escAttr($image['title']); ?>" />
        </a>
    </div>
<?php } ?>
</div>
<?php } ?>
