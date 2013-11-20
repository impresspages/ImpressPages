<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<footer class="clearfix">
    <div class="col_12">
        <?php echo $this->generateManagedString('themeName', 'p', __('Theme "Blank"', 'theme-Blank'), 'left'); // Todox: too much of escaping ?>
        <?php
        $_slogan = sprintf(__('Drag & drop with %s', 'theme-Blank'), '<a href="http://www.impresspages.org">' . __('ImpressPages CMS', 'theme-Blank') . '</a>');
        ?>
        <?php echo $this->generateManagedText('slogan', 'div', $_slogan, 'right'); ?>
    </div>
</footer>
</div>
<?php
    ipAddJQuery();
    ipAddThemeAsset('colorbox/jquery.colorbox.js');
    ipAddThemeAsset('site.js');
    ipPrintJavascript();
?>
</body>
</html>
