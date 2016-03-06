<?php if (ipIsManagementState()) { ?>
    <div class="ipsContainer"></div>
<?php } else { ?>
    <?php echo isset($html) ? $html : ''; ?>
<?php } ?>
