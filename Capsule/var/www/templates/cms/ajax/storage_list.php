<?php use Capsule\Common\TplVar;
use Capsule\Plugin\Storage\Storage;
$instance_name = TplVar::getInstance()->instance_name?>
<div id="<?=$instance_name?>-header-place" class="capsule-ui-storage-overview-header-place">
    <div id="<?=$instance_name?>-header" class="capsule-ui-storage-overview-header storage-list-header">
        <div id="<?=$instance_name?>-header-wrapper" class="capsule-ui-storage-overview-header-wrapper storage-list-header-wrapper">
            <div class="capsule-ui-storage-overview-hcell storage-name">
                <div>test</div>
            </div>
        </div>
    </div>
</div>
<div id="<?=$instance_name?>-body" class="capsule-ui-storage-overview-body">
    <div id="<?=$instance_name?>-body-wrapper" class="capsule-ui-storage-overview-body-wrapper storage-list-body-wrapper">
        <?php foreach (Storage::config() as $k => $v) : ?>
        <div class="capsule-ui-storage-overview-body-row storage-list-row">
            <div title="<?=$v->get('title', $k)?>" class="storage-name"><div><?=$v->get('name', $k)?></div><input type="hidden" value="<?=$k?>"></div>
        </div>
        <?php endforeach ?>
    </div>
</div>
<div id="<?=$instance_name?>-footer" class="capsule-ui-storage-overview-footer">
    footer : /direct/navigation/etc, избранное, последние 100 открытых каталогов например
</div>