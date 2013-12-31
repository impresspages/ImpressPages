<div style="float: left; width: 300px;">
    <div class="list-group">
        <a href="#" class="list-group-item active">
            <h4 class="list-group-item-heading">List group item heading</h4>
            <p class="list-group-item-text">...</p>
        </a>
    </div>
</div>
<div>
    <?php if (!empty($notes)) { ?>
        <?php foreach ($notes as $note) { ?>
            <div class="note">
                <?php echo $note ?>
            </div>
        <?php } ?>
    <?php } ?>

    <div class="content">
        <h1>ImpressPages CMS <?php echo esc($version); ?></h1>
    </div>
    <div class="content">
        <h1><?php _e('Cache', 'ipAdmin') ?></h1>

        <p><?php _e(
                'Some modules use cache. If you move your site to another location (domain, folder, etc.), you need to clear cache.',
                'ipAdmin'
            ) ?></p>
        <a class="ipsClearCache button" href="#"><?php _e('Clear cache', 'ipAdmin') ?></a>

        <div class="clear"></div>
    </div>
    <div id="systemInfo" class="content" style="display: none;">
        <h1><?php _e('System message', 'ipAdmin') ?></h1>
    </div>
</div>
<div>
    <div class="ip">
        <div class="ipsGrid" data-gateway="{&quot;aa&quot;:&quot;Log.grid&quot;}"></div>
    </div>
</div>
<div>
    <div class="ip">
        <div class="ipsGrid" data-gateway="{&quot;aa&quot;:&quot;Email.grid&quot;}"></div>
    </div>
</div>


