<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->subview('_header.php'); ?>
    <div class="col_12">
        <?php ipSlot('Ip.string', array('id' => 'homeString1', 'tag' => 'p', 'default' => _e('ImpressPages theme Blank', 'theme-Blank'), 'class' => 'homeHeadline')); ?>
        <?php ipSlot('Ip.text', array('id' => 'homeText1', 'tag' => 'div', 'default' => '<p style="text-align: center;">' . nl2br(__("ImpressPages is a sexy web content management tool\n with drag&drop and in-place editing.", 'theme-Blank')) . '</p><p style="text-align: center;"><a href="#" class="button">' . __('Start', 'theme-Blank') . '</a></p>', 'class' => 'homeDescription')); ?>
    </div>
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
