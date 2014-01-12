<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php ob_start(); // Example content ?>
<div class="ipWidget ipPreviewWidget ipWidget-Title ipLayout-level2">
<h2 class="ipwTitle"><?php _e('Heading in the second row', 'Blank') ?></h2>
</div>
<div class="ipWidget ipPreviewWidget ipWidget-Text ipLayout-default">
<p><?php _e('You can still add any text with any styles. And at the end of it just put read more link. Don\'t worry about pasting text from your text editor, even if it\'s MS Word or any excerpt from the Internet.', 'Blank') ?></p>
<p><a href="#"><?php _e('Read more >', 'Blank') ?></a></p></div>
';
<?php $exampleContent = ob_get_clean(); ?>

<div class="homerow clearfix">

<?php
    // making block name dynamic, related with the first row
    $count = (int)ipGetThemeOption('homeBlocks', 0);

    switch(ipGetThemeOption('homeBlocks2', 0)) {
        case 2:
?>
            <div class="col_12 col_md_6 col_lg_6">
                <?php echo ipBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_6">
                <?php echo ipBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
        case 3:
?>
            <div class="col_12 col_lg_4 col_lg_4">
                <?php echo ipBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_lg_4 col_lg_4">
                <?php echo ipBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_lg_4 col_lg_4">
                <?php echo ipBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
        case 4:
?>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo ipBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo ipBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="clear hidden_md hidden_lg"></div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo ipBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo ipBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
    }
?>

</div>
