<?php
/** @var $this \Ip\View */
?>

<?php if (!empty($notes)) { ?>
    <?php foreach ($notes as $note) { ?>
        <div class="note">
            <?php echo $note ?>
        </div>
    <?php } ?>
<?php } ?>

<?php
echo $this->subview('header.php')->render();
?>
<div class="content">
    <h1>ImpressPages CMS <?php echo $this->esc($version); ?></h1>
</div>
<div class="content">
    <h1><?php echo $this->escPar('System.cache') ?></h1>
    <?php echo $this->par('System.cache_comments') ?>
    <a class="ipsClearCache button" href="#" ><?php echo $this->escPar('System.cache_clear') ?></a>
    <div class="clear"></div>
    </div>
<div id="systemInfo" class="content" style="display: none;">
    <h1><?php echo $this->escPar('Config.system_message') ?></h1>
</div>
<?php
echo $this->subview('footer.php')->render();
?>

