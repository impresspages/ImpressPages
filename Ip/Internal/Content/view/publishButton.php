<div class="ipModuleContentPublishButton btn-group">
    <button type="button" class="btn btn-primary navbar-btn ipsContentPublish">{{Publish}}</button>
    <button type="button" class="btn btn-primary navbar-btn dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-caret-down"></i></button>
    <ul class="_revisions dropdown-menu" role="menu">
        <li class="_button"><button type="button" class="btn btn-default btn-block ipsContentSave">{{Save now}}</button></li>
        <li class="divider"></li>
        <?php foreach ($revisions as $revisionKey => $revision){
            $revisionClass = '';
            if ($revision['revisionId'] == $currentRevision['revisionId']) {
                $revisionClass .= $revisionClass ? ' ' : '';
                $revisionClass .= 'active';
            }
            ?>
            <li<?php echo $revisionClass ? ' class="'.$revisionClass.'"' : ''; ?>>
                <a href="<?php echo $managementUrls[$revisionKey]; ?>">
                    <strong><?php echo (int)$revision['revisionId']; ?></strong> - <?php echo date("Y-m-d H:i", $revision['createdAt']); echo $revision['isPublished'] ? ' '.esc(__('Published', 'ipAdmin')) . ' ' : ''; ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
