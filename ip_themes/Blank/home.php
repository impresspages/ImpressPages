<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->subview('_header.php'); ?>
    <div class="col_12">
        <?php echo $this->generateManagedString('homeString1', 'p', __('ImpressPages theme Blank', 'theme-Blank'), 'homeHeadline'); ?>
        <?php echo $this->generateManagedText('homeText1', 'div', '<p style="text-align: center;">' . nl2br(__("ImpressPages is a sexy web content management tool\n with drag&drop and in-place editing.", 'theme-Blank')) . '</p><p style="text-align: center;"><a href="#" class="button">' . __('Start', 'theme-Blank') . '</a></p>', 'homeDescription'); ?>
    </div>
    <?php
    if ($this->getThemeOption('homeBlocks', 0) > 0) {
        echo $this->subview('_homeBlocks.php');
    }
    ?>
    <?php
    if ($this->getThemeOption('homeBlocks2', 0) > 0) {
        echo $this->subview('_homeBlocks2.php');
    }
    ?>
    <div class="main col_12">
        <?php echo ipBlock('main')->exampleContent(' '); ?>
    </div>
    <div class="clear"></div>
<?php echo $this->subview('_footer.php'); ?>
