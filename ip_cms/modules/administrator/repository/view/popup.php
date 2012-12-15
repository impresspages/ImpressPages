<div class="ipModRepositoryPopup">
    <div class="tabs">
        <ul>
            <li><a href="#ipModRepositoryTabUpload">Upload files</a></li>
            <li><a href="#ipModRepositoryTabRecent">Recent files</a></li>
            <li><a href="#ipModRepositoryTabBuy">Buy</a></li>
        </ul>
        <div id="ipModRepositoryTabUpload">
            <div id="ipModRepositoryDragContainer">
                <div class="ipmFiles"></div>
                <div class="ipUploadProgressContainer">
                    <div class="ipmCurErrors"></div>
                    <div class="ipmCurUploads"></div>
                    <a href="#" style="z-index: 2500;" class="ipAdminButton ipmBrowseButton" id="ipModRepositoryUploadButton"><?php echo $this->escPar('standard/configuration/system_translations/add_new'); ?></a>
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
                <div class="ipmBrowserContainer">

                </div>
                <div class="ipgClear"><!-- --></div>
            </div>

            <img class="ipsFileTemplate ipgHide ipmFile" src="" alt="file"/>
            <a class="ipgAdminButton ipgConfirmButton ipaConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm') ?></a>
            <a class="ipgAdminButton ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel') ?></a>
        </div>
        <div id="ipModRepositoryTabBuy">
            <p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
            <p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
        </div>
    </div>
</div>