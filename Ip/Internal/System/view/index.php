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

    <h2><?php _e('Cache', 'ipAdmin'); ?></h2>
    <p><?php _e(
            'Some modules use cache. If you move your site to another location (domain, folder, etc.), you need to clear cache.',
            'ipAdmin'
        ) ?></p>
    <a href="#" class="ipsClearCache btn btn-primary"><?php _e('Clear cache', 'ipAdmin'); ?></a>

    <div class="hidden" id="systemInfo">
        <h2><?php _e('System message', 'ipAdmin'); ?></h2>
    </div>
</div>
