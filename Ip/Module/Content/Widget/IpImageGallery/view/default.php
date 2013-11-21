<?php if (isset($images) && is_array($images)){ ?>
<ul>
<?php
foreach ($images as $imageKey => $image) {
    $curImage = isset($image['imageSmall']) ? $image['imageSmall'] : '';
    $curImageBig = isset($image['imageBig']) ? $image['imageBig'] : '';
    $curTitle = isset($image['title']) ? $image['title'] : '';
?>
    <li>
        <a href="<?php echo ipEsc(ipConfig()->baseUrl($curImageBig)) ?>" title="<?php echo ipEsc($curTitle); ?>">
            <img src="<?php echo ipEsc(ipConfig()->baseUrl($curImage)) ?>" alt="<?php echo ipEsc($curTitle); ?>" />
        </a>
    </li>
<?php } ?>
</ul>
<?php } ?>
