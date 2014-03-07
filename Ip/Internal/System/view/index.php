<div class="ipModuleSystem">
    <?php if (!empty($notes)) { ?>
        <?php foreach ($notes as $note) { ?>
            <div class="note">
                <?php echo $note; ?>
            </div>
        <?php } ?>
    <?php } ?>

    <div class="page-header">
        <h1>ImpressPages CMS <small><?php echo esc($version); ?></small></h1>
    </div>

<!--    <h2>--><?php //_e('Changes in URL detected', 'ipAdmin'); ?><!--</h2>-->
<!--    <p>--><?php //_e(
//            'We have detected that website\s URL has changed. Would you like to update links to new URL?.',
//            'ipAdmin'
//        ) ?><!--</p>-->
<!--    <a href="#" class="ipsUpdateLinks btn btn-primary">--><?php //_e('Update', 'ipAdmin'); ?><!--</a>-->

    <div class="hidden" id="systemInfo">
        <h2><?php _e('System message', 'ipAdmin'); ?></h2>
    </div>
</div>
