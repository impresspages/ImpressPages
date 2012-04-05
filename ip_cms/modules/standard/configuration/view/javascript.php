<?php foreach ($javascript as $levelKey => $level) { ?>
    <?php foreach ($level as $fileKey => $record) { ?>
        <?php if ($record['type'] == 'file') { ?>
            <script type="text/javascript" src="<?php echo $record['value'] ?>"></script>
        <?php } ?>
        <?php if ($record['type'] == 'content') { ?>
            <script type="text/javascript">
        <?php echo $record['value']; ?>
            </script>
        <?php } ?>
    <?php } ?>
<?php } ?>
