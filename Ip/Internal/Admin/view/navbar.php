<div class="ip ipsAdminNavbarContainer">
    <div class="ipAdminNavbar ipsAdminNavbar navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation" aria-label="<?php _e('Page editor', 'Ip-admin'); ?>">
        <ul class="_active nav navbar-nav">
            <li class="ipsItemCurrent">
                <a href="<?php echo escAttr(ipActionUrl(array('aa' => 'Pages.index'))); ?>" class="_back">
                    <i class="fa fa-fw fa-angle-double-left" aria-hidden="true"></i>
                    <?php _e('Pages', 'Ip-admin'); ?>
                </a>
            </li>
        </ul>

        <ul class="_right nav navbar-nav navbar-right">
            <?php foreach ($navbarButtons as $button) { ?>
            <li>
                <a
                    href="<?php echo empty($button['url']) ? '#' : escAttr($button['url']); ?>"
                    class="<?php echo empty($button['class']) ? '' : escAttr($button['class']); ?>"
                    title="<?php echo empty($button['hint']) ? '' : escAttr($button['hint']); ?>"
                    <?php if (empty($button['text']) && !empty($button['hint'])) { ?>aria-label="<?php echo escAttr($button['hint']); ?>"<?php } ?>
                >
                    <i class="fa <?php echo empty($button['faIcon']) ? '' : escAttr($button['faIcon']); ?>" aria-hidden="true"></i>
                    <?php echo empty($button['text']) ? '' : $button['text']; ?>
                </a>
            </li>
            <?php } ?>
        </ul>

        <div class="navbar-center">
            <div class="navbar-center-container">
                <?php foreach ($navbarCenterElements as $el) { echo $el; } ?>
            </div>
        </div>
    </div>
</div>
