<?php if (isset($pictures) && is_array($pictures)){ ?>
<ul>
<?php foreach ($pictures as $pictureKey => $picture) { ?>
<?php $curPicture = isset($picture['pictureSmall']) ? $picture['pictureSmall'] : ''; ?>
<?php $curTitle = isset($picture['title']) ? $picture['title'] : ''; ?>
    <li><a href="" title="<?php echo htmlspecialchars($curTitle) ?>"> <img
            src="<?php echo htmlspecialchars(BASE_URL.$curPicture) ?>"
            alt="<?php htmlspecialchars($curTitle) ?>" /> </a>
    </li>
    <?php } ?>
</ul>
    <?php } ?>