<?php if (!empty($link) && !ipIsManagementState()) { ?>
    <a href="<?php echo esc($link, 'attr') ?>" <?php echo !empty($blank) ? 'target="_blank"' : '' ?>
<?php } ?>
    <h<?php echo (int)$level ?> <?php echo isset($id) ? 'id="'.esc($id).'"': '' ?> class="ipwTitle"><?php echo !empty($title) ? esc($title) : '&nbsp;'; ?></h<?php echo (int)$level ?>>
<?php if (!empty($link)) { ?>
    </a>
<?php } ?>
