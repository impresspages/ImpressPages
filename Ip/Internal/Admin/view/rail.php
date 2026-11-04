<?php
/**
 * Level 1 navigation: icon rail on the left, labels slide out on hover or keyboard focus.
 *
 * @var array $groups from \Ip\Internal\Admin\Navigation::groups()
 * @var string $siteName
 */
?>
<nav class="ipAdminRail ipsAdminRail" aria-label="<?php _e('Administration', 'Ip-admin'); ?>">
    <div class="_inner">
        <a class="_brand" href="<?php echo escAttr(ipActionUrl(array('aa' => 'Pages.index'))); ?>">
            <span class="_mark" aria-hidden="true">iP</span>
            <span class="_label"><?php echo esc($siteName); ?></span>
        </a>
        <ul class="_items">
            <?php foreach ($groups as $group) { ?>
                <li>
                    <a class="_link" href="<?php echo escAttr($group['url']); ?>"<?php echo $group['current'] ? ' aria-current="page"' : ''; ?> title="<?php echo escAttr($group['title']); ?>">
                        <i class="fa <?php echo escAttr($group['icon']); ?>" aria-hidden="true"></i>
                        <span class="_label"><?php echo esc($group['title']); ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
        <ul class="_items _bottom">
            <li>
                <a class="_link" href="<?php echo escAttr(ipHomeUrl()); ?>" title="<?php _e('View site', 'Ip-admin'); ?>">
                    <i class="fa fa-external-link" aria-hidden="true"></i>
                    <span class="_label"><?php _e('View site', 'Ip-admin'); ?></span>
                </a>
            </li>
            <li class="_separator" aria-hidden="true"></li>
            <li>
                <a class="_link _logout ipsAdminLogout" href="<?php echo escAttr(ipActionUrl(array('sa' => 'Admin.logout'))); ?>" title="<?php _e('Log out', 'Ip-admin'); ?>">
                    <i class="fa fa-power-off" aria-hidden="true"></i>
                    <span class="_label"><?php _e('Log out', 'Ip-admin'); ?></span>
                </a>
            </li>
        </ul>
    </div>
</nav>
