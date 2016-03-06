<div
data-maptypeid="<?php echo escAttr($mapTypeId); ?>"
data-zoom="<?php echo escAttr($zoom); ?>"
data-lat="<?php echo escAttr($lat); ?>"
data-lng="<?php echo escAttr($lng); ?>"

<?php if (isset($markerlat)) { ?>
    data-markerlat="<?php echo escAttr($markerlat); ?>"
<?php } ?>

<?php if (isset($markerlng)) { ?>
    data-markerlng="<?php echo escAttr($markerlng); ?>"
<?php } ?>

style="height: <?php echo ($height); ?>;"
data-initialized="0"
class="ipsMap">
</div>
<?php if (ipIsManagementState()) { ?>
    <script>
        if (typeof ipMap !== 'undefined'){
            ipMap.init();
        }
    </script>
<?php } ?>
