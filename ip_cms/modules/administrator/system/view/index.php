<?php
/** @var $this \Ip\View */
$this;
?>
<?php
echo $this->subview('header.php');
?>
<div class="content">
    <h1>ImpressPages CMS <?php echo $this->esc($version); ?></h1>
</div>
<div class="content">
    <h1><?php echo $this->escPar('administrator/system/admin_translations/cache') ?></h1>
    <?php echo $this->par('administrator/system/admin_translations/cache_comments') ?>
    <a class="ipsClearCache button" href="#" ><?php echo $this->escPar('administrator/system/admin_translations/cache_clear') ?></a>
    <div class="clear"></div>
    </div>
<div id="systemInfo" class="content" style="display: none;">
    <h1><?php echo $this->escPar('standard/configuration/system_translations/system_message') ?></h1>
    <?php if ($enableUpdate){ ?>
        <script type="text/javascript" src="<?php echo BASE_URL.MODULE_DIR  ?>administrator/system/public/update.js"></script>
    <?php } ?>
    <script type="text/javascript" src="<?php echo BASE_URL.MODULE_DIR ?>administrator/system/public/clearCache.js"></script>
    <?php echo $site->generateJavascript(); ?>
</div>
<?php
echo $this->subview('footer.php');
?>

