<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<footer class="clearfix">
    <div class="col_12">
        <?php echo ipSlot('Ip.string', array('id' => 'themeName', 'tag' => 'p', 'default' => __('Theme "Blank"', 'Blank', false), 'class' => 'left')) ?>
        <div>
            <?php echo sprintf(__('Drag & drop with %s', 'Blank'), '<a href="http://www.impresspages.org">' . __('ImpressPages CMS', 'Blank') . '</a>'); ?>
        </div>
    </div>
</footer>
</div>
<?php echo ipJs(); ?>

</body>
</html>