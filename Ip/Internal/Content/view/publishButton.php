<?php
if ($isPublished) {
    $publishClass = 'btn-default';
    $buttonText = __('Published', 'ipAdmin', FALSE);
} else {
    $publishClass = 'btn-primary';
    $buttonText = __('Publish', 'ipAdmin', FALSE);
}
?>
<div class="ipModuleContentPublishButton btn-group">
    <button type="button" class="btn <?php echo $publishClass ?> navbar-btn ipsContentPublish"><?php echo esc($buttonText) ?></button>
    <button type="button" class="btn <?php echo $publishClass ?> navbar-btn dropdown-toggle ipsContentRevisions" data-toggle="dropdown"><i class="fa fa-fw fa-caret-down"></i></button>
    <ul class="_revisions dropdown-menu" role="menu">
        <li class="_button"><button type="button" class="btn btn-default btn-block ipsContentSave"><?php _e('Save now', 'ipAdmin') ?></button></li>
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
