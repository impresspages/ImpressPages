<div
data-maptypeid="<?php echo esc($mapTypeId, 'attr'); ?>"
data-zoom="<?php echo esc($zoom, 'attr'); ?>"
data-lat="<?php echo esc($lat, 'attr'); ?>"
data-lng="<?php echo esc($lng, 'attr'); ?>"

<?php if (isset($markerlat)) { ?>
    data-markerlat="<?php echo esc($markerlat, 'attr'); ?>"
<?php } ?>

<?php if (isset($markerlng)) { ?>
    data-markerlng="<?php echo esc($markerlng, 'attr'); ?>"
<?php } ?>

style="height: <?php echo ($height); ?>; width: <?php echo ($width); ?>;"
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
