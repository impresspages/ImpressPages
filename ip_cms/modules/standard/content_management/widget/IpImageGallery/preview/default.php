<?php if (isset($images) && is_array($images)){ ?>
<ul>
<?php
foreach ($images as $imageKey => $image) {
    $curImage = isset($image['imageSmall']) ? $image['imageSmall'] : '';
    $curTitle = isset($image['title']) ? $image['title'] : '';
?>
    <li>
        <a href="<?php echo BASE_URL.IMAGE_DIR.htmlspecialchars($curTitle); ?>" title="<?php htmlspecialchars($curTitle); ?>">
            <img src="<?php echo htmlspecialchars(BASE_URL.$curImage); ?>" alt="<?php htmlspecialchars($curTitle); ?>" />
        </a>
    </li>
<?php } ?>
</ul>
<?php } ?>
