<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<footer class="clearfix">
    <div class="col_12">
        <?php echo ipSlot('Ip.string', array('id' => 'themeName', 'tag' => 'p', 'default' => _e('Theme "Blank"', 'theme-Blank'), 'css' => 'left')) ?>
        <?php
        $_slogan = sprintf(__('Drag & drop with %s', 'theme-Blank'), '<a href="http://www.impresspages.org">' . __('ImpressPages CMS', 'theme-Blank') . '</a>');
        ?>
        <?php echo ipSlot('Ip.text', array ('id' => 'slogan', 'tag' => 'div', 'default' => $_slogan, 'css' => 'right')); ?>
    </div>
</footer>
</div>
<?php
    ipAddJQuery();
    //TODOX incompatible with new jquery ipAddThemeAsset('colorbox/jquery.colorbox.js');
    //ipAddThemeAsset('site.js');
    ipPrintJavascript();
?>
</body>
</html>
