<div class="homerow clearfix">

<?php
    // making block name dynamic, related with the first row
    $count = (int)$this->getThemeOption('homeBlocks', 0);

    switch($this->getThemeOption('homeBlocks2', 0)) {
        case 2:
?>
            <div class="col_12 col_md_6">
                <?php echo $this->generateBlock('home'.++$count, true)->exampleContent('<pre>Hello</pre>'); // TODOX: add example content everywhere ?>
            </div>
            <div class="col_12 col_md_6">
                <?php echo $this->generateBlock('home'.++$count, true)->exampleContent('<pre>Hello</pre>'); ?>
            </div>
<?php
            break;
        case 3:
?>
            <div class="col_12 col_lg_4">
                <?php echo $this->generateBlock('home'.++$count, true)->exampleContent('<pre>Hello</pre>'); ?>
            </div>
            <div class="col_12 col_lg_4">
                <?php echo $this->generateBlock('home'.++$count, true)->exampleContent('<pre>Hello</pre>'); ?>
            </div>
            <div class="col_12 col_lg_4">
                <?php echo $this->generateBlock('home'.++$count, true)->exampleContent('<pre>Hello</pre>'); ?>
            </div>
<?php
            break;
        case 4:
?>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home'.++$count, true)->exampleContent('<pre>Hello</pre>'); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home'.++$count, true)->exampleContent('<pre>Hello</pre>'); ?>
            </div>
            <div class="clear hidden_md hidden_lg"></div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home'.++$count, true)->exampleContent('<pre>Hello</pre>'); ?>
            </div>
            <div class="col_12 col_md_6 col_lg_3">
                <?php echo $this->generateBlock('home'.++$count, true)->exampleContent('<pre>Hello</pre>'); ?>
            </div>
<?php
            break;
    }
?>

</div>
