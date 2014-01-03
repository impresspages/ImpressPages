<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->subview('_header.php'); ?>
    <?php
    if (ipGetThemeOption('homeBlocks', 0) > 0) {
        echo $this->subview('_homeBlocks.php');
    }
    ?>
    <?php
    if (ipGetThemeOption('homeBlocks2', 0) > 0) {
        echo $this->subview('_homeBlocks2.php');
    }
    ?>
    <div class="main col_12">
        <?php echo ipBlock('main')->exampleContent(' '); ?>
    </div>
    <div class="clear"></div>
<?php echo $this->subview('_footer.php'); ?>
