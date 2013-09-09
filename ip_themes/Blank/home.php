<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->subview('_header.php'); ?>
    <div class="col_12">
        <?php echo $this->generateManagedString('homeString1', 'p', 'ImpressPages theme Blank', 'homeString'); ?>
        <?php echo $this->generateManagedText('homeText1', 'div', '<p>ImpressPages is a sexy web content management tool<br /> with drag&drop and in-place editing.</p><p><a href="#">Start</a></p>', 'homeText'); ?>
    </div>
    <div class="homerow clearfix">
        <div class="col_12 col_lg_4">
            <?php echo $site->generateBlock('home1', true)->exampleContent('<pre>Hello</pre>'); // TODOX: add example content everywhere ?>
        </div>
        <div class="col_12 col_md_6 col_lg_4">
            <?php echo $site->generateBlock('home2', true)->exampleContent('<pre>Hello</pre>'); ?>
        </div>
        <div class="col_12 col_md_6 col_lg_4">
            <?php echo $site->generateBlock('home3', true)->exampleContent('<pre>Hello</pre>'); ?>
        </div>
    </div>
    <div class="homerow clearfix">
        <div class="col_12">
            <?php echo $site->generateBlock('home4', true)->exampleContent('<pre>Hello</pre>'); ?>
        </div>
    </div>
    <div class="homerow clearfix">
        <div class="col_6">
            <?php echo $site->generateBlock('home5', true)->exampleContent('<pre>Hello</pre>'); ?>
        </div>
        <div class="col_6">
            <?php echo $site->generateBlock('home6', true)->exampleContent('<pre>Hello</pre>'); ?>
        </div>
    </div>
    <div class="homerow clearfix">
        <div class="col_3">
            <?php echo $site->generateBlock('home7', true)->exampleContent('<pre>Hello</pre>'); ?>
        </div>
        <div class="col_3">
            <?php echo $site->generateBlock('home8', true)->exampleContent('<pre>Hello</pre>'); ?>
        </div>
        <div class="col_3">
            <?php echo $site->generateBlock('home9', true)->exampleContent('<pre>Hello</pre>'); ?>
        </div>
        <div class="col_3">
            <?php echo $site->generateBlock('home10', true)->exampleContent('<pre>Hello</pre>'); ?>
        </div>
    </div>
    <div class="main col_12">
        <?php echo $site->generateBlock('main'); ?>
    </div>
    <div class="clear"></div>
<?php echo $this->subview('_footer.php'); ?>
