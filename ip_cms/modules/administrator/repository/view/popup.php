<div class="ipModuleRepositoryPopup">
    <div class="tabs">
        <ul>
            <li><a href="#ipModuleRepositoryTabUpload"><?php echo $this->escPar('administrator/repository/admin_translations/tab_upload') ?></a></li>
            <li><a href="#ipModuleRepositoryTabAll"><?php echo $this->escPar('administrator/repository/admin_translations/tab_files') ?></a></li>
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
                        <a href="#" class="ipAdminButton ipmBrowseButton" id="ipModuleRepositoryUploadButton"><?php echo $this->escPar('standard/configuration/admin_translations/add_new'); ?></a>
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
            <div class="ipmBrowser clearfix">
                <div class="ipmBrowserContainer">
                    <h2 class="ipgHide ipaRecentTitle">{{Recent}}</h2>
                    <ul class="ipgHide ipaRecentList"></ul>
                </div>
                <div class="ipgHide ipmRepositoryActions">
                    <a class="ipAdminButton ipConfirmButton ipaSelectionConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm') ?></a>
                    <a class="ipAdminButton ipaSelectionCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel') ?></a>
                </div>
                <div class="ipgClear"><!-- --></div>
            </div>
            <div class="ipsFileTemplate ipgHide">
                <img class="" src="" alt="file"/>
                <span class="name"></span>
            </div>
            <div class="ipgHide">
                <h2 class="ipsListTitleTemplate"></h2>
                <ul class="ipsListTemplate"></ul>
            </div>
            <a class="ipAdminButton ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel') ?></a>

        </div>
        <div id="ipModuleRepositoryTabAll" class="ipmTabAll">
            <div class="ipmBrowser clearfix">
                <ul class="ipmBrowserContainer">

                </ul>
                <div class="ipgClear"><!-- --></div>
            </div>

            <div class="ipsFileTemplate ipgHide">
                <img class="" src="" alt="file"/>
                <span class="name"></span>
            </div>
            <a class="ipAdminButton ipConfirmButton ipaConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm') ?></a>
            <a class="ipAdminButton ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel') ?></a>
        </div>
        <div id="ipModuleRepositoryTabBuy" data-marketurl='<?php echo $marketUrl ?>' class="ipmTabBuy">
        </div>
    </div>
</div>