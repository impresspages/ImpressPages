<?php if (isset($files) && is_array($files)){ ?>
<ul>
<?php foreach ($files as $fileKey => $file) { ?>
<?php $curFileName = isset($file['fileName']) ? $file['fileName'] : ''; ?>
<?php $curTitle = isset($file['title']) ? $file['title'] : ''; ?>
    <li><a href="<?php echo htmlspecialchars(BASE_URL.$curFileName) ?>"
        title="<?php echo htmlspecialchars($curTitle) ?>"><?php echo htmlspecialchars($curTitle) ?>
    </a>
    </li>
    <?php } ?>
</ul>
    <?php } ?>