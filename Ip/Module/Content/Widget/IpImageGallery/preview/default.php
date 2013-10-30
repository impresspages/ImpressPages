<?php if (isset($images) && is_array($images)){ ?>
<ul>
<?php
foreach ($images as $imageKey => $image) {
    $curImage = isset($image['imageSmall']) ? $image['imageSmall'] : '';
    $curImageBig = isset($image['imageBig']) ? $image['imageBig'] : '';
    $curTitle = isset($image['title']) ? $image['title'] : '';
?>
    <li>
        <a href="<?php echo htmlspecialchars(\Ip\Config::baseUrl($curImageBig)) ?>" title="<?php echo htmlspecialchars($curTitle); ?>">
            <img src="<?php echo htmlspecialchars(\Ip\Config::baseUrl($curImage)) ?>" alt="<?php echo htmlspecialchars($curTitle); ?>" />
        </a>
    </li>
<?php } ?>
</ul>
<?php } ?>
