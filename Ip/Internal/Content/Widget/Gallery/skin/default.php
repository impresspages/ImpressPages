<?php if (isset($images) && is_array($images)){ ?>
<ul>
<?php
foreach ($images as $imageKey => $image) {
    $curImage = isset($image['imageSmall']) ? $image['imageSmall'] : '';
    $curImageBig = isset($image['imageBig']) ? $image['imageBig'] : '';
    $curTitle = isset($image['title']) ? $image['title'] : '';
?>
    <li>
        <a rel="lightbox" href="<?php echo esc($curImageBig) ?>" title="<?php echo esc($curTitle); ?>">
            <img src="<?php echo esc($curImage) ?>" alt="<?php echo esc($curTitle); ?>" />
        </a>
    </li>
<?php } ?>
</ul>
<?php } ?>
