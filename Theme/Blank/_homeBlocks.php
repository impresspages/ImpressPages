<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php ob_start(); // Example content ?>
<div class="ipWidget ipPreviewWidget ipWidget-Image ipLayout-default">
    <img src="http://lorempixel.com/373/249/nature/5/" alt="" title="">
</div>
<div class="ipWidget ipPreviewWidget ipWidget-Title ipLayout-level2">
    <h2 class="ipwTitle"><?php _e('This is a level2 headline', 'Blank') ?></h2>
</div>
<div class="ipWidget ipPreviewWidget ipWidget-Text ipLayout-default">
<?php _e('<p>Here is a text block where to can put any information.
It supports <strong>bold</strong>, <em>italics</em>, <span style="text-decoration: underline;">underline</span>, <a href="http://www.impresspages.org">various links</a>
You can make lists:
</p>
<ul>
<li>You add widgets to any block by simply dragging and dropping;</li>
<li>You can paste any content to text widget and it will adapt to your website styles automatically;</li>
<li>And many more.</li>
</ul>', 'Blank'); ?>
</div>
<?php $exampleContent = ob_get_clean(); ?>

<div class="homerow clearfix">

<?php
    switch(ipGetThemeOption('homeBlocks', 0)) {
        case 2:
?>
            <div class="col_12 col_md_6 col_lg_6">
                <?php echo ipBlock('home1')->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_6">
                <?php echo ipBlock('home2')->asStatic()->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
        case 3:
?>
            <div class="col_12 col_md_4 col_lg_4">
                <?php echo ipBlock('home1')->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_4 col_lg_4">
                <?php echo ipBlock('home2')->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_4 col_lg_4">
                <?php echo ipBlock('home3')->asStatic()->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
        case 4:
?>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo ipBlock('home1')->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo ipBlock('home2')->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="clear hidden_md hidden_lg"></div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo ipBlock('home3')->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo ipBlock('home4')->asStatic()->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
    }
?>

</div>
