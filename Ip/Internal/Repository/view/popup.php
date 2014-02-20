<div class="ip">
    <div class="ipModuleRepositoryPopup">
        <div class="tabs">
            <ul>
                <li><a href="#ipModuleRepositoryTabUpload"><?php _e('Upload new', 'ipAdmin') ?></a></li>
                <li><a href="#ipModuleRepositoryTabBuy"><?php _e('Buy images', 'ipAdmin') ?></a>
            </ul>

            <a href="#" class="ipmClose ipsClose ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick"></span></a>

            <div id="ipModuleRepositoryTabUpload" class="ipmTabUpload">
                <div id="ipModuleRepositoryDragContainer" class="ipmContainer" >
                    <div class="ipmFiles"></div>
                    <div class="ipUploadProgressContainer">
                        <div class="ipmCurErrors"></div>
                        <div class="ipmBrowseButtonWrapper">
                            <span class="impDragdropNotice"><?php _e('Drag&drop files here or click a button to upload.', 'ipAdmin') ?></span>
                            <a href="#" class="ipAdminButton ipaAction ipmBrowseButton" id="ipModuleRepositoryUploadButton"><?php _e('Add new', 'ipAdmin') ?></a>
                        </div>
                        <div class="ipmBrowseButtonWrapper">
                            <span class="impDragdropNotice"><?php _e('Need more images? Browse and choose from thousands of them.', 'ipAdmin') ?></span>
                            <a href="#ipModuleRepositoryTabBuy" class="ipAdminButton ipaConfirm ipmBrowseButton" id="ipModuleRepositoryBuyButton"><?php _e('Buy images', 'ipAdmin') ?></a>
                        </div>
                    </div>
                    <div class="ipUploadProgressItemSample hidden">
                        <div class="ipUploadProgressItem">
                            <div class="ipUploadProgressbar"></div>
                            <p class="ipUploadTitle"></p>
                        </div>
                    </div>
                    <p class="ipmErrorSample ipgError hidden"></p>
                </div>
                <div class="ipmBrowser">
                    <div class="ipmBrowserControls">
                        <div class="ipmBrowserSearch">
                            <form class="ipmForm" action="">
                                <input type="text" class="ipAdminInput ipmTerm" value="" placeholder="">
                                <button type="submit" class="ipmButton"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="ipmBrowserContainer clearfix">
                        <h2 class="hidden ipmListTitle ipmRecentTitle"><?php _e('Recent files', 'ipAdmin') ?></h2>
                        <ul class="hidden ipmList clearfix ipmRecentList"></ul>
                    </div>
                </div>
                <div class="hidden ipmRepositoryActions">
                    <div class="ipmInner">
                        <span class="ipmTitle"><?php _e('Selected:', 'ipAdmin') ?> <strong class="ipmSelectionCount"></strong></span>
                        <a class="ipAdminButton ipaConfirm ipsSelectionConfirm" href="#"><?php _e('Confirm', 'ipAdmin') ?></a>
                        <a class="ipAdminButton ipsSelectionCancel" href="#"><?php _e('Cancel', 'ipAdmin') ?></a>
                        <a class="ipAdminButton ipsSelectionDelete" href="#"><?php _e('Delete', 'ipAdmin'); ?> <i class="fa fa-trash-o"></i></a>
                    </div>
                </div>
                <?php // hidden templates for dynamic elements ?>
                <div class="hidden">
                    <h2 class="ipmListTitleTemplate ipmListTitle"></h2>
                    <ul class="ipmListTemplate ipmList clearfix"></ul>
                    <ul>
                        <li class="ipmFileTemplate">
                            <i class=""></i>
                            <img src="" alt="" title="" />
                            <span></span>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="ipModuleRepositoryTabBuy" data-marketurl="<?php echo $marketUrl; ?>" class="ipmTabBuy">
                <div class="ipmContainer" id="ipModuleRepositoryTabBuyContainer"></div>
                <div class="hidden ipmLoading">
                    <span class="ipmLoadingText">
                        <?php _e('Your images are being downloaded to your website. It may take some time to finish. Please wait.', 'ipAdmin') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
