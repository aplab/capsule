<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$element = TplVar::getInstance()->element;
?><div class="capsule-ui-oe-el-text">
    <div class="capsule-ui-oe-el-text-title"><?=I18n::_($element->property->title)?>:</div>
    <div class="capsule-ui-oe-el-text-value">
        <div class="capsule-ui-oe-el-text-brdr">
            <input type="text" value="<?=$element->value?>" name="<?=$element->name?>" />
        </div>
    </div>
</div>