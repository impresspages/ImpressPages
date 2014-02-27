<h<?php echo (int)$level; ?> class="_title"<?php echo isset($id) ? ' id="'.esc($id).'"': ''; ?>>
    <?php if ($showLink) { ?>
        <a href="<?php echo esc($link, 'attr'); ?>"<?php echo !empty($blank) ? ' target="_blank"' : ''; ?>>
    <?php } ?>
        <?php echo !empty($title) ? esc($title) : '&nbsp;'; ?>
    <?php if ($showLink) { ?>
        </a>
    <?php } ?>
</h<?php echo (int)$level; ?>>
