<?php if (ipContent()->isManagementState()) { ?>
    <div class="ipsContainer"></div>
<?php } else { ?>
    <?php echo isset($html) ? $html : ''; ?>
<?php } ?>

