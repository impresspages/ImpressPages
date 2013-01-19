<div class="ipModRepositoryPopup">
    <div class="tabs">
        <ul>
            <li><a href="#ipModRepositoryTabUpload">Upload files xxx</a></li>
            <li><a href="#ipModRepositoryTabRecent">Recent files xxx</a></li>
            <li><a href="#ipModRepositoryTabBuy">Buy xxx</a></li>
        </ul>

        <a href="#" class="ipmClose ipaClose ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">close</span></a>

        <div id="ipModRepositoryTabUpload">
            <div id="ipModRepositoryDragContainer">
                <div class="ipmFiles"></div>
                <div class="ipUploadProgressContainer">
                    <div class="ipmCurErrors"></div>
                    <div class="ipmCurUploads"></div>
                    <a href="#" style="z-index: 99100;" class="ipAdminButton ipmBrowseButton" id="ipModRepositoryUploadButton"><?php echo $this->escPar('standard/configuration/system_translations/add_new'); ?></a>
                </div>
                <div class="ipUploadProgressItemSample ipgHide">
                    <div class="ipUploadProgressItem">
                        <div class="ipUploadProgressbar"></div>
                        <p class="ipUploadTitle"></p>
                    </div>
                </div>
                <div class="ipmFile ipmFileSample ipgHide">
                    <a href="#" class="ipaButton ipaFileMove"><?php echo $this->escPar('standard/content_management/widget_file/move') ?></a>
                    <input type="text" class="ipAdminInput ipaRenameTo" name="title" value="" />
                    <a href="#" class="ipaButton ipaFileLink" target="_blank"><?php echo $this->escPar('standard/content_management/widget_file/preview') ?></a>
                    <a href="#" class="ipaButton ipaFileRemove"><?php echo $this->escPar('standard/content_management/widget_file/remove') ?></a>
                </div>
                <p class="ipmErrorSample ipgError ipgHide"></p>
            </div>
            <a class="ipgAdminButton ipgConfirmButton ipaConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm') ?></a>
            <a class="ipgAdminButton ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel') ?></a>

        </div>
        <div id="ipModRepositoryTabRecent">
            <div class="ipmBrowser clearfix">
                <ul class="ipmBrowserContainer">

                </ul>
                <div class="ipgClear"><!-- --></div>
            </div>

            <div class="ipsFileTemplate ipgHide">
                <img class="" src="" alt="file"/>
                <span class="name"></span>
            </div>
            <a class="ipgAdminButton ipgConfirmButton ipaConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm') ?></a>
            <a class="ipgAdminButton ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel') ?></a>
        </div>
        <div id="ipModRepositoryTabBuy">
            <form class="ipaSearchForm">
                <input type="text" class="ipAdminInput ipaSearchTerm" >
                <button type="submit" class="ipAdminButton">Search</button>
            </form>
            <div class="ipmContent ipaContent ipgHide">
                <?php echo \Ip\View::create('buyPagination.php'); ?>
                <ul class="ipmResults ipaResults">
                    <?php /*populated using JS*/ ?>
                </ul>
                <div class="clear"><!-- --></div>
                <?php echo \Ip\View::create('buyPagination.php'); ?>
            </div>

            <!-- search results item template -->
            <ul class="ipgHide">
                <li class="ipaSearchResultItemTemplate">
                    <a href="#" class="ipmThumbnail"></a>
                </li>
            </ul>

            <!-- image detail template -->
            <div class="ipmBuyDialog ipgHide">
                <img src="" alt="Image" />
                <a href="#">Buy XXX</a>
            </div>


        </div>
    </div>
</div>