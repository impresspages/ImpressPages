<div class="ip ipsModuleRepositoryPopup">
    <div class="ipModuleRepositoryPopup">
        <button type="button" class="ipsClose close">&times;</button>

        <ul class="nav nav-tabs ipsTabs">
            <li class="active"><a href="#ipsModuleRepositoryTabUpload" data-toggle="tab"><?php _e('File repository', 'Ip-admin'); ?></a></li>
            <li class="<?php echo $allowUpload ? '' : 'hidden' ?>"><a href="#ipsModuleRepositoryTabBuy" data-toggle="tab"><?php _e('Buy images', 'Ip-admin'); ?></a></li>
        </ul>

        <div class="tab-content">
            <div id="ipsModuleRepositoryTabUpload" class="tab-pane active _tabUpload">
                <div id="ipsModuleRepositoryDragContainer" class="_upload ipsUpload" >
                    <?php echo ipFilter('ipAdminRepositorySidebar', ipView('popupSide.php', $this->getVariables())->render(), $this->getVariables()) ?>
                </div>
                <div class="_browser ipsBrowser">
                    <div class="_browserControls">
                        <div class="_search">
                            <form class="_form ipsBrowserSearch" action="">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control ipsTerm" placeholder="<?php _e('Search by filename', 'Ip-admin'); ?>">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default ipsSubmit" type="submit"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="_browserContainer ipsBrowserContainer clearfix">
                        <?php if ($allowRepository) { ?>
                            <h2 class="_listTitle _recentTitle ipsListTitle ipsRecentTitle hidden"><?php _e('Recent files', 'Ip-admin'); ?></h2>
                            <ul class="_list clearfix ipsList ipsRecentList hidden"></ul>
                        <?php } else { ?>
                            <h2 class="ipsPermissionError"><?php _e('You have no right to browse the repository', 'Ip-admin') ?></h2>
                        <?php } ?>
                    </div>
                </div>
                <div class="_repositoryActions ipsRepositoryActions hidden">
                    <div class="_container">
                        <span class="_title"><?php _e('Selected:', 'Ip-admin'); ?> <strong class="ipsSelectionCount"></strong></span>
                        <button class="btn btn-primary btn-sm ipsSelectionConfirm"><?php _e('Confirm', 'Ip-admin'); ?></button>
                        <button class="btn btn-default btn-sm ipsSelectionCancel"><?php _e('Cancel', 'Ip-admin'); ?></button>
                        <button class="btn btn-danger btn-sm ipsSelectionDelete"><?php _e('Delete', 'Ip-admin'); ?><i class="fa fa-fw fa-trash-o"></i></button>
                    </div>
                </div>
                <?php // hidden templates for dynamic elements ?>
                <div class="hidden">
                    <h2 class="_listTitle ipsListTitle ipsListTitleTemplate"></h2>
                    <ul class="_list clearfix ipsList ipsListTemplate"></ul>
                    <ul>
                        <li class="ipsFileTemplate">
                            <i class=""></i>
                            <img src="<?php echo ipFileUrl('Ip/Internal/Content/assets/img/empty.gif') ?>" alt="" title="" />
                            <span></span>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="ipsModuleRepositoryTabBuy" class="tab-pane _tabBuy" data-marketurl="<?php echo $marketUrl; ?>">
                <div class="_container" id="ipsModuleRepositoryTabBuyContainer"></div>
                <div class="_loading ipsLoading hidden">
                    <span class="_loadingText">
                        <?php _e('Your images are being downloaded to your website. It may take some time to finish. Please wait.', 'Ip-admin'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
