<?php if (!$loggedIn) {?>
    <?php if ($passwordResetEnabled) { ?>
        <?php echo $passwordResetForm->render(); ?>
    <?php } ?> 
<?php } ?>