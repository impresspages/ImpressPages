<footer class="clearfix">
    <div class="col_12">
        <?php echo ipSlot('text', array('id' => 'themeName', 'tag' => 'div', 'default' => __('Theme "Air"', 'Air', false), 'class' => 'left')); ?>
        <div class="right">
            <?php echo sprintf(__('Drag & drop with %s', 'Air'), '<a href="http://www.impresspages.org">ImpressPages</a>'); ?>
        </div>
    </div>
</footer>
</div>
<?php echo ipAddJs('assets/site.js'); ?>
<?php echo ipJs(); ?>

</body>
</html>
