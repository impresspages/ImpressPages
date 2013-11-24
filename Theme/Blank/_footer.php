<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<footer class="clearfix">
    <div class="col_12">
        <?php echo ipSlot('Ip.string', array('id' => 'themeName', 'tag' => 'p', 'default' => _s('Theme "Blank"', 'theme-Blank'), 'class' => 'left')) ?>
        <div>
            <?php echo sprintf(__('Drag & drop with %s', 'theme-Blank'), '<a href="http://www.impresspages.org">' . __('ImpressPages CMS', 'theme-Blank') . '</a>'); ?>
        </div>
    </div>
</footer>
</div>
<?php
    ipAddJQuery();
    //TODOX incompatible with new jquery ipAddThemeAsset('colorbox/jquery.colorbox.js');
    //ipAddThemeAsset('site.js');
    ipPrintJavascript();
?>

<?php
//TODOX remove this test image
echo ipSlot('Ip.image', array(
        'id' => 'test2',
        'default' => 'http://cdn.impresspages.org/Theme/impresspages/img/impresspages_cms_logo.png',
        'width' => 200,
        'height' => 300,
        'class' => 'super'

    )); ?>
</body>
</html>
