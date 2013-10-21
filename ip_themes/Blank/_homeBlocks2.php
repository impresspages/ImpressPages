<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php
$exampleContent = '
<div class="ipWidget ipPreviewWidget ipWidget-IpTitle ipLayout-level2">
<h2 class="ipwTitle">Heading in the second row</h2>
</div>
<div class="ipWidget ipPreviewWidget ipWidget-IpText ipLayout-default">
<p>You can still add any text with any styles. And at the end of it just put read more link. Don\'t worry about pasting text from your text editor, even if it\'s MS Word or any excerpt from the Internet.</p>
<p><a href="#">Read more &gt;</a></p></div>
';
?>

<div class="homerow clearfix">

<?php
    // making block name dynamic, related with the first row
    $count = (int)$this->getThemeOption('homeBlocks', 0);

    switch($this->getThemeOption('homeBlocks2', 0)) {
        case 2:
?>
            <div class="col_12 col_md_6 col_lg_6">
                <?php echo $this->generateBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_6">
                <?php echo $this->generateBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
        case 3:
?>
            <div class="col_12 col_lg_4 col_lg_4">
                <?php echo $this->generateBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_lg_4 col_lg_4">
                <?php echo $this->generateBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_lg_4 col_lg_4">
                <?php echo $this->generateBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
        case 4:
?>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="clear hidden_md hidden_lg"></div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home'.++$count)->asStatic()->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
    }
?>

</div>
