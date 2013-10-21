<?php if (isset($files) && is_array($files)) { ?>
<ul>
<?php
    foreach ($files as $fileKey => $file) {
        $curFilePath = isset($file['fileName']) ? $file['fileName'] : '';
        $curFileName = basename($curFilePath);
        $curFileSize = $curFilePath && file_exists(BASE_DIR.$curFilePath) && is_file(BASE_DIR.$curFilePath) ? filesize(BASE_DIR.$curFilePath) : '';
        $curTitle = isset($file['title']) ? $file['title'] : '';
?>
    <li>
        <a href="<?php echo htmlspecialchars(BASE_URL.$curFilePath); ?>" 
           title="<?php echo htmlspecialchars($curFileName) . ($curFileSize ? ' ('.$curFileSize.'b)' : ''); ?>">
            <?php echo htmlspecialchars($curTitle); ?>
        </a>
    </li>
<?php } ?>
</ul>
<?php } ?>
