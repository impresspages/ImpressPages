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
               <?php echo $this->generateBlock('ipBreadcrumb'); ?>
            </div>
            <?php echo $site->generateBlock('main'); ?>
        </div>
        <div class="side grid_3 left">
            <nav><?php /* add class="collapse" to <nav> to hide second level by default */ ?>
                <?php
                    //first argument is unique name of this menu within your theme. Choose anything you like. Next argument is zone name. They don't have to be equal.
                    echo $this->generateMenu('left', 'menu2');
                    echo $this->generateMenu('left', 'menu3');
                ?>
            </nav>
            <aside>
                <?php echo $this->generateBlock('side', true); ?>
            </aside>
        </div>
        <div class="clear"></div>
<?php echo $this->subview('_foot.php'); ?>
