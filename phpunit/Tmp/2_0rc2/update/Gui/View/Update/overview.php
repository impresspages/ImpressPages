<h1>Overview</h1>
<p>Your current ImpressPages CMS version is <b><?php echo $this->esc($currentVersion) ?></b></p>
<br/>
<p>Update to <b><?php echo $this->esc($destinationVersion) ?></b></p>
<?php foreach ($notes as $key => $note) { ?>
    <hr/>
    <?php echo $note; ?>
<?php } ?>
<a class="button actProceed" href="#">Update</a>