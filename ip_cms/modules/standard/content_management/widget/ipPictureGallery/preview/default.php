<?php if (isset($pictures) && is_array($pictures)){ ?>
<ul>
<?php
foreach ($pictures as $pictureKey => $picture) {
    $curPicture = isset($picture['pictureSmall']) ? $picture['pictureSmall'] : '';
    $curTitle = isset($picture['title']) ? $picture['title'] : '';
?>
    <li>
        <a class="ipwImage" href="<?php echo BASE_URL.IMAGE_DIR.htmlspecialchars($curTitle); ?>">
            <img src="<?php echo htmlspecialchars(BASE_URL.$curPicture); ?>" alt="<?php htmlspecialchars($curTitle); ?>" />
        </a>
    </li>
<?php } ?>
</ul>
<?php } ?>
