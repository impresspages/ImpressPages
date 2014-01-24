<div style="float: left; width: 300px;">
    <div class="list-group">
        <a href="#" class="list-group-item ipsTopMenu active" data-tab="ipsStatus">
            <h4 class="list-group-item-heading"><?php _e('Status', 'ipAdmin') ?></h4>
        </a>
        <a href="#" class="list-group-item ipsTopMenu" data-tab="ipsLog">
            <h4 class="list-group-item-heading"><?php _e('Log', 'ipAdmin') ?></h4>
        </a>
        <a href="#" class="list-group-item ipsTopMenu" data-tab="ipsEmail">
            <h4 class="list-group-item-heading"><?php _e('Email queue', 'ipAdmin') ?></h4>
        </a>
    </div>
</div>
<div class="ipsStatus">
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
<div class="ipsLog ipgHide">
    <div class="ipsGrid" data-gateway="{&quot;aa&quot;:&quot;Log.grid&quot;}"></div>
</div>
<div class="ipsEmail ipgHide">
    <div class="ipsGrid" data-gateway="{&quot;aa&quot;:&quot;Email.grid&quot;}"></div>
</div>
