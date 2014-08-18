<?php use Capsule\Common\TplVar;
use Capsule\Plugin\Storage\Storage;
$instance_name = TplVar::getInstance()->instance_name?>
<div id="<?=$instance_name?>-header-place" class="capsule-ui-storage-overview-header-place">
    <div id="<?=$instance_name?>-header" class="capsule-ui-storage-overview-header storage-contents-header">
        <div id="<?=$instance_name?>-header-wrapper" class="capsule-ui-storage-overview-header-wrapper storage-contents-header-wrapper">
            <div class="capsule-ui-storage-overview-hcell storage-name">
                <div>test</div>
            </div>
        </div>
    </div>
</div>
<div id="<?=$instance_name?>-body" class="capsule-ui-storage-overview-body">
    <div id="<?=$instance_name?>-body-wrapper" class="capsule-ui-storage-overview-body-wrapper storage-contents-body-wrapper">
        <?php foreach (TplVar::getInstance()->list as $k => $v) : ?>
        <div class="capsule-ui-storage-overview-body-row storage-contents-row">
            <div title="<?=$v->getFilename()?>" class="storage-name"><div><?=$v->getFilename()?></div><input type="hidden" value="<?=$k?>"></div>
        </div>
        <?php endforeach ?>
    </div>
</div>
<div id="<?=$instance_name?>-footer" class="capsule-ui-storage-overview-footer">
    footer : /direct/navigation/etc, избранное, последние 100 открытых каталогов например
</div>