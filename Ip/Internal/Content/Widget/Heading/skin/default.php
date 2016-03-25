<h<?php echo (int)$level; ?> class="_title"<?php echo !empty($anchor) ? ' id="' . esc($anchor) . '"': ''; ?>>
    <?php if ($showLink) { ?>
        <a href="<?php echo escAttr($link); ?>"<?php echo !empty($blank) ? ' target="_blank"' : ''; ?>>
    <?php } ?>
        <?php echo !empty($title) ? $title : '&nbsp;'; ?>
    <?php if ($showLink) { ?>
        </a>
    <?php } ?>
</h<?php echo (int)$level; ?>>
