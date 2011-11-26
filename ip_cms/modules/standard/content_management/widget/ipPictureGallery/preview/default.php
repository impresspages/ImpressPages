<?php if (isset($pictures) && is_array($pictures)){ ?>
    <ul>
    <?php foreach ($pictures as $pictureKey => $picture) { ?>
        <?php $curPicture = isset($picture['pictureSmall']) ? htmlspecialchars($picture['pictureSmall']) : ''; ?>
        <?php $curTitle = isset($picture['title']) ? htmlspecialchars($picture['title']) : ''; ?>
        <li>
            <a href="" title="<?php echo addslashes($curTitle) ?>">
                <img src="<?php echo addslashes(BASE_URL.$curPicture) ?>" alt="<?php addslashes($curTitle) ?>" />
            </a>
        </li>
    <?php } ?>
    </ul>
<?php } ?>