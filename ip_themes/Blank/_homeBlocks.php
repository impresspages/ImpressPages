<?php
$exampleContent = '
<div class="ipWidget ipPreviewWidget ipWidget-IpImage ipLayout-default">
<img src="http://dev.ipcms/file/DSC_0138_10.jpg" alt="" title="">
</div>
<div class="ipWidget ipPreviewWidget ipWidget-IpTitle ipLayout-level2">
<h2 class="ipwTitle">This is a level2 headline</h2>
</div>
<div class="ipWidget ipPreviewWidget ipWidget-IpText ipLayout-default">
<p>Here is a text block where to can put any information. It supports <strong>bold</strong>, <em>italics</em>, <span style="text-decoration: underline;">underline</span>, <a href="http://www.impresspages.org">various links</a>. You can make lists:</p>
<ul>
<li>You add widgets to any block by simply dragging and dropping;</li>
<li>You can paste any content to text widget and it will adapt to your website styles automatically;</li>
<li>And many more.</li>
</ul></div>
';
?>

<div class="homerow clearfix">

<?php
    switch($this->getThemeOption('homeBlocks', 0)) {
        case 2:
?>
            <div class="col_12 col_md_6">
                <?php echo $this->generateBlock('home1', true)->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6">
                <?php echo $this->generateBlock('home2', true)->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
        case 3:
?>
            <div class="col_12 col_md_4">
                <?php echo $this->generateBlock('home1', true)->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_4">
                <?php echo $this->generateBlock('home2', true)->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_4">
                <?php echo $this->generateBlock('home3', true)->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
        case 4:
?>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home1', true)->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home2', true)->exampleContent($exampleContent); ?>
            </div>
            <div class="clear hidden_md hidden_lg"></div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home3', true)->exampleContent($exampleContent); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home4', true)->exampleContent($exampleContent); ?>
            </div>
<?php
            break;
    }
?>

</div>
