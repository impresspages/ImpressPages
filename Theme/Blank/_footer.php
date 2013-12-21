<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<footer class="clearfix">
    <div class="col_12">
        <?php echo ipSlot('Ip.string', array('id' => 'themeName', 'tag' => 'p', 'default' => __('Theme "Blank"', 'theme-Blank', false), 'class' => 'left')) ?>
        <div>
            <?php echo sprintf(__('Drag & drop with %s', 'theme-Blank'), '<a href="http://www.impresspages.org">' . __('ImpressPages CMS', 'theme-Blank') . '</a>'); ?>
        </div>
    </div>
</footer>
</div>
<?php echo ipJs(); ?>

</body>
</html>