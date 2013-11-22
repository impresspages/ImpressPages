<div class="ipModulePlugins">
    <?php foreach ($plugins as $plugin){ ?>
        <div class="panel panel-default" data-pluginname="<?php echo ipEsc($plugin['name']) ?>">
            <div class="panel-heading"><?php echo ipEsc($plugin['title']) ?></div>
            <div class="panel-body">
                <p>
                <?php echo ipEsc($plugin['description']) ?>
                </p>
                <?php if ($plugin['active']) { ?>
                    <button type="button" class="ipsDeactivate btn btn-default navbar-btn">{{deactivate}}</button>
                <?php } else { ?>
                    <button type="button" class="ipsActivate btn btn-default navbar-btn">{{activate}}</button>
                    <button type="button" class="ipsRemove btn btn-default navbar-btn">{{remove}}</button>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
