<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->subview('_header.php'); ?>

<div>
    <?php echo $this->subview('_homeBlock'.$this->getThemeOption('homeContentBlocks', 0).'.php')->render() ?>
    <div class="clear"></div>
</div>
<div class="main grid_12">
    <?php echo $this->generateBlock('main'); ?>
</div>

<?php echo $this->subview('_footer.php'); ?>
