<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->subview('_head.php'); ?>
        <div class="main grid_7 right suffix_1">
            <div class="breadcrumb">
               <?php echo $this->generateBlock('home', true); ?>
            </div>
        </div>
        <div>
            <div class="grid_3 left">
                <?php echo $this->generateManagedString('boxTitle1', 'h2', 'Some title'); ?>
                <?php echo $this->generateManagedImage('boxImage1', THEME_DIR.THEME.'/img/header.jpg', array('width' => $this->getThemeOption('boxWidth')), 'boxImage'); ?>
                <?php echo $this->generateManagedText('boxText1', 'div', '<p>Lorem ipsum dolor sit amet</p>'); ?>
            </div>
            <div class="grid_3 left">
                <?php echo $this->generateManagedString('boxTitle2', 'h2', 'Some title'); ?>
                <?php echo $this->generateManagedImage('boxImage2', THEME_DIR.THEME.'/img/header.jpg', array('width' => $this->getThemeOption('boxWidth')), 'boxImage'); ?>
                <?php echo $this->generateManagedText('boxText2', 'div', '<p>Lorem ipsum dolor sit amet</p>'); ?>
            </div>
            <div class="grid_3 left">
                <?php echo $this->generateManagedString('boxTitle3', 'h2', 'Some title'); ?>
                <?php echo $this->generateManagedImage('boxImage3', THEME_DIR.THEME.'/img/header.jpg', array('width' => $this->getThemeOption('boxWidth')), 'boxImage'); ?>
                <?php echo $this->generateManagedText('boxText3', 'div', '<p>Lorem ipsum dolor sit amet</p>'); ?>
            </div>
        </div>
        <div>
            <div class="grid_4 left">
                <?php echo $site->generateBlock('home1', true); ?>
            </div>
            <div class="grid_4 left">
                <?php echo $site->generateBlock('home2', true); ?>
            </div>
        </div>
<?php echo $this->subview('_foot.php'); ?>
