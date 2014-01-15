<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->subview('_header.php'); ?>
    <div class="main col_12">
        <?php echo ipBlock('main')->exampleContent(' '); ?>
    </div>
    <div class="clear"></div>
<?php echo $this->subview('_footer.php'); ?>
