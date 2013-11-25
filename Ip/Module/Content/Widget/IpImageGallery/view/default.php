<?php if (isset($images) && is_array($images)){ ?>
<ul>
<?php
foreach ($images as $imageKey => $image) {
    $curImage = isset($image['imageSmall']) ? $image['imageSmall'] : '';
    $curImageBig = isset($image['imageBig']) ? $image['imageBig'] : '';
    $curTitle = isset($image['title']) ? $image['title'] : '';
?>
    <li>
        <a href="<?php echo esc(ipConfig()->baseUrl($curImageBig)) ?>" title="<?php echo esc($curTitle); ?>">
            <img src="<?php echo esc(ipConfig()->baseUrl($curImage)) ?>" alt="<?php echo esc($curTitle); ?>" />
        </a>
    </li>
<?php } ?>
</ul>
<?php } ?>
