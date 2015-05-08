<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$element = TplVar::getInstance()->element;
?><div class="capsule-ui-oe-el-tree">
    <div class="capsule-ui-oe-el-tree-title"><?=I18n::_($element->property->title)?>:</div>
    <div class="capsule-ui-oe-el-tree-value">
        <div class="capsule-cms-control-select">
            <div>
                <select name="<?=$element->name?>">
                    <option value="0"></option>
                    <?php if ($element->hasValue) : ?>
                        <?php foreach ($element->options as $value => $optn) : ?>
                            <option value="<?=$value?>"<?=(string)$value===(string)$element->value?' selected="selected"':''?>><?=$optn['text']?></option>
                        <?php endforeach ?>
                    <?php else : ?>
                        <?php foreach ($element->options as $value => $optn) : ?>
                            <option value="<?=$value?>"<?=(string)$value===(string)$element->default?' selected="selected"':''?>><?=$optn['text']?></option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>
            </div>
        </div>
    </div>
</div>