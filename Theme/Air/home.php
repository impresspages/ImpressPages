<?php echo ipView('_header.php')->render(); ?>
<?php // todox: removed home blocks; loosing examples how front page can be built; 3 columns is now impossible to build ?>
    <div class="main col_12">
        <?php echo ipBlock('main')->exampleContent(' '); ?>
    </div>
    <div class="clear"></div>
<?php echo ipView('_footer.php')->render(); ?>
