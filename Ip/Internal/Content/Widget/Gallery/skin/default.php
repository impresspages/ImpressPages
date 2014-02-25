<?php if (isset($images) && is_array($images)){ ?>
<ul>
<?php foreach ($images as $imageKey => $image) { ?>
    <li class="ipsItem">
        <a
            <?php if ($image['type'] == 'lightbox' && !ipIsManagementState()){ ?>
                rel="lightbox"
                href="<?php echo esc($image['imageBig'], 'attr') ?>"
            <?php } ?>
            <?php if ($image['type'] == 'link'){ ?>
                href="<?php echo esc($image['url'], 'attr') ?>"
                <?php echo $image['blank'] ? ' target="_blank" ' : 'noblank' ?>
            <?php } ?>
            title="<?php echo esc($image['title']); ?>">
            <img class="ipsImage" src="<?php echo esc($image['imageSmall'], 'attr') ?>" alt="<?php echo esc($image['title'], 'attr'); ?>" />
        </a>
    </li>
<?php } ?>
</ul>
<?php } ?>
