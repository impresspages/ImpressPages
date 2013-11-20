<?php if (isset($files) && is_array($files)) { ?>
<ul>
<?php
    foreach ($files as $fileKey => $file) {
        $curFilePath = isset($file['fileName']) ? $file['fileName'] : '';
        $curFileName = basename($curFilePath);
        $curFileSize = $curFilePath && is_file(ipConfig()->baseFile($curFilePath)) ? filesize(ipConfig()->baseFile($curFilePath)) : '';
        $curTitle = isset($file['title']) ? $file['title'] : '';
?>
    <li>
        <a href="<?php echo htmlspecialchars(ipConfig()->baseUrl($curFilePath)) ?>"
           title="<?php echo htmlspecialchars($curFileName) . ($curFileSize ? ' ('.$curFileSize.'b)' : ''); ?>">
            <?php echo htmlspecialchars($curTitle); ?>
        </a>
    </li>
<?php } ?>
</ul>
<?php } ?>
