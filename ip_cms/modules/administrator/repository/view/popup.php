<div class="ipModuleRepositoryPopup">
    <div class="tabs">
        <ul>
            <li><a href="#ipModuleRepositoryTabUpload"><?php echo $this->escPar('administrator/repository/admin_translations/tab_upload') ?></a></li>
            <li><a href="#ipModuleRepositoryTabBuy">{{Buy images}}</a>
        </ul>

        <a href="#" class="ipmClose ipaClose ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick"></span></a>

        <div id="ipModuleRepositoryTabUpload" class="ipmTabUpload">
            <div id="ipModuleRepositoryDragContainer" class="impContainer" >
                <div class="ipmFiles"></div>
                <div class="ipUploadProgressContainer">
                    <div class="ipmCurErrors"></div>
                    <div class="ipmBrowseButtonWrapper">
                        <span class="impDragdropNotice"><?php echo $this->escPar('administrator/repository/admin_translations/upload_description') ?></span>
                        <a href="#" class="ipAdminButton ipaAction ipmBrowseButton" id="ipModuleRepositoryUploadButton"><?php echo $this->escPar('standard/configuration/admin_translations/add_new'); ?></a>
                    </div>
                    <div class="ipmBrowseButtonWrapper">
                        <span class="impDragdropNotice">{{Need more images? Browse and choose from thousands of them.}}</span>
                        <a href="#ipModuleRepositoryTabBuy" class="ipAdminButton ipaConfirm ipmBrowseButton" id="ipModuleRepositoryBuyButton">{{Buy images}}</a>
                    </div>
                </div>
                <div class="ipUploadProgressItemSample ipgHide">
                    <div class="ipUploadProgressItem">
                        <div class="ipUploadProgressbar"></div>
                        <p class="ipUploadTitle"></p>
                    </div>
                </div>
                <p class="ipmErrorSample ipgError ipgHide"></p>
            </div>
            <div class="ipmBrowser">
                <div class="ipmBrowserContainer clearfix">
                    <h2 class="ipgHide ipmListTitle ipmRecentTitle">{{Recent files}}</h2>
                    <ul class="ipgHide ipmList clearfix ipmRecentList"></ul>
                </div>
            </div>
            <div class="ipgHide ipmRepositoryActions">
                <div class="ipmInner">
                    <span class="ipmTitle">{{Selection}}</span>
                    <a class="ipAdminButton ipaConfirm ipaSelectionConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm') ?></a>
                    <a class="ipAdminButton ipaSelectionCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel') ?></a>
                </div>
            </div>
            <? // hidden templates for dynamic elements ?>
            <div class="ipgHide">
                <h2 class="ipmListTitleTemplate ipmListTitle"></h2>
                <ul class="ipmListTemplate ipmList clearfix"></ul>
                <ul>
                    <li class="ipmFileTemplate">
                        <img rc="" alt="" title="" />
                    </li>
                </ul>
            </div>
        </div>
        <div id="ipModuleRepositoryTabBuy" data-marketurl="<?php echo $marketUrl; ?>" class="ipmTabBuy"></div>
    </div>
</div>