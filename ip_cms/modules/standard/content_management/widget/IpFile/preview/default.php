<?php if (isset($files) && is_array($files)){ ?>
    <ul>
    <?php foreach ($files as $fileKey => $file) { ?>
        <?php $curFileName = isset($file['fileName']) ? htmlspecialchars($file['fileName']) : ''; ?>
        <?php $curTitle = isset($file['title']) ? htmlspecialchars($file['title']) : ''; ?>
        <li>
            <a href="<?php echo addslashes(BASE_URL.$curFileName) ?>" title="<?php echo addslashes($curTitle) ?>"><?php echo htmlspecialchars($curTitle) ?></a>
        </li>
    <?php } ?>
    </ul>
<?php } ?>