<?php if (ipContent()->isManagementState()) { ?>
    <div class="ipsContainer"><?php echo isset($html) ? $html : ''; ?></div>
<?php } else { ?>
    <?php echo isset($html) ? $html : ''; ?>
<?php } ?>

