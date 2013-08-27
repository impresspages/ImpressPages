<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->subview('_header.php'); ?>
        <div>
            <div class="grid_4 left">
                <?php echo $this->generateManagedString('boxTitle1', 'h2', 'Some title'); ?>
                <?php echo $this->generateManagedImage('boxImage1', THEME_DIR.THEME.'/img/image1.png', array('width' => $this->getThemeOption('boxWidth'), 'height' => '175'), 'boxImage'); ?>
                <?php echo $this->generateManagedText('boxText1', 'div', '<p>Lorem ipsum dolor sit amet</p>'); ?>
            </div>
            <div class="grid_4 left">
                <?php echo $this->generateManagedString('boxTitle2', 'h2', 'Some title'); ?>
                <?php echo $this->generateManagedImage('boxImage2', THEME_DIR.THEME.'/img/image2.png', array('width' => $this->getThemeOption('boxWidth'), 'height' => '175'), 'boxImage'); ?>
                <?php echo $this->generateManagedText('boxText2', 'div', '<p>Lorem ipsum dolor sit amet</p>'); ?>
            </div>
            <div class="grid_4 left">
                <?php echo $this->generateManagedString('boxTitle3', 'h2', 'Some title'); ?>
                <?php echo $this->generateManagedImage('boxImage3', THEME_DIR.THEME.'/img/image3.png', array('width' => $this->getThemeOption('boxWidth'), 'height' => '175'), 'boxImage'); ?>
                <?php echo $this->generateManagedText('boxText3', 'div', '<p>Lorem ipsum dolor sit amet</p>'); ?>
            </div>
            <div class="clear"></div>
        </div>
        <div>
            <div class="grid_6 left">
                <?php echo $site->generateBlock('home1', true); ?>
            </div>
            <div class="grid_6 left">
                <?php echo $site->generateBlock('home2', true); ?>
            </div>
            <div class="clear"></div>
        </div>
<div class="clear"></div>
<?php echo $this->subview('_footer.php'); ?>
